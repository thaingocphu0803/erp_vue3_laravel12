<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import AppBreadcrumb from '@/components/layout/AppBreadcrumb.vue'
import BaseSearchBtn from '@/components/BaseSearchBtn.vue'
import ListFilter from '@/components/list/ListFilter.vue'
import CONFIG from '@/config/constants'
import { useTableModule } from '@/composables/useTableModule'
import { useRoute } from 'vue-router'
import type { EmployeeFilterParams } from '@/types/employee'
import type { commonStatus } from '@/types/common'
import { useDepartmentStore } from '@/stores/department'
import { usePositionStore } from '@/stores/position'
import { storeToRefs } from 'pinia'
import { debounce } from 'vuetify/lib/util/helpers.mjs'
import { useRouteQuery } from '@/composables/useRouteQuery'
import RetryBtn from '@/components/RetryBtn.vue'
import ThrottleAlert from '@/components/ThrottleAlert.vue'
import { useThrottleStore } from '@/stores/throttle'
import SYSTEM from '@/config/system'
import { formatLaravelRetryAfter } from '@/utils/errorHandler'
import { useToastStore } from '@/stores/toast'
import { useEmployeeStore } from '@/stores/employee'

const route = useRoute()

// toast store
const toast = useToastStore()

// department store
const { departments } = storeToRefs(useDepartmentStore())
const { departmentsFetch } = useDepartmentStore()

// position store
const { positions } = storeToRefs(usePositionStore())
const { positionFetch } = usePositionStore()

// throttle store
const { throttle, isDisabled } = storeToRefs(useThrottleStore())
const { initThrottle, startThrottle } = useThrottleStore()

// employee store
const { employeePaginate } = useEmployeeStore()
const { employeeIndex } = storeToRefs(useEmployeeStore())

// loading index
const loading = ref<boolean>(false)

// error retrieve index
const isError = ref<boolean>(false)

// error message retrieve index
const errorMessage = ref<string>('')

// selected employee ids
const selectedEmployeeIds = ref<number[]>([])

// filter params
const filterParams = ref<EmployeeFilterParams>({
	status: route.query.status as commonStatus | null,
	department_id: route.query.department ? Number(route.query.department) : null,
	position_id: route.query.position ? Number(route.query.position) : null,
	search: route.query.search as string | '',
	itemsPerPage: Number(route.query.itemsPerPage) | CONFIG.itemPerPage,
	page: Number(route.query.page) | CONFIG.page,
	sortKey: null,
	sortOrder: null,
})

// total item length
const totalItemLength = ref<number>(0)

// total page
const totalPage = ref<number>(0)

// update query params
const { updateQueryParams, replaceQueryParams } = useRouteQuery()


const { employeeHeaders } = useTableModule()

// handle update search value
const handleUpdateSearchValue = debounce((val: string) => {
	filterParams.value.search = val
}, CONFIG.debounceTimeout)

// reset url to default
const resetURLToDefault = () => {
	filterParams.value.page = CONFIG.page
	filterParams.value.itemsPerPage = CONFIG.itemPerPage
	filterParams.value.search = ''
	filterParams.value.status = null
	filterParams.value.sortKey = null
	filterParams.value.sortOrder = null

	replaceQueryParams({
		page: filterParams.value.page,
		itemsPerPage: filterParams.value.itemsPerPage,
	})
}

// watch filter status change
watch(
	[
		() => filterParams.value.status,
		() => filterParams.value.department_id,
		() => filterParams.value.position_id
	],
	async () => {
		let isPageChanged = false

		if (filterParams.value.page !== CONFIG.page) {
			filterParams.value.page = CONFIG.page
			isPageChanged = true
		}

		updateQueryParams(filterParams.value)

		if (!isPageChanged) {
			await fetchEmployeeIndex()
		}
	},
)

// handle employee pagination
const handleEmployeePaginate = async (options: any) => {
	const { sortBy, itemsPerPage: newItemsPerPage, page: newPage, search: newSearch } = options

	filterParams.value.page = newPage
	filterParams.value.search = newSearch
	filterParams.value.itemsPerPage = newItemsPerPage
	filterParams.value.sortKey = sortBy.length ? sortBy[0].key : undefined
	filterParams.value.sortOrder = sortBy.length ? sortBy[0].order : undefined

	updateQueryParams(filterParams.value)

	await fetchEmployeeIndex()
}

