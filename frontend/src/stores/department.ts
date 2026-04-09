import api from '@/services/api'
import { defineStore } from 'pinia'
import { ref } from 'vue'

interface Department {
	id: number
	name: string
	leader_id: number | null
}

export const useDepartmentStore = defineStore('deparment', () => {
	const departments = ref<Department[]>([])

	const isFetched = ref<boolean>(false)

	const departmentsFetch = async () => {
		const params = { model: 'departments' }

		if (isFetched.value) return

		const response = await api.get('lookup/list', { params })
		departments.value = response.data.data

		isFetched.value = true
	}

	const departmentCreate = async (payload: object) => {
		const response = await api.post('department/create', payload)
		isFetched.value = false
		return response
	}

	return { departments, departmentsFetch, departmentCreate }
})
