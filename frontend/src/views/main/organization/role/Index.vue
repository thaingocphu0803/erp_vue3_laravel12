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
import ListAction from '@/components/list/ListAction.vue'

interface RoleItem {
	id: number
	name: string
	description: string
	status: commonStatus
}

const route = useRoute()
const toast = useToastStore()

const loading = ref<boolean>(false)

const roleStatus = ref(route.query.status as commonStatus | undefined)

const tempSearch = ref((route.query.search as string) || '')

const search = ref((route.query.search as string) || '')

const selectedRoleIds = ref<number[]>([])

const bulkActionLoading = ref<string | null>(null)

const confirmDeleteDialog = ref(false)

const handleBulkAction = async (action: 'active' | 'inactive' | 'delete') => {

	if (action === 'delete') {
		confirmDeleteDialog.value = true
		return
	}

	try {
		bulkActionLoading.value = action
		let url = ''
		if (action === 'active') url = 'role/bulk-active'
		if (action === 'inactive') url = 'role/bulk-inactive'

		const response = await api.post(url, { ids: selectedRoleIds.value })

		if (response.status === 200 || response.status === 204) {
			const successMessage = response.data?.message || 'Thao tác thành công'
			toast.show(successMessage, 'success')
			selectedRoleIds.value = []

			const params = {
				page: page.value,
				search: search.value,
				itemsPerPage: itemsPerPage.value,
				status: roleStatus.value,
			}
			await fetchRoleIndex(params)
		}
	} catch (error: any) {
		const errorMessage = error.response?.data?.message || 'Có lỗi xảy ra'
		toast.show(errorMessage, 'error')
	} finally {
		bulkActionLoading.value = null
	}
}

const executeBulkDelete = async () => {
	try {
		bulkActionLoading.value = 'delete'
		const response = await api.post('role/bulk-delete', { ids: selectedRoleIds.value })

		if (response.status === 200 || response.status === 204) {
			const successMessage = response.data?.message || 'Thao tác thành công'
			toast.show(successMessage, 'success')
			selectedRoleIds.value = []

			const params = {
				page: page.value,
				search: search.value,
				itemsPerPage: itemsPerPage.value,
				status: roleStatus.value,
			}
			await fetchRoleIndex(params)
		}
	} catch (error: any) {
		const errorMessage = error.response?.data?.message || 'Có lỗi xảy ra'
		toast.show(errorMessage, 'error')
	} finally {
		bulkActionLoading.value = null
		confirmDeleteDialog.value = false
	}
}

const itemsPerPage = ref(Number(route.query.itemsPerPage) || CONFIG.itemPerPage)
const page = ref(Number(route.query.page) || CONFIG.page)

const roleItems = ref<RoleItem[]>([])
const totalItemLength = ref<number>(0)
const totalPage = ref<number>(0)

const { updateQueryParams, replaceQueryParams } = useRouteQuery()
const { statuses } = useFilterModule()
const { roleHeaders } = useTableModule()

const resetURLToDefault = () => {
	page.value = CONFIG.page
	itemsPerPage.value = CONFIG.itemPerPage
	search.value = ''
	tempSearch.value = ''
	roleStatus.value = undefined

	replaceQueryParams({
		page: page.value,
		itemsPerPage: itemsPerPage.value,
	})
}

watch(roleStatus, async () => {
	let isPageChanged = false

	if (page.value !== CONFIG.page) {
		page.value = CONFIG.page
		isPageChanged = true
	}

	const params = {
		page: page.value,
		status: roleStatus.value,
	}

	const newQueryParams = updateQueryParams(params)

	if (!isPageChanged) {
		await fetchRoleIndex(newQueryParams)
	}
})

const handleUpdateSearchValue = debounce((val: string) => {
	search.value = val
}, CONFIG.debounceTimeout)

const handleRolePaginate = async (options: any) => {
	const { sortBy, itemsPerPage: newItemsPerPage, page: newPage, search: newSearch } = options

	const params = {
		page: newPage,
		search: newSearch,
		itemsPerPage: newItemsPerPage,
		sortKey: sortBy.length ? sortBy[0].key : undefined,
		sortOrder: sortBy.length ? sortBy[0].order : undefined,
	}

	const newQueryParams = updateQueryParams(params)

	await fetchRoleIndex(newQueryParams)
}

