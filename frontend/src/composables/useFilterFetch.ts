import { ref } from 'vue'
import { useThrottleStore } from '@/stores/throttle'
import { storeToRefs } from 'pinia'
import SYSTEM from '@/config/system'
import { formatLaravelRetryAfter } from '@/utils/errorHandler'

export function useFilterFetch(fetchApiFunc: () => Promise<any>, throttleKey: string) {
	const throttleStore = useThrottleStore()
	const { throttle, isDisabled } = storeToRefs(throttleStore)
	const { startThrottle } = throttleStore

	const loading = ref<boolean>(false)
	const isError = ref<boolean>(false)
	const errorMessage = ref<string>('')

	const fetchFilterData = async () => {
		if (isDisabled.value(throttleKey)) return

		try {
			loading.value = true
			await fetchApiFunc()
			isError.value = false
			errorMessage.value = ''
		} catch (error: any) {
			isError.value = true
			errorMessage.value = 'common.error.fetchDataFailed'

			if (error.status === SYSTEM.SERVER_ERROR.TOO_MANY_REQUESTS) {
				throttle.value[throttleKey] = formatLaravelRetryAfter(error)
				startThrottle(throttleKey)
			}
		} finally {
			loading.value = false
		}
	}

	// Helper function to check if there is an error due to disablement on mount
	const checkDisabledError = () => {
		if (isDisabled.value(throttleKey)) {
			isError.value = true
		}
	}

	return {
		loading,
		isError,
		errorMessage,
		fetchFilterData,
		checkDisabledError,
	}
}

