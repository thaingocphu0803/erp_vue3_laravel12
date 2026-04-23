<script setup lang="ts">
import { ref, computed, reactive, watch, onMounted } from 'vue'
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

interface PositionItem {
	id: number
	name: string
	description: string
	status: commonStatus
}

const route = useRoute()
const toast = useToastStore()

const loading = ref<boolean>(false)

const positionStatus = ref(route.query.status as commonStatus | undefined)

const tempSearch = ref((route.query.search as string) || '')

const search = ref((route.query.search as string) || '')
const itemsPerPage = ref(Number(route.query.itemsPerPage) || CONFIG.itemPerPage)
const page = ref(Number(route.query.page) || CONFIG.page)

const positionItems = ref<PositionItem[]>()
const totalItemLength = ref<number>(0)
const totalPage = ref<number>(0)

const { updateQueryParams, replaceQueryParams } = useRouteQuery()
const { statuses } = useFilterModule()
const { positionHeaders } = useTableModule()

const resetURLToDefault = () => {
	page.value = CONFIG.page
	itemsPerPage.value = CONFIG.itemPerPage
	search.value = ''
	tempSearch.value = ''
	positionStatus.value = undefined

	replaceQueryParams({
		page: page.value,
		itemsPerPage: itemsPerPage.value,
	})
}

watch(positionStatus, async () => {
	let isPageChanged = false

	if (page.value !== CONFIG.page) {
		page.value = CONFIG.page
		isPageChanged = true
	}

	const params = {
		page: page.value,
		status: positionStatus.value,
	}

	const newQueryParams = updateQueryParams(params)

	if (!isPageChanged) {
		await fetchPositionIndex(newQueryParams)
	}
})

const handleUpdateSearchValue = debounce((val: string) => {
	search.value = val
}, CONFIG.debounceTimeout)

const handlePositionPaginate = async (options: any) => {
	const { sortBy, itemsPerPage: newItemsPerPage, page: newPage, search: newSearch } = options

	const params = {
		page: newPage,
		search: newSearch,
		itemsPerPage: newItemsPerPage,
		sortKey: sortBy.length ? sortBy[0].key : undefined,
		sortOrder: sortBy.length ? sortBy[0].order : undefined,
	}

	const newQueryParams = updateQueryParams(params)

	await fetchPositionIndex(newQueryParams)
}

const fetchPositionIndex = async (params: object) => {
	try {
		loading.value = true
		const response = await api.get('position/index', { params })

		if (response.status === 200) {
			const data = response?.data

			positionItems.value = data.data
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
					<v-col cols="12" sm="6" lg="4">
						<base-search-btn
							v-model="tempSearch"
							:label="$t('common.filter.name')"
							@update:model-value="handleUpdateSearchValue"
						>
						</base-search-btn>
					</v-col>

					<v-col cols="12" sm="6" lg="3">
						<list-filter
							v-model="positionStatus"
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
				:headers="positionHeaders"
				:items="positionItems"
				:items-per-page="itemsPerPage"
				item-value="id"
				:items-length="totalItemLength"
				:search
				:loading
				@update:options="handlePositionPaginate"
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
