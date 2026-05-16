<script setup lang="ts">
import { ref, watch, onMounted, computed } from 'vue'
import AppBreadcrumb from '@/components/layout/AppBreadcrumb.vue'
import ListHeader from '@/components/list/ListHeader.vue'
import BaseSearchBtn from '@/components/BaseSearchBtn.vue'
import ListFilter from '@/components/list/ListFilter.vue'
import CONFIG from '@/config/constants'
import { useRoute } from 'vue-router'
import { debounce } from 'vuetify/lib/util/helpers.mjs'
import { useRouteQuery } from '@/composables/useRouteQuery'
import { useFilterModule } from '@/composables/useFilterModule'
import { useTableModule } from '@/composables/useTableModule'
import BaseStatusChip from '@/components/BaseStatusChip.vue'
import { useToastStore } from '@/stores/toast'
import type { bulkActionStatus, commonStatus } from '@/types/common'
import SYSTEM from '@/config/system'
import { usePositionStore } from '@/stores/position'
import { storeToRefs } from 'pinia'
import { useThrottleStore } from '@/stores/throttle'
import type { PositionFilterParams } from '@/types/position'
import { formatLaravelRetryAfter } from '@/utils/errorHandler'
import BaseBtn from '@/components/BaseBtn.vue'
import RetryBtn from '@/components/RetryBtn.vue'
import ThrottleAlert from '@/components/ThrottleAlert.vue'
import ListBulkAction from '@/components/list/ListBulkAction.vue'
import { useDepartmentStore } from '@/stores/department'
import type { Department } from '@/types/department'
import { t } from '@/plugins/vueI18n'

// route
const route = useRoute()

// toast store
const toast = useToastStore()

// position store
const { positionPaginate, positionBulkUpdateStatus, positionBulkDelete } = usePositionStore()
const { positionIndex } = storeToRefs(usePositionStore())

// department store
const { departments } = storeToRefs(useDepartmentStore())
const { departmentsFetch } = useDepartmentStore()

// throttle store
const { throttle, isDisabled } = storeToRefs(useThrottleStore())
const { initThrottle, startThrottle } = useThrottleStore()

// loading index
const loading = ref<boolean>(false)

// is bulk proccessing
const isBulkProccessing = ref<bulkActionStatus | null>(null)

// error retrieve index
const isError = ref<boolean>(false)

// error message retrieve index
const errorMessage = ref<string>('')

// selected position ids
const selectedPositionIds = ref<number[]>([])

