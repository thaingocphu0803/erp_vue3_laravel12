import { positionService } from '@/services/positionService'
import { defineStore } from 'pinia'
import { ref } from 'vue'
import type { Position } from '@/types/position'

export const usePositionStore = defineStore('position', () => {
	const positions = ref<Position[]>()

	const positionByDepartment = ref<Position[]>()

	const isFetchedByDepartment = ref<boolean>(false)

	const isFetched = ref<boolean>(false)

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

	return {
		positions,
		isFetched,
		positionByDepartment,
		positionCreate,
		positionFetch,
		positionsFetchByDepartmentId,
		positionReset,
	}
})
