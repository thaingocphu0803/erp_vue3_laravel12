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
import SYSTEM from '@/config/system'
import ListAction from '@/components/list/ListAction.vue'
import BaseConfirmModal from '@/components/BaseConfirmModal.vue'
import ListBulkAction from '@/components/list/ListBulkAction.vue'
import { useRoleStore } from '@/stores/role'
import { storeToRefs } from 'pinia'
import type { commonStatus } from '@/types/common'
import RetryBtn from '@/components/RetryBtn.vue'
import ThrottleAlert from '@/components/ThrottleAlert.vue'
import { useThrottleStore } from '@/stores/throttle'
import { formatLaravelRetryAfter } from '@/utils/errorHandler'
import type { RoleFilterParams } from '@/types/role'

// route
const route = useRoute()

// toast store
const toast = useToastStore()

// role store
const { roleDelete, rolesPaginate } = useRoleStore()
const { roleIndex } = storeToRefs(useRoleStore())

// throttle store
const { throttle, isDisabled } = storeToRefs(useThrottleStore())
const { initThrottle, startThrottle } = useThrottleStore()

// loading index
const loading = ref<boolean>(false)

// error retrieve index
const isError = ref<boolean>(false)

// error message retrieve index
const errorMessage = ref<string>('')

// selected role ids
const selectedRoleIds = ref<number[]>([])

// label for close or open item delete modal
const itemDeleteDialog = ref(false)

// role id for delete
const roleIdForDelete = ref<number>(0)

// filter params
const filterParams = ref<RoleFilterParams>({
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

// role headers
const { roleHeaders } = useTableModule()

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
			await fetchRolePaginate()
		}
	},
)

// handle update search value
const handleUpdateSearchValue = debounce((val: string) => {
	filterParams.value.search = val
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

	await fetchRolePaginate()
}

// fetch role paginate
const fetchRolePaginate = async () => {
	try {
		loading.value = true
		const response = await rolesPaginate(filterParams.value)

		isError.value = false
		totalItemLength.value = response?.data?.meta.total
		totalPage.value = response?.data?.meta.last_page
	} catch (error: any) {
		isError.value = true

		// handle unprocessable entity error
		if (error.status === SYSTEM.SERVER_ERROR.UNPROCESSABLE_ENTITY) {
			errorMessage.value = error.response.data.messageCode
			resetURLToDefault()
			toast.show(errorMessage.value, 'error')
		}

		// handle too many request error
		if (error.status === SYSTEM.SERVER_ERROR.TOO_MANY_REQUESTS) {
			throttle.value['rolesPaginate'] = formatLaravelRetryAfter(error)
			startThrottle('rolesPaginate')
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

const handleItemDelete = async () => {
	try {
		const response = await roleDelete(roleIdForDelete.value)
		const message = response?.data?.messageCode
		toast.show(message, 'success')
		await fetchRolePaginate()
	} catch (error: any) {
		// handle not found error
		if (error.status === SYSTEM.SERVER_ERROR.NOT_FOUND) {
			errorMessage.value = 'role.alert.error.notFoundForDelete'
			toast.show(errorMessage.value, 'error')
		}

		// handle internal server error
		if (error.status === SYSTEM.SERVER_ERROR.INTERNAL_SERVER_ERROR) {
			errorMessage.value = 'role.alert.error.delete'
			toast.show(errorMessage.value, 'error')
		}
	} finally {
		itemDeleteDialog.value = false
	}
}

const handleDeleteAction = (roleId: number) => {
	itemDeleteDialog.value = true
	roleIdForDelete.value = roleId
}

const handleBulkDelete = () => {
	console.log('bulkDelete')
}

const handleBulkActive = () => {
	console.log('bulkActive')
}

const handleBulkInactive = () => {
	console.log('bulkInactive')
}

onMounted(() => {
	initThrottle('rolesPaginate')
})
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
		<list-bulk-action :selected-items="selectedRoleIds" @bulk-delete="handleBulkDelete"
			@bulk-active="handleBulkActive" @bulk-inactive="handleBulkInactive"></list-bulk-action>

		<!-- data-table-server -->
		<v-card class="elevation-1">
			<!-- error state -->
			<v-col cols="12" align="center" justify="center" v-show="isError">
				<div class="text-body-1 text-grey-darken-1 font-weight-medium mb-5">
					{{ $t(errorMessage) }}
				</div>

				<!-- retry btn -->
				<retry-btn :disabled="isDisabled('rolesPaginate')" :loading="loading"
					@click="fetchRolePaginate"></retry-btn>

				<!-- throttle alert -->
				<throttle-alert :show="isDisabled('rolesPaginate')"
					:time="throttle['rolesPaginate'] || 0"></throttle-alert>
			</v-col>

			<!-- table data -->
			<v-data-table-server v-show="!isError" :page="filterParams.page" :headers="roleHeaders" :items="roleIndex"
				:items-per-page="filterParams.itemsPerPage" item-value="id" :items-length="totalItemLength"
				:search="filterParams.search" :loading show-select v-model="selectedRoleIds"
				@update:options="handleRolePaginate">
				<!-- data-table-server item action -->
				<template v-slot:item.actions="{ item }">
					<list-action @delete="handleDeleteAction(item.id)"></list-action>
				</template>

				<template v-slot:item.status="{ value }">
					<base-status-chip :val="value"></base-status-chip>
				</template>

				<!-- data-table-server bottom -->
				<template v-slot:bottom>
					<v-divider></v-divider>
					<div class="d-flex justify-center justify-sm-space-between align-center pa-4">
						<!-- pagination items per page -->
						<list-filter class="d-none d-sm-block" v-model="filterParams.itemsPerPage"
							:items="CONFIG.perPage" :label="$t('common.filter.itemPerPage')" max-width="200"
							min-width="200" :clearable="false"></list-filter>

						<!-- pagination -->
						<v-pagination v-if="totalPage > 1" v-model="filterParams.page" :length="totalPage"
							:total-visible="CONFIG.pageVisible" rounded="shape" density="comfortable"></v-pagination>
					</div>
				</template>
			</v-data-table-server>
		</v-card>
	</v-container>

	<!-- Item Delete Confirm Modal -->
	<base-confirm-modal v-model="itemDeleteDialog" :title="$t('common.confirmModal.delete.title')" :content="$t('common.confirmModal.delete.content', {
		name: $t('common.subModule.role').toLocaleLowerCase(),
	})
		" :titleConfirmBtn="$t('common.btn.delete')" :loading @confirm="handleItemDelete()"
		@cancel="itemDeleteDialog = false"></base-confirm-modal>
</template>
