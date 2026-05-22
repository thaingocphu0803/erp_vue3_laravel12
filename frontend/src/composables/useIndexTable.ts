import { ref, type Ref } from 'vue'
import { debounce } from 'vuetify/lib/util/helpers.mjs'
import { useRouteQuery } from '@/composables/useRouteQuery'
import { useToastStore } from '@/stores/toast'
import { useThrottleStore } from '@/stores/throttle'
import { storeToRefs } from 'pinia'
import CONFIG from '@/config/constants'
import SYSTEM from '@/config/system'
import { formatLaravelRetryAfter } from '@/utils/errorHandler'
import type { FilterParams } from '@/types/common'

export function useIndexTable<TFilterParams extends FilterParams & Record<string, any>>(
	fetchApiFunc: (params: TFilterParams) => Promise<any>,
	throttleKey: string,
	defaultFilterParams: TFilterParams,
) {
	const toast = useToastStore()
	const throttleStore = useThrottleStore()
	const { throttle, isDisabled } = storeToRefs(throttleStore)
	const { startThrottle } = throttleStore

	// State
	const loading = ref<boolean>(false)
	const isError = ref<boolean>(false)
	const errorMessage = ref<string>('')
	const totalItemLength = ref<number>(0)
	const totalPage = ref<number>(0)

	// Filter Params
	const filterParams = ref<TFilterParams>({ ...defaultFilterParams }) as Ref<TFilterParams>

	const { updateQueryParams, replaceQueryParams } = useRouteQuery()

	// reset url to default
	const resetURLToDefault = () => {
		for (const key in filterParams.value) {
			if (key === 'page') filterParams.value[key] = CONFIG.page as any
			else if (key === 'itemsPerPage') filterParams.value[key] = CONFIG.itemPerPage as any
			else if (key === 'search') filterParams.value[key] = '' as any
			else filterParams.value[key] = null as any
		}

		replaceQueryParams({
			page: filterParams.value.page,
			itemsPerPage: filterParams.value.itemsPerPage,
		})
	}

	// handle update search value
	const handleUpdateSearchValue = debounce((val: string) => {
		filterParams.value.search = val as any
	}, CONFIG.debounceTimeout)

	// handle pagination from Vuetify
	const handlePaginate = async (options: any) => {
		const { sortBy, itemsPerPage: newItemsPerPage, page: newPage, search: newSearch } = options

		filterParams.value.page = newPage
		filterParams.value.search = newSearch
		filterParams.value.itemsPerPage = newItemsPerPage
		filterParams.value.sortKey = sortBy && sortBy.length ? sortBy[0].key : undefined
		filterParams.value.sortOrder = sortBy && sortBy.length ? sortBy[0].order : undefined

		updateQueryParams(filterParams.value)
		await fetchIndex()
	}

	// fetch index
	const fetchIndex = async () => {
		try {
			loading.value = true
			const response = await fetchApiFunc(filterParams.value)

			isError.value = false
			totalItemLength.value = response?.data?.meta?.total || 0
			totalPage.value = response?.data?.meta?.last_page || 0
		} catch (error: any) {
			isError.value = true

			// handle unprocessable entity error
			if (error.status === SYSTEM.SERVER_ERROR.UNPROCESSABLE_ENTITY) {
				errorMessage.value =
					error.response?.data?.messageCode || 'common.error.fetchDataFailed'
				toast.show(errorMessage.value, 'error')
				resetURLToDefault()
			}

			// handle too many request error
			if (error.status === SYSTEM.SERVER_ERROR.TOO_MANY_REQUESTS) {
				throttle.value[throttleKey] = formatLaravelRetryAfter(error)
				startThrottle(throttleKey)
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

	// watch logic for custom filters (like status, department_id)
	const triggerFilterChange = async () => {
		let isPageChanged = false

		if (filterParams.value.page !== CONFIG.page) {
			filterParams.value.page = CONFIG.page as any
			isPageChanged = true
		}

		updateQueryParams(filterParams.value)

		if (!isPageChanged) {
			await fetchIndex()
		}
	}

	return {
		loading,
		isError,
		errorMessage,
		totalItemLength,
		totalPage,
		filterParams,
		resetURLToDefault,
		handleUpdateSearchValue,
		handlePaginate,
		fetchIndex,
		triggerFilterChange,
		throttle,
		isDisabled,
	}
}

