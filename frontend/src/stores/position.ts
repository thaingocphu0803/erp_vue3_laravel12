import { positionService } from '@/services/positionService'
import { defineStore } from 'pinia'
import { ref } from 'vue'
import type { Position, PositionItem } from '@/types/position'
import type { commonStatus } from '@/types/common'

export const usePositionStore = defineStore('position', () => {
	const isFetched = ref<boolean>(false)

	const isFetchedByDepartment = ref<boolean>(false)

	const positions = ref<Position[]>()

	const positionByDepartment = ref<Position[]>()

	const positionIndex = ref<PositionItem[]>()

	const positionCreate = async (payload: object) => {
		const response = await positionService.create(payload)
		isFetched.value = false
		isFetchedByDepartment.value = false
		return response
	}

	const positionFetch = async () => {
		if (isFetched.value) return

		const response = await positionService.getList()
		positions.value = response.data.data
		isFetched.value = true
		return response
	}

	const positionsFetchByDepartmentId = async (departmentId: number) => {
		if (isFetchedByDepartment.value) return

		const response = await positionService.getListByDepartment(departmentId)
		positionByDepartment.value = response.data.data
		isFetchedByDepartment.value = true
		return response
	}

	const positionReset = () => {
		positionByDepartment.value = []
		isFetchedByDepartment.value = false
	}

	const positionPaginate = async (params: object) => {
		const response = await positionService.getPaginate(params)
		positionIndex.value = response.data.data
		return response
	}

	const positionDelete = async (id: number) => {
		const response = await positionService.delete(id)
		isFetched.value = false
		isFetchedByDepartment.value = false
		return response
	}

	const positionBulkUpdateStatus = async (ids: number[], status: commonStatus) => {
		const payload = { ids, status }
		const response = await positionService.bulkUpdateStatus(payload)
		isFetched.value = false
		isFetchedByDepartment.value = false
		return response
	}

	const positionBulkDelete = async (ids: number[]) => {
		const payload = { ids }
		const response = await positionService.bulkDelete(payload)
		isFetched.value = false
		isFetchedByDepartment.value = false
		return response
	}

	return {
		positions,
		isFetched,
		positionByDepartment,
		positionIndex,
		positionCreate,
		positionFetch,
		positionsFetchByDepartmentId,
		positionReset,
		positionPaginate,
		positionDelete,
		positionBulkDelete,
		positionBulkUpdateStatus,
	}
})