// fetch employee index
const fetchEmployeeIndex = async () => {
	try {
		loading.value = true
		const response = await employeePaginate(filterParams.value)

		isError.value = false
		totalItemLength.value = response?.data?.meta.total
		totalPage.value = response?.data?.meta.last_page
	} catch (error: any) {
		isError.value = true

		// handle unprocessable entity error
		if (error.status === SYSTEM.SERVER_ERROR.UNPROCESSABLE_ENTITY) {
			errorMessage.value = error.response.data.messageCode
			toast.show(errorMessage.value, 'error')
			resetURLToDefault()
		}

		// handle too many request error
		if (error.status === SYSTEM.SERVER_ERROR.TOO_MANY_REQUESTS) {
			throttle.value['employeePaginate'] = formatLaravelRetryAfter(error)
			startThrottle('employeePaginate')
		}

		// handle internal server error
		if (error.status === SYSTEM.SERVER_ERROR.INTERNAL_SERVER_ERROR) {
			errorMessage.value = 'common.error.fetchDataFailed'
			toast.show(errorMessage.value, 'error')
		}
	} finally {
		loading.value = false
	}
}

// loading department in filter
const loadingDepartment = ref<boolean>(false)

// error message for department filter
const errorMessageGetDepartmentList = ref<string>('')

const isErrorGetDepartmentList = ref<boolean>(false)

// get department list
const getDepartmentList = async () => {
	if (isDisabled.value('departmentFetch:index')) return

	try {
		loadingDepartment.value = true
		await departmentsFetch()
		isErrorGetDepartmentList.value = false
		errorMessageGetDepartmentList.value = ''
	} catch (error: any) {
		isErrorGetDepartmentList.value = true

		errorMessageGetDepartmentList.value = 'common.error.fetchDataFailed'

		if (error.status === SYSTEM.SERVER_ERROR.TOO_MANY_REQUESTS) {
			throttle.value['departmentFetch:index'] = formatLaravelRetryAfter(error)
			startThrottle('departmentFetch:index')
		}

	} finally {
		loadingDepartment.value = false
	}
}

// loading position in filter
const loadingPosition = ref<boolean>(false)

// error message for position filter
const errorMessageGetPositionList = ref<string>('')

const isErrorGetPositionList = ref<boolean>(false)

// get position list
const getPositionList = async () => {
	if (isDisabled.value('positionFetch:index')) return

	try {
		loadingPosition.value = true
		await positionFetch()
		isErrorGetPositionList.value = false
		errorMessageGetPositionList.value = ''
	} catch (error: any) {
		isErrorGetPositionList.value = true

		errorMessageGetPositionList.value = 'common.error.fetchDataFailed'

		if (error.status === SYSTEM.SERVER_ERROR.TOO_MANY_REQUESTS) {
			throttle.value['positionFetch:index'] = formatLaravelRetryAfter(error)
			startThrottle('positionFetch:index')
		}

	} finally {
		loadingPosition.value = false
	}
}

// on mounted
onMounted(async () => {
	initThrottle('employeePaginate')
	initThrottle('departmentFetch:index')
	initThrottle('positionFetch:index')

	if (isDisabled.value('departmentFetch:index')) {
		isErrorGetDepartmentList.value = true
	}

	if (isDisabled.value('positionFetch:index')) {
		isErrorGetPositionList.value = true
	}
})

</script>

