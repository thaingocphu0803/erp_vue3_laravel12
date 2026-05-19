<script setup lang="ts">
import { onMounted, watch, computed } from 'vue'
import AppBreadcrumb from '@/components/layout/AppBreadcrumb.vue'
import ListHeader from '@/components/list/ListHeader.vue'
import BaseSearchBtn from '@/components/BaseSearchBtn.vue'
import ListFilter from '@/components/list/ListFilter.vue'
import CONFIG from '@/config/constants'
import { useRoute } from 'vue-router'
import { useFilterModule } from '@/composables/useFilterModule'
import { useTableModule } from '@/composables/useTableModule'
import BaseStatusChip from '@/components/BaseStatusChip.vue'
import ListBulkAction from '@/components/list/ListBulkAction.vue'
import { usePositionStore } from '@/stores/position'
import { useDepartmentStore } from '@/stores/department'
import { storeToRefs } from 'pinia'
import type { commonStatus } from '@/types/common'
import RetryBtn from '@/components/RetryBtn.vue'
import ThrottleAlert from '@/components/ThrottleAlert.vue'
import { useThrottleStore } from '@/stores/throttle'
import type { PositionFilterParams } from '@/types/position'
import type { Department } from '@/types/department'
import { t } from '@/plugins/vueI18n'
import { useIndexTable } from '@/composables/useIndexTable'
import { useBulkAction } from '@/composables/useBulkAction'
import { useFilterFetch } from '@/composables/useFilterFetch'

const route = useRoute()

// store
const { positionPaginate, positionBulkUpdateStatus, positionBulkDelete } = usePositionStore()
const { positionIndex } = storeToRefs(usePositionStore())
const { departmentsFetch } = useDepartmentStore()
const { departments } = storeToRefs(useDepartmentStore())
const { initThrottle } = useThrottleStore()

// filter options
const { statuses } = useFilterModule()
const { positionHeaders } = useTableModule()

// composables
const {
	loading,
	isError,
	errorMessage,
	totalItemLength,
	totalPage,
	filterParams,
	handleUpdateSearchValue,
	handlePaginate,
	fetchIndex,
	triggerFilterChange,
	throttle,
	isDisabled
} = useIndexTable<PositionFilterParams>(
	positionPaginate,
	'positionPaginate',
	{
		status: route.query.status as commonStatus | null,
		department_id: route.query.department ? Number(route.query.department) : null,
		search: route.query.search as string | '',
		itemsPerPage: Number(route.query.itemsPerPage) || CONFIG.itemPerPage,
		page: Number(route.query.page) || CONFIG.page,
		sortKey: null,
		sortOrder: null,
	}
)

const {
	selectedIds,
	isBulkProccessing,
	handleBulkDelete,
	handleBulkChangeStatus
} = useBulkAction(
	positionBulkDelete,
	positionBulkUpdateStatus,
	'positionBulkDelete',
	'positionBulkUpdateStatus',
	fetchIndex,
	{
		bulkDelete: 'position.alert.error.bulkDelete',
		bulkUpdateStatus: 'position.alert.error.bulkUpdateStatus'
	}
)

const {
	loading: loadingDepartment,
	isError: isErrorGetDepartmentList,
	errorMessage: errorMessageGetDepartmentList,
	fetchFilterData: getDepartmentList,
	checkDisabledError: checkDepartmentDisabledError
} = useFilterFetch(departmentsFetch, 'departmentFetch:index')

const departmentOptions = computed(() => {
	const allOption: Department = {
		id: 0,
		name: t('common.filter.allDepartmentsApply'),
		leader_id: null,
	}
	return [allOption, ...departments.value]
})

// watch filters
watch(() => [filterParams.value.status, filterParams.value.department_id], triggerFilterChange)

onMounted(() => {
	initThrottle('positionPaginate')
	initThrottle('positionBulkDelete')
	initThrottle(`positionBulkUpdateStatus:${CONFIG.active}`)
	initThrottle(`positionBulkUpdateStatus:${CONFIG.inactive}`)
	initThrottle('departmentFetch:index')
	checkDepartmentDisabledError()
})
</script>

<template>
	<app-breadcrumb class="mb-2" />

	<v-container fluid class="employee-list">
		<list-header title="common.header.listPosition">
			<template v-slot:prepend>
				<v-btn color="primary" class="text-none" :to="{ name: 'org.position.create' }">
					<v-icon icon="mdi-plus"></v-icon>
					<span class="d-none d-sm-inline ml-sm-2">{{ $t('common.button.addPosition') }}</span>
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
		<list-bulk-action :selected-items="selectedIds" :is-bulk-proccessing="isBulkProccessing"
			@bulk-delete="handleBulkDelete" @bulk-active="handleBulkChangeStatus"
			@bulk-inactive="handleBulkChangeStatus"></list-bulk-action>

		<!-- data table -->
		<v-card class="elevation-1">
			<v-col cols="12" align="center" justify="center" v-show="isError">
				<div class="text-body-1 text-grey-darken-1 font-weight-medium mb-5">
					{{ errorMessage.length ? $t(errorMessage) : '' }}
				</div>

				<retry-btn :disabled="isDisabled('positionPaginate')" :loading="loading"
					@click="fetchIndex"></retry-btn>

				<throttle-alert :show="isDisabled('positionPaginate')"
					:time="throttle['positionPaginate'] || 0"></throttle-alert>
			</v-col>

			<v-data-table-server v-show="!isError" :page="filterParams.page" :headers="positionHeaders"
				:items="positionIndex" :items-per-page="filterParams.itemsPerPage" item-value="id"
				:items-length="totalItemLength" :search="filterParams.search" :loading show-select
				v-model="selectedIds" @update:options="handlePaginate">

				<template v-slot:item.name="{ item }">
					<v-btn density="compact" variant="plain" color="primary" class="text-none">{{ item.name }}</v-btn>
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