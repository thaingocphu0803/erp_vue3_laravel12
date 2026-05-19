<script setup lang="ts">
import { watch, onMounted } from 'vue'
import AppBreadcrumb from '@/components/layout/AppBreadcrumb.vue'
import ListHeader from '@/components/list/ListHeader.vue'
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
import RetryBtn from '@/components/RetryBtn.vue'
import ThrottleAlert from '@/components/ThrottleAlert.vue'
import { useThrottleStore } from '@/stores/throttle'
import { useEmployeeStore } from '@/stores/employee'
import { useIndexTable } from '@/composables/useIndexTable'
import { useFilterFetch } from '@/composables/useFilterFetch'

const route = useRoute()

// store
const { employeePaginate } = useEmployeeStore()
const { employeeIndex } = storeToRefs(useEmployeeStore())
const { initThrottle } = useThrottleStore()

const { departments } = storeToRefs(useDepartmentStore())
const { departmentsFetch } = useDepartmentStore()

const { positions } = storeToRefs(usePositionStore())
const { positionFetch } = usePositionStore()

// filter options
const { employeeHeaders } = useTableModule()

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
} = useIndexTable<EmployeeFilterParams>(
	employeePaginate,
	'employeePaginate',
	{
		status: route.query.status as commonStatus | null,
		department_id: route.query.department ? Number(route.query.department) : null,
		position_id: route.query.position ? Number(route.query.position) : null,
		search: route.query.search as string | '',
		itemsPerPage: Number(route.query.itemsPerPage) || CONFIG.itemPerPage,
		page: Number(route.query.page) || CONFIG.page,
		sortKey: null,
		sortOrder: null,
	}
)

const {
	loading: loadingDepartment,
	isError: isErrorGetDepartmentList,
	errorMessage: errorMessageGetDepartmentList,
	fetchFilterData: getDepartmentList,
	checkDisabledError: checkDepartmentDisabledError
} = useFilterFetch(departmentsFetch, 'departmentFetch:index')

const {
	loading: loadingPosition,
	isError: isErrorGetPositionList,
	errorMessage: errorMessageGetPositionList,
	fetchFilterData: getPositionList,
	checkDisabledError: checkPositionDisabledError
} = useFilterFetch(positionFetch, 'positionFetch:index')

// watch filters
watch(
	() => [filterParams.value.status, filterParams.value.department_id, filterParams.value.position_id],
	triggerFilterChange
)

// selected employee ids (for data-table-server selection, even without bulk actions yet)
import { ref } from 'vue'
const selectedEmployeeIds = ref<number[]>([])

onMounted(() => {
	initThrottle('employeePaginate')
	initThrottle('departmentFetch:index')
	initThrottle('positionFetch:index')

	checkDepartmentDisabledError()
	checkPositionDisabledError()
})

</script>

<template>
	<app-breadcrumb class="mb-2" />

	<v-container :fluid="true" class="employee-list">
		<list-header title="common.header.listEmployee">
			<template v-slot:prepend>
				<v-btn color="primary" class="text-none" :to="{ name: 'hr.employee.create' }">
					<v-icon icon="mdi-plus"></v-icon>
					<span class="d-none d-sm-inline ml-sm-2">{{ $t('common.button.addEmployee') }}</span>
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
							:loading="loadingPosition" @click.stop="getPositionList" searchable>
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
			<v-col cols="12" align="center" justify="center" v-show="isError">
				<div class="text-body-1 text-grey-darken-1 font-weight-medium mb-5">
					{{ errorMessage.length ? $t(errorMessage) : '' }}
				</div>

				<retry-btn :disabled="isDisabled('employeePaginate')" :loading="loading"
					@click="fetchIndex"></retry-btn>

				<throttle-alert :show="isDisabled('employeePaginate')"
					:time="throttle['employeePaginate'] || 0"></throttle-alert>
			</v-col>
			
			<v-data-table-server v-show="!isError" :page="filterParams.page" :headers="employeeHeaders"
				:items="employeeIndex" :items-per-page="filterParams.itemsPerPage" item-value="id"
				:items-length="totalItemLength" :search="filterParams.search" v-model="selectedEmployeeIds"
				@update:options="handlePaginate">

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
							min-width="200" :clearable="false"></list-filter>

						<v-pagination v-if="totalPage > 1" v-model="filterParams.page" :length="totalPage"
							:total-visible="CONFIG.pageVisible" rounded="shape" density="comfortable"></v-pagination>
					</div>
				</template>
			</v-data-table-server>
		</v-card>
	</v-container>
</template>
