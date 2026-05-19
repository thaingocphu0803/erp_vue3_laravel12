<script setup lang="ts">
import { onMounted, watch } from 'vue'
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
import { useDepartmentStore } from '@/stores/department'
import { storeToRefs } from 'pinia'
import type { commonStatus } from '@/types/common'
import RetryBtn from '@/components/RetryBtn.vue'
import ThrottleAlert from '@/components/ThrottleAlert.vue'
import { useThrottleStore } from '@/stores/throttle'
import type { DepartmentFilterParams } from '@/types/department'
import { useIndexTable } from '@/composables/useIndexTable'
import { useBulkAction } from '@/composables/useBulkAction'

const route = useRoute()

// store
const { departmentsPaginate, departmentBulkUpdateStatus, departmentBulkDelete } = useDepartmentStore()
const { departmentIndex } = storeToRefs(useDepartmentStore())
const { initThrottle } = useThrottleStore()

// filter options
const { statuses } = useFilterModule()
const { departmentHeaders } = useTableModule()

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
} = useIndexTable<DepartmentFilterParams>(
	departmentsPaginate,
	'departmentsPaginate',
	{
		status: route.query.status as commonStatus | null,
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
	departmentBulkDelete,
	departmentBulkUpdateStatus,
	'departmentBulkDelete',
	'departmentBulkUpdateStatus',
	fetchIndex,
	{
		bulkDelete: 'department.alert.error.bulkDelete',
		bulkUpdateStatus: 'department.alert.error.bulkUpdateStatus'
	}
)

// watch filters
watch(() => filterParams.value.status, triggerFilterChange)

onMounted(() => {
	initThrottle('departmentsPaginate')
	initThrottle('departmentBulkDelete')
	initThrottle(`departmentBulkUpdateStatus:${CONFIG.active}`)
	initThrottle(`departmentBulkUpdateStatus:${CONFIG.inactive}`)
})
</script>

<template>
	<app-breadcrumb class="mb-2" />

	<v-container fluid class="employee-list">
		<list-header title="common.header.listDepartment">
			<template v-slot:prepend>
				<v-btn color="primary" class="text-none" :to="{ name: 'org.department.create' }">
					<v-icon icon="mdi-plus"></v-icon>
					<span class="d-none d-sm-inline ml-sm-2">{{ $t('common.button.addDepartment') }}</span>
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
		<list-bulk-action :selected-items="selectedIds" :is-bulk-proccessing="isBulkProccessing"
			@bulk-delete="handleBulkDelete" @bulk-active="handleBulkChangeStatus"
			@bulk-inactive="handleBulkChangeStatus"></list-bulk-action>

		<!-- data-table-server -->
		<v-card class="elevation-1">
			<v-col cols="12" align="center" justify="center" v-show="isError">
				<div class="text-body-1 text-grey-darken-1 font-weight-medium mb-5">
					{{ errorMessage.length ? $t(errorMessage) : '' }}
				</div>

				<retry-btn :disabled="isDisabled('departmentsPaginate')" :loading="loading"
					@click="fetchIndex"></retry-btn>

				<throttle-alert :show="isDisabled('departmentsPaginate')"
					:time="throttle['departmentsPaginate'] || 0"></throttle-alert>
			</v-col>

			<v-data-table-server v-show="!isError" :page="filterParams.page" :headers="departmentHeaders"
				:items="departmentIndex" :items-per-page="filterParams.itemsPerPage" item-value="id"
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
