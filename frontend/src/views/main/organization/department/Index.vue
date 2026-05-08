<script setup lang="ts">
import { onMounted, ref, watch } from 'vue'
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
import type { DepartmentFilterParams } from '@/types/department'
import { useDepartmentStore } from '@/stores/department'
import { storeToRefs } from 'pinia'
import { useThrottleStore } from '@/stores/throttle'
import { formatLaravelRetryAfter } from '@/utils/errorHandler'
import BaseBtn from '@/components/BaseBtn.vue'
import RetryBtn from '@/components/RetryBtn.vue'
import ThrottleAlert from '@/components/ThrottleAlert.vue'
import ListBulkAction from '@/components/list/ListBulkAction.vue'

// route
const route = useRoute()

// toast store
const toast = useToastStore()

// department store
const { departmentsPaginate, departmentBulkUpdateStatus, departmentBulkDelete } = useDepartmentStore()
const { departmentIndex } = storeToRefs(useDepartmentStore())

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

// selected department ids
const selectedDepartmentIds = ref<number[]>([])

// filter params
const filterParams = ref<DepartmentFilterParams>({
	status: route.query.status as commonStatus | null,
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

// department headers
const { departmentHeaders } = useTableModule()

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
	() => filterParams.value.status,
	async () => {
		let isPageChanged = false

		if (filterParams.value.page !== CONFIG.page) {
			filterParams.value.page = CONFIG.page
			isPageChanged = true
		}

		updateQueryParams(filterParams.value)

		if (!isPageChanged) {
			await fetchDepartmentIndex()
		}
	},
)

// handle update search value
const handleUpdateSearchValue = debounce((val: string) => {
	filterParams.value.search = val
}, CONFIG.debounceTimeout)

// handle department pagination
const handleDepartmentPaginate = async (options: any) => {
	const { sortBy, itemsPerPage: newItemsPerPage, page: newPage, search: newSearch } = options

	filterParams.value.page = newPage
	filterParams.value.search = newSearch
	filterParams.value.itemsPerPage = newItemsPerPage
	filterParams.value.sortKey = sortBy.length ? sortBy[0].key : undefined
	filterParams.value.sortOrder = sortBy.length ? sortBy[0].order : undefined

	updateQueryParams(filterParams.value)

	await fetchDepartmentIndex()
}

// fetch department index
const fetchDepartmentIndex = async () => {
	try {
		loading.value = true
		const response = await departmentsPaginate(filterParams.value)

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
			throttle.value['departmentsPaginate'] = formatLaravelRetryAfter(error)
			startThrottle('departmentsPaginate')
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
		const response = await departmentBulkDelete(selectedDepartmentIds.value)
		const message = response?.data?.messageCode
		toast.show(message, 'success')
		selectedDepartmentIds.value = []
		await fetchDepartmentIndex()
	} catch (error: any) {
		if (error.status === SYSTEM.SERVER_ERROR.UNPROCESSABLE_ENTITY) {
			errorMessage.value = error.response.data.messageCode
			toast.show(errorMessage.value, 'error')
		}

		if (error.status === SYSTEM.SERVER_ERROR.INTERNAL_SERVER_ERROR) {
			errorMessage.value = 'department.alert.error.bulkDelete'
			toast.show(errorMessage.value, 'error')
		}

		if (error.status === SYSTEM.SERVER_ERROR.TOO_MANY_REQUESTS) {
			throttle.value['departmentBulkDelete'] = formatLaravelRetryAfter(error)
			startThrottle('departmentBulkDelete')

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
		const response = await departmentBulkUpdateStatus(selectedDepartmentIds.value, status)
		const message = response?.data?.messageCode
		toast.show(message, 'success')
		await fetchDepartmentIndex()
	} catch (error: any) {
		if (error.status === SYSTEM.SERVER_ERROR.UNPROCESSABLE_ENTITY) {
			errorMessage.value = error.response.data.messageCode
			toast.show(errorMessage.value, 'error')
		}

		if (error.status === SYSTEM.SERVER_ERROR.INTERNAL_SERVER_ERROR) {
			errorMessage.value = 'department.alert.error.bulkUpdateStatus'
			toast.show(errorMessage.value, 'error')
		}

		if (error.status === SYSTEM.SERVER_ERROR.TOO_MANY_REQUESTS) {
			throttle.value[`departmentBulkUpdateStatus:${status}`] = formatLaravelRetryAfter(error)
			startThrottle(`departmentBulkUpdateStatus:${status}`)

			toast.show('common.throttle.tooManyRequestsAlert', 'error')
		}
	} finally {
		isBulkProccessing.value = null
	}
}

// on mounted
onMounted(async () => {
	initThrottle('departmentsPaginate')
	initThrottle(`departmentBulkUpdateStatus:${CONFIG.active}`)
	initThrottle(`departmentBulkUpdateStatus:${CONFIG.inactive}`)
	initThrottle('departmentBulkDelete')
})
</script>

<template>
	<app-breadcrumb class="mb-2" />

	<v-container fluid class="employee-list">
		<list-header title="common.header.listDepartment">
			<template v-slot:prepend>
				<v-btn color="primary" class="text-none" :to="{ name: 'org.department.create' }">
					<v-icon icon="mdi-plus"></v-icon>
					<span class="d-none d-sm-inline ml-sm-2">{{
						$t('common.button.addDepartment')
					}}</span>
				</v-btn>
			</template>
		</list-header>

		<v-card class="elevation-1 mb-4">
			<v-card-text>
				<v-row dense>
					<v-col cols="12" sm="6" lg="4">
						<base-search-btn :label="$t('common.filter.nameOrCode')"
							@update:model-value="handleUpdateSearchValue">
						</base-search-btn>
					</v-col>

					<v-col cols="12" sm="6" lg="3">
						<list-filter v-model="filterParams.status" :items="statuses" item-title="name" item-value="id"
							:label="$t('common.filter.status')"></list-filter>
					</v-col>
				</v-row>
			</v-card-text>
		</v-card>

		<!-- bulk action -->
		<list-bulk-action :selected-items="selectedDepartmentIds" :is-bulk-proccessing="isBulkProccessing"
			@bulk-delete="handleBulkDelete" @bulk-active="handleBulkChangeStatus"
			@bulk-inactive="handleBulkChangeStatus"></list-bulk-action>

		<!-- data-table-server -->
		<v-card class="elevation-1">
			<!-- error state -->
			<v-col cols="12" align="center" justify="center" v-show="isError">
				<div class="text-body-1 text-grey-darken-1 font-weight-medium mb-5">
					{{ errorMessage.length ? $t(errorMessage) : '' }}
				</div>

				<!-- retry btn -->
				<retry-btn :disabled="isDisabled('departmentsPaginate')" :loading="loading"
					@click="fetchDepartmentIndex"></retry-btn>

				<!-- throttle alert -->
				<throttle-alert :show="isDisabled('departmentsPaginate')"
					:time="throttle['departmentsPaginate'] || 0"></throttle-alert>
			</v-col>

			<!-- data-table-server -->
			<v-data-table-server v-show="!isError" :page="filterParams.page" :headers="departmentHeaders"
				:items="departmentIndex" :items-per-page="filterParams.itemsPerPage" item-value="id"
				:items-length="totalItemLength" :search="filterParams.search" :loading show-select
				v-model="selectedDepartmentIds" @update:options="handleDepartmentPaginate">

				<!-- data-table-server item action -->
				<template v-slot:item.name="{ item }">
					<base-btn :title="item.name" variant="plain" color="primary" class="text-none"></base-btn>
				</template>

				<template v-slot:item.status="{ value }">
					<base-status-chip :val="value"></base-status-chip>
				</template>

				<template v-slot:bottom>
					<v-divider></v-divider>
					<div class="d-flex justify-center justify-sm-space-between align-center pa-4">
						<list-filter class="d-none d-sm-block" v-model="filterParams.itemsPerPage"
							:items="CONFIG.perPage" :label="$t('common.filter.itemPerPage')" max-width="200"
							min-width="200" :clearable="false"></list-filter>

						<v-pagination v-if="totalPage > 1" v-model="filterParams.page" :length="totalPage"
							:total-visible="CONFIG.pageVisible" rounded="shape" density="comfortable"></v-pagination>
					</div>
				</template>
			</v-data-table-server>
		</v-card>
	</v-container>
</template>