const fetchRoleIndex = async (params: object) => {
	try {
		loading.value = true
		const response = await api.get('role/index', { params })

		if (response.status === 200) {
			const data = response?.data

			roleItems.value = data.data
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
		<!-- header -->
		<list-header title="common.header.listRole">
			<!-- prepend -->
			<template v-slot:prepend>
				<v-btn color="primary" class="text-none" :to="{ name: 'org.role.create' }">
					<v-icon icon="mdi-plus"></v-icon>
					<span class="d-none d-sm-inline ml-sm-2">{{
						$t('common.button.addRole')
					}}</span>
				</v-btn>
			</template>
		</list-header>

		<!-- filter -->
		<v-card class="elevation-1 mb-4">
			<v-card-text>
				<v-row dense>
					<v-col cols="12" sm="6" lg="4">
						<base-search-btn v-model="tempSearch" :label="$t('common.filter.nameOrCode')"
							@update:model-value="handleUpdateSearchValue">
						</base-search-btn>
					</v-col>

					<v-col cols="12" sm="6" lg="3">
						<list-filter v-model="roleStatus" :items="statuses" item-title="name" item-value="id"
							:label="$t('common.filter.status')"></list-filter>
					</v-col>
				</v-row>
			</v-card-text>
		</v-card>

		<!-- bulk action -->
		<v-expand-transition>
			<v-card v-if="selectedRoleIds.length > 0" class="elevation-1 mb-4 pa-4">
				<v-row dense align="center">
					<v-col cols="12" class="d-flex align-center flex-wrap">
						<span class="mr-4 font-weight-medium">
							{{ $t('common.action.bulkAction.selectedItems', { count: selectedRoleIds.length }) }}
						</span>

						<v-btn color="success" variant="flat" class="mr-2 text-none" prepend-icon="mdi-check-circle"
							@click="handleBulkAction('active')" :loading="bulkActionLoading === 'active'"
							:disabled="bulkActionLoading !== null" size="small">
							{{ $t('common.btn.active') }}
						</v-btn>

						<v-btn color="warning" variant="flat" class="mr-2 text-none" prepend-icon="mdi-minus-circle"
							@click="handleBulkAction('inactive')" :loading="bulkActionLoading === 'inactive'"
							:disabled="bulkActionLoading !== null" size="small">
							{{ $t('common.btn.inactive') }}
						</v-btn>

						<v-btn color="error" variant="flat" class="text-none" prepend-icon="mdi-delete"
							@click="handleBulkAction('delete')" :loading="bulkActionLoading === 'delete'"
							:disabled="bulkActionLoading !== null" size="small">
							{{ $t('common.btn.delete') }}
						</v-btn>
					</v-col>
				</v-row>
			</v-card>
		</v-expand-transition>

		<!-- data-table-server -->
		<v-card class="elevation-1">
			<v-data-table-server :page :headers="roleHeaders" :items="roleItems" :items-per-page="itemsPerPage"
				item-value="id" :items-length="totalItemLength" :search :loading show-select v-model="selectedRoleIds"
				@update:options="handleRolePaginate">
				<!-- data-table-server item action -->
				<template v-slot:item.actions="{ item }">
					<list-action></list-action>
				</template>

				<template v-slot:item.status="{ value }">
					<base-status-chip :val="value"></base-status-chip>
				</template>

				<!-- data-table-server bottom -->
				<template v-slot:bottom>
					<v-divider></v-divider>
					<div class="d-flex justify-center justify-sm-space-between align-center pa-4">
						<!-- pagination items per page -->
						<list-filter class="d-none d-sm-block" v-model="itemsPerPage" :items="CONFIG.perPage"
							:label="$t('common.filter.itemPerPage')" max-width="200" min-width="200"
							:clearable="false"></list-filter>

						<!-- pagination -->
						<v-pagination v-if="totalPage > 1" v-model="page" :length="totalPage"
							:total-visible="CONFIG.pageVisible" rounded="shape" density="comfortable"></v-pagination>
					</div>
				</template>
			</v-data-table-server>
		</v-card>
	</v-container>

	<!-- Confirm Delete Dialog -->
	<v-dialog v-model="confirmDeleteDialog" max-width="400">
		<v-card>
			<v-card-title class="text-h6 font-weight-bold d-flex align-center">
				<v-icon color="error" class="mr-2">mdi-alert</v-icon>
				{{ $t('common.action.bulkAction.deleteModal.title') }}
			</v-card-title>
			<v-card-text>
				{{ $t('common.action.bulkAction.deleteModal.content', { count: selectedRoleIds.length }) }}
			</v-card-text>
			<v-card-actions class="pa-4 pt-0">
				<v-spacer></v-spacer>
				<v-btn color="grey-darken-1" variant="text" @click="confirmDeleteDialog = false" class="text-none">{{
					$t('common.btn.cancel') }}</v-btn>
				<v-btn color="error" variant="flat" @click="executeBulkDelete" :loading="bulkActionLoading === 'delete'"
					class="text-none">{{ $t('common.btn.delete') }}</v-btn>
			</v-card-actions>
		</v-card>
	</v-dialog>
</template>
