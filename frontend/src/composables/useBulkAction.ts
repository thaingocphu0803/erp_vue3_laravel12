import { ref } from 'vue'
import { useToastStore } from '@/stores/toast'
import { useThrottleStore } from '@/stores/throttle'
import { storeToRefs } from 'pinia'
import CONFIG from '@/config/constants'
import SYSTEM from '@/config/system'
import { formatLaravelRetryAfter } from '@/utils/errorHandler'
import type { bulkActionStatus, commonStatus } from '@/types/common'

export function useBulkAction(
	bulkDeleteApiFunc: (ids: number[]) => Promise<any>,
	bulkUpdateStatusApiFunc: (ids: number[], status: commonStatus) => Promise<any>,
	deleteThrottleKey: string,
	updateStatusThrottleKeyPrefix: string,
	onSuccessRefreshFunc: () => Promise<void>,
	errorMessages: { bulkDelete: string; bulkUpdateStatus: string }
) {
	const toast = useToastStore()
	const throttleStore = useThrottleStore()
	const { throttle } = storeToRefs(throttleStore)
	const { startThrottle } = throttleStore

	const selectedIds = ref<number[]>([])
	const isBulkProccessing = ref<bulkActionStatus | null>(null)
	const errorMessage = ref<string>('')

	const handleBulkDelete = async () => {
		try {
			isBulkProccessing.value = CONFIG.delete as bulkActionStatus
			const response = await bulkDeleteApiFunc(selectedIds.value)
			const message = response?.data?.messageCode
			toast.show(message, 'success')
			selectedIds.value = []
			await onSuccessRefreshFunc()
		} catch (error: any) {
			if (error.status === SYSTEM.SERVER_ERROR.UNPROCESSABLE_ENTITY) {
				errorMessage.value = error.response?.data?.messageCode
				toast.show(errorMessage.value, 'error')
			}

			if (error.status === SYSTEM.SERVER_ERROR.INTERNAL_SERVER_ERROR) {
				errorMessage.value = errorMessages.bulkDelete
				toast.show(errorMessage.value, 'error')
			}

			if (error.status === SYSTEM.SERVER_ERROR.TOO_MANY_REQUESTS) {
				throttle.value[deleteThrottleKey] = formatLaravelRetryAfter(error)
				startThrottle(deleteThrottleKey)
				toast.show('common.throttle.tooManyRequestsAlert', 'error')
			}
		} finally {
			isBulkProccessing.value = null
		}
	}

	const handleBulkChangeStatus = async (status: commonStatus) => {
		try {
			isBulkProccessing.value = status
			const response = await bulkUpdateStatusApiFunc(selectedIds.value, status)
			const message = response?.data?.messageCode
			toast.show(message, 'success')
			await onSuccessRefreshFunc()
		} catch (error: any) {
			if (error.status === SYSTEM.SERVER_ERROR.UNPROCESSABLE_ENTITY) {
				errorMessage.value = error.response?.data?.messageCode
				toast.show(errorMessage.value, 'error')
			}

			if (error.status === SYSTEM.SERVER_ERROR.INTERNAL_SERVER_ERROR) {
				errorMessage.value = errorMessages.bulkUpdateStatus
				toast.show(errorMessage.value, 'error')
			}

			if (error.status === SYSTEM.SERVER_ERROR.TOO_MANY_REQUESTS) {
				const throttleKey = `${updateStatusThrottleKeyPrefix}:${status}`
				throttle.value[throttleKey] = formatLaravelRetryAfter(error)
				startThrottle(throttleKey)
				toast.show('common.throttle.tooManyRequestsAlert', 'error')
			}
		} finally {
			isBulkProccessing.value = null
		}
	}

	return {
		selectedIds,
		isBulkProccessing,
		errorMessage,
		handleBulkDelete,
		handleBulkChangeStatus
	}
}