<template>
	<app-breadcrumb class="mb-2" />

	<v-container :fluid="true" class="employee-list">
		<list-header title="common.header.listEmployee">
			<template v-slot:prepend>
				<v-btn color="primary" class="text-none" :to="{ name: 'hr.employee.create' }">
					<v-icon icon="mdi-plus"></v-icon>
					<span class="d-none d-sm-inline ml-sm-2">{{
						$t('common.button.addEmployee')
					}}</span>
				</v-btn>
			</template>
		</list-header>

		<v-card class="elevation-1 mb-4">
			<v-card-text>
				<v-row dense>
					<v-col cols="12" sm="4" lg="3">
						<base-search-btn :label="$t('common.filter.nameOrCode')"
							@update:model-value="handleUpdateSearchValue">
						</base-search-btn>
					</v-col>

					<v-col cols="12" sm="4" lg="3">
						<list-filter v-model="filterParams.department_id" :items="departments" item-title="name"
							item-value="id" :label="$t('common.filter.department')"
							:error-messages="isDisabled('departmentFetch:index') ? '' : errorMessageGetDepartmentList"
							:loading="loadingDepartment" @click.stop="getDepartmentList" searchable>
							<template #append-inner v-if="isErrorGetDepartmentList">
								<retry-btn only-icon :disabled="isDisabled('departmentFetch:index')"
									@click.stop="getDepartmentList"></retry-btn>
							</template>
						</list-filter>
						<throttle-alert :show="isDisabled('departmentFetch:index')"
							:time="throttle['departmentFetch:index'] || 0"></throttle-alert>
					</v-col>

					<v-col cols="12" sm="4" lg="3">
						<list-filter v-model="filterParams.position_id" :items="positions" item-title="name"
							item-value="id" :label="$t('common.filter.position')"
							:error-messages="isDisabled('positionFetch:index') ? '' : errorMessageGetPositionList"
							:loading="loadingDepartment" @click.stop="getPositionList" searchable>
							<template #append-inner v-if="isErrorGetPositionList">
								<retry-btn only-icon :disabled="isDisabled('positionFetch:index')"
									@click.stop="getPositionList"></retry-btn>
							</template>
						</list-filter>
						<throttle-alert :show="isDisabled('positionFetch:index')"
							:time="throttle['positionFetch:index'] || 0"></throttle-alert>
					</v-col>

				</v-row>
			</v-card-text>
		</v-card>

		<!-- data table -->
		<v-card class="elevation-1">
			<!-- error state -->
			<v-col cols="12" align="center" justify="center" v-show="isError">
				<div class="text-body-1 text-grey-darken-1 font-weight-medium mb-5">
					{{ errorMessage.length ? $t(errorMessage) : '' }}
				</div>

				<!-- retry btn -->
				<retry-btn :disabled="isDisabled('employeePaginate')" :loading="loading"
					@click.stop="fetchEmployeeIndex"></retry-btn>

				<!-- throttle alert -->
				<throttle-alert :show="isDisabled('employeePaginate')"
					:time="throttle['employeePaginate'] || 0"></throttle-alert>
			</v-col>
			<!-- data table -->
			<v-data-table-server v-show="!isError" :page="filterParams.page" :headers="employeeHeaders"
				:items="employeeIndex" :items-per-page="filterParams.itemsPerPage" item-value="id"
				:items-length="totalItemLength" :search="filterParams.search" v-model="selectedEmployeeIds"
				@update:options="handleEmployeePaginate">

				<!-- avatar -->
				<template v-slot:item.avatar="{ item }">
					<v-avatar :image="item.avatar ?? CONFIG.avatar" size="42" density="compact"></v-avatar>
				</template>

				<!-- name -->
				<template v-slot:item.name="{ item }">
					<v-btn density="compact" variant="plain" color="primary" class="text-none text-truncate">{{
						item.name
					}}</v-btn>
				</template>

				<!-- status -->
				<template v-slot:item.status="{ value }">
					<base-status-chip :val="value"></base-status-chip>
				</template>

				<template v-slot:bottom>
					<v-divider></v-divider>
					<div class="d-flex justify-center justify-sm-space-between align-center pa-4">
						<list-filter class="d-none d-sm-block" v-model="filterParams.itemsPerPage"
							:items="CONFIG.perPage" :label="$t('common.filter.itemPerPage')" max-width="200"
							min-width="200" :clearable="false" @update:model-value="fetchEmployeeIndex"></list-filter>

						<v-pagination v-if="totalPage > 1" v-model="filterParams.page" :length="totalPage"
							:total-visible="CONFIG.pageVisible" rounded="shape" density="comfortable"
							@update:model-value="fetchEmployeeIndex"></v-pagination>
					</div>
				</template>
			</v-data-table-server>
		</v-card>
	</v-container>
</template>