// filter params
const filterParams = ref<PositionFilterParams>({
	status: route.query.status as commonStatus | null,
	department_id: route.query.department ? Number(route.query.department) : null,
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

// statuses
const { statuses } = useFilterModule()

// position headers
const { positionHeaders } = useTableModule()

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
	[() => filterParams.value.status, () => filterParams.value.department_id],
	async () => {
		let isPageChanged = false

		if (filterParams.value.page !== CONFIG.page) {
			filterParams.value.page = CONFIG.page
			isPageChanged = true
		}

		updateQueryParams(filterParams.value)

		if (!isPageChanged) {
			await fetchPositionIndex()
		}
	},
)

// handle position pagination
const handlePositionPaginate = async (options: any) => {
	const { sortBy, itemsPerPage: newItemsPerPage, page: newPage, search: newSearch } = options

	filterParams.value.page = newPage
	filterParams.value.search = newSearch
	filterParams.value.itemsPerPage = newItemsPerPage
	filterParams.value.sortKey = sortBy.length ? sortBy[0].key : undefined
	filterParams.value.sortOrder = sortBy.length ? sortBy[0].order : undefined

	updateQueryParams(filterParams.value)

	await fetchPositionIndex()
}

// fetch position index
const fetchPositionIndex = async () => {
	try {
		loading.value = true
		const response = await positionPaginate(filterParams.value)

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
			throttle.value['positionPaginate'] = formatLaravelRetryAfter(error)
			startThrottle('positionPaginate')
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

// handle bulk delete
const handleBulkDelete = async () => {
	try {
		isBulkProccessing.value = CONFIG.delete
		const response = await positionBulkDelete(selectedPositionIds.value)
		const message = response?.data?.messageCode
		toast.show(message, 'success')
		selectedPositionIds.value = []
		await fetchPositionIndex()
	} catch (error: any) {
		if (error.status === SYSTEM.SERVER_ERROR.UNPROCESSABLE_ENTITY) {
			errorMessage.value = error.response.data.messageCode
			toast.show(errorMessage.value, 'error')
		}

		if (error.status === SYSTEM.SERVER_ERROR.INTERNAL_SERVER_ERROR) {
			errorMessage.value = 'position.alert.error.bulkDelete'
			toast.show(errorMessage.value, 'error')
		}

		if (error.status === SYSTEM.SERVER_ERROR.TOO_MANY_REQUESTS) {
			throttle.value['positionBulkDelete'] = formatLaravelRetryAfter(error)
			startThrottle('positionBulkDelete')

			toast.show('common.throttle.tooManyRequestsAlert', 'error')
		}
	} finally {
		isBulkProccessing.value = null
	}
}

// handle bulk change status
const handleBulkChangeStatus = async (status: commonStatus) => {
	try {
		isBulkProccessing.value = status
		const response = await positionBulkUpdateStatus(selectedPositionIds.value, status)
		const message = response?.data?.messageCode
		toast.show(message, 'success')
		await fetchPositionIndex()
	} catch (error: any) {
		if (error.status === SYSTEM.SERVER_ERROR.UNPROCESSABLE_ENTITY) {
			errorMessage.value = error.response.data.messageCode
			toast.show(errorMessage.value, 'error')
		}

		if (error.status === SYSTEM.SERVER_ERROR.INTERNAL_SERVER_ERROR) {
			errorMessage.value = 'position.alert.error.bulkUpdateStatus'
			toast.show(errorMessage.value, 'error')
		}

		if (error.status === SYSTEM.SERVER_ERROR.TOO_MANY_REQUESTS) {
			throttle.value[`positionBulkUpdateStatus:${status}`] = formatLaravelRetryAfter(error)
			startThrottle(`positionBulkUpdateStatus:${status}`)

			toast.show('common.throttle.tooManyRequestsAlert', 'error')
		}
	} finally {
		isBulkProccessing.value = null
	}
}

// department options with "All" option
const departmentOptions = computed(() => {
	const allOption: Department = {
		id: 0,
		name: t('common.filter.allDepartmentsApply'),
		leader_id: null,
	}
	return [allOption, ...departments.value]
})

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

// on mounted
onMounted(async () => {
	initThrottle('positionPaginate')
	initThrottle(`positionBulkUpdateStatus:${CONFIG.active}`)
	initThrottle(`positionBulkUpdateStatus:${CONFIG.inactive}`)
	initThrottle('positionBulkDelete')
	initThrottle('departmentFetch:index')

	if (isDisabled.value('departmentFetch:index')) {
		isErrorGetDepartmentList.value = true
	}
})
</script>

<template>
	<app-breadcrumb class="mb-2" />

	<v-container fluid class="employee-list">
		<list-header title="common.header.listPosition">
			<template v-slot:prepend>
				<v-btn color="primary" class="text-none" :to="{ name: 'org.position.create' }">
					<v-icon icon="mdi-plus"></v-icon>
					<span class="d-none d-sm-inline ml-sm-2">{{
						$t('common.button.addPosition')
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
						<list-filter v-model="filterParams.status" :items="statuses" item-title="name" item-value="id"
							:label="$t('common.filter.status')"></list-filter>
					</v-col>

					<v-col cols="12" sm="4" lg="3">
						<list-filter v-model="filterParams.department_id" :items="departmentOptions" item-title="name"
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
				</v-row>
			</v-card-text>
		</v-card>

		<!-- bulk action -->
		<list-bulk-action :selected-items="selectedPositionIds" :is-bulk-proccessing="isBulkProccessing"
			@bulk-delete="handleBulkDelete" @bulk-active="handleBulkChangeStatus"
			@bulk-inactive="handleBulkChangeStatus"></list-bulk-action>


		<!-- data table -->
		<v-card class="elevation-1">
			<!-- error state -->
			<v-col cols="12" align="center" justify="center" v-show="isError">
				<div class="text-body-1 text-grey-darken-1 font-weight-medium mb-5">
					{{ errorMessage.length ? $t(errorMessage) : '' }}
				</div>

				<!-- retry btn -->
				<retry-btn :disabled="isDisabled('positionPaginate')" :loading="loading"
					@click.stop="fetchPositionIndex"></retry-btn>

				<!-- throttle alert -->
				<throttle-alert :show="isDisabled('positionPaginate')"
					:time="throttle['positionPaginate'] || 0"></throttle-alert>
			</v-col>
			<!-- data table -->
			<v-data-table-server v-show="!isError" :page="filterParams.page" :headers="positionHeaders"
				:items="positionIndex" :items-per-page="filterParams.itemsPerPage" item-value="id"
				:items-length="totalItemLength" :search="filterParams.search" :loading show-select
				v-model="selectedPositionIds" @update:options="handlePositionPaginate">

				<!-- position name -->
				<template v-slot:item.name="{ item }">
					<v-btn density="compact" variant="plain" color="primary" class="text-none">{{ item.name }}</v-btn>
				</template>

				<!-- position status -->
				<template v-slot:item.status="{ value }">
					<base-status-chip :val="value"></base-status-chip>
				</template>

				<template v-slot:bottom>
					<v-divider></v-divider>
					<div class="d-flex justify-center justify-sm-space-between align-center pa-4">
						<list-filter class="d-none d-sm-block" v-model="filterParams.itemsPerPage"
							:items="CONFIG.perPage" :label="$t('common.filter.itemPerPage')" max-width="200"
							min-width="200" :clearable="false" @update:model-value="fetchPositionIndex"></list-filter>

						<v-pagination v-if="totalPage > 1" v-model="filterParams.page" :length="totalPage"
							:total-visible="CONFIG.pageVisible" rounded="shape" density="comfortable"
							@update:model-value="fetchPositionIndex"></v-pagination>
					</div>
				</template>
			</v-data-table-server>
		</v-card>
	</v-container>
</template>