<script setup lang="ts">
import { ref, watch } from 'vue'
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
import api from '@/services/api'
import BaseStatusChip from '@/components/BaseStatusChip.vue'
import { useToastStore } from '@/stores/toast'
import type { commonStatus } from '@/types/common'
import SYSTEM from '@/config/system'
import type { DepartmentFilterParams } from '@/types/department'

interface DepartmentItem {
	id: number
	name: string
	code: string
	description: string
	status: commonStatus
}

// route
const route = useRoute()

// toast store
const toast = useToastStore()

// loading index
const loading = ref<boolean>(false)

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

const handleUpdateSearchValue = debounce((val: string) => {
	search.value = val
}, CONFIG.debounceTimeout)

// handle role pagination
const handleRolePaginate = async (options: any) => {
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
		const response = await api.get('department/index', { params: filterParams.value })

		if (response.status === 200) {
			const data = response?.data

			departmentItems.value = data.data
			totalItemLength.value = data.meta.total
			totalPage.value = data.meta.last_page
		}
	} catch (error: any) {
		if (error.status === SYSTEM.SERVER_ERROR.UNPROCESSABLE_ENTITY) {
			resetURLToDefault()
		}

		if (
			error.status === SYSTEM.SERVER_ERROR.UNPROCESSABLE_ENTITY ||
			error.status === SYSTEM.SERVER_ERROR.INTERNAL_SERVER_ERROR
		) {
			const errorMesssage = error.response.data.message
			toast.show(errorMesssage, 'error')
		}
	} finally {
		loading.value = false
	}
}
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
						<base-search-btn
							v-model="tempSearch"
							:label="$t('common.filter.nameOrCode')"
							@update:model-value="handleUpdateSearchValue"
						>
						</base-search-btn>
					</v-col>

					<v-col cols="12" sm="6" lg="3">
						<list-filter
							v-model="filterParams.status"
							:items="statuses"
							item-title="name"
							item-value="id"
							:label="$t('common.filter.status')"
						></list-filter>
					</v-col>
				</v-row>
			</v-card-text>
		</v-card>

		<v-card class="elevation-1">
			<v-data-table-server
				:page
				:headers="departmentHeaders"
				:items="departmentItems"
				:items-per-page="itemsPerPage"
				item-value="id"
				:items-length="totalItemLength"
				:search
				:loading
				@update:options="handleDepartmentPaginate"
			>
				<template v-slot:item.name="{ item }">
					<v-btn variant="text" color="primary" class="text-none custom-link-btn">
						{{ item.name }}</v-btn
					>
				</template>

				<template v-slot:item.status="{ value }">
					<base-status-chip :val="value"></base-status-chip>
				</template>

				<template v-slot:bottom>
					<v-divider></v-divider>
					<div class="d-flex justify-center justify-sm-space-between align-center pa-4">
						<list-filter
							class="d-none d-sm-block"
							v-model="itemsPerPage"
							:items="CONFIG.perPage"
							:label="$t('common.filter.itemPerPage')"
							max-width="200"
							min-width="200"
							:clearable="false"
						></list-filter>

						<v-pagination
							v-if="totalPage > 1"
							v-model="page"
							:length="totalPage"
							:total-visible="CONFIG.pageVisible"
							rounded="shape"
							density="comfortable"
						></v-pagination>
					</div>
				</template>
			</v-data-table-server>
		</v-card>
	</v-container>
</template>

<style scoped>
.custom-link-btn:deep(.v-btn__overlay) {
	display: none;
}
.custom-link-btn:hover {
	text-decoration: underline;
}
</style>
