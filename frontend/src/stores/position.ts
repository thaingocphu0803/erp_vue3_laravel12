import api from '@/services/api'
import { defineStore } from 'pinia'
import { ref } from 'vue'

interface Position {
	id: number
	name: string
}

export const usePositionStore = defineStore('position', () => {
	const positions = ref<Position[]>()

	const positionByDepartment = ref<Position[]>()

	const isFetchedByDepartment = ref<boolean>(false)

	const isFetched = ref<boolean>(false)

	const positionCreate = async (payload: object) => {
		const response = await api.post('position/create', payload)
		isFetched.value = false
		isFetchedByDepartment.value = false
		return response
	}

	const positionFetch = async () => {
		if (isFetched.value) return

		const response = await api.get('position/list')
		positions.value = response.data.data
		isFetched.value = true
		return response
	}

	const positionsFetchByDepartmentId = async (departmentId: number) => {
		if (isFetchedByDepartment.value) return

		const params = {
			department_id: departmentId,
		}
		const response = await api.get('position/list-by-department', { params })
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
