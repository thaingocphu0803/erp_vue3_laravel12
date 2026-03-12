<script setup lang="ts">
import { ref, computed, reactive, watch, onMounted } from 'vue'
import AppBreadcrumb from '@/components/layout/AppBreadcrumb.vue'
import ListHeader from '@/components/list/ListHeader.vue'
import ListSearch from '@/components/list/ListSearch.vue'
import ListFilter from '@/components/list/ListFilter.vue'
import defaultConfig from '@/config/default'
import { useRoute } from 'vue-router'
import { debounce } from 'vuetify/lib/util/helpers.mjs'
import { useRouteQuery } from '@/composables/useRouteQuery'
import { useFilterModule } from '@/composables/useFilterModule'
import { useTableModule } from '@/composables/useTableModule'
import api from '@/services/api'
import BaseStatusChip from '@/components/BaseStatusChip.vue'
import { useToastStore } from '@/stores/toast'


interface DepartmentItem {
	id: number,
	name: string,
	code: string,
	description: string,
	status: 'A' | 'X'
}

const route = useRoute();
const toast = useToastStore();

const loading = ref<boolean>(false)

const departmentStatus = ref(route.query.status as "A" | "X" | undefined)

const tempSearch = ref((route.query.search as string) || '')

const search = ref((route.query.search as string) || '')
const itemsPerPage = ref(Number(route.query.itemsPerPage) || defaultConfig.itemPerPage)
const page = ref(Number(route.query.page) || defaultConfig.page)


const departmentItems = ref<DepartmentItem[]>([]);
const totalItemLength = ref<number>(0)
const totalPage = ref<number>(0)


const { updateQueryParams, replaceQueryParams } = useRouteQuery()
const { statuses } = useFilterModule()
const { departmentHeaders } = useTableModule()

const resetURLToDefault = () => {
	page.value = defaultConfig.page
	itemsPerPage.value = defaultConfig.itemPerPage
	search.value = ''
	tempSearch.value = ''
	departmentStatus.value = undefined

	replaceQueryParams({
		page: page.value,
		itemsPerPage: itemsPerPage.value
	})
}

watch(departmentStatus, async () => {

	let isPageChanged = false;

	if (page.value !== defaultConfig.page) {
		page.value = defaultConfig.page;
		isPageChanged = true;
	}

	const params = {
		page: page.value,
		status: departmentStatus.value,
	}

	const newQueryParams = updateQueryParams(params)

	if (!isPageChanged) {
		await fetchDepartmentIndex(newQueryParams)
	}
})

const handleUpdateSearchValue = (debounce((val: string) => {
	search.value = val
}, defaultConfig.debounceTimeout))

const handleDepartmentPaginate = async (options: any) => {

	const { sortBy, itemsPerPage: newItemsPerPage, page: newPage, search: newSearch } = options

	const params = {
		page: newPage,
		search: newSearch,
		itemsPerPage: newItemsPerPage,
		sortKey: sortBy.length ? sortBy[0].key : undefined,
		sortOrder: sortBy.length ? sortBy[0].order : undefined
	}

	const newQueryParams = updateQueryParams(params)

	await fetchDepartmentIndex(newQueryParams)
}

const fetchDepartmentIndex = async (params: object) => {
	try {
		loading.value = true
		const response = await api.get('department/index', { params });

		if (response.status === 200) {
			const data = response?.data

			departmentItems.value = data.data
			totalItemLength.value = data.meta.total
			totalPage.value = data.meta.last_page
		}
	} catch (error: any) {
		if (error.response?.status === 422) {
			resetURLToDefault()
		}

		if (error.response?.status === 422 || error.response?.status === 500) {
			const errorMesssage =  error.response.data.messageCode
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
		<list-header title="common.header.listEmployee">
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
						<list-search
							v-model="tempSearch"
							:label="$t('common.filter.departmentNameOrCode')"
							@update:model-value="handleUpdateSearchValue"
						>
						</list-search>
					</v-col>

					<v-col cols="12" sm="6" lg="3">
						<list-filter
							v-model="departmentStatus"
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
					<v-btn variant="text" color="primary" class="text-none custom-link-btn" > {{ item.name }}</v-btn>
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
							:items="defaultConfig.perPage"
							:label="$t('common.filter.itemPerPage')"
							max-width="200"
							min-width="200"
							:clearable="false"
						></list-filter>

						<v-pagination
							v-if="totalPage > 1"
							v-model="page"
							:length="totalPage"
							:total-visible="defaultConfig.pageVisible"
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
