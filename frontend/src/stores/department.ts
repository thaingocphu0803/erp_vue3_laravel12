import api from '@/services/api'
import { defineStore } from 'pinia'
import { ref } from 'vue'

interface Department {
	code: string
	name: string
}

export const useDepartmentStore = defineStore('deparment', () => {
	const departments = ref<Department[]>()

	const isFetched = ref<boolean>(false)

	const departmentsFetch = async () => {
		const params = { model: 'department' }

		try {
			if (isFetched.value) return

			const response = await api.get('lookup/list', { params })
			departments.value = response.data.data.list

			isFetched.value = true
		} catch (error: any) {
			throw error
		}
	}

	const departmentCreate = async (payload: object) => {
		try {
			const response = await api.post('department/create', payload)
			isFetched.value = false
			return response
		} catch (error) {
			throw error
		}
	}

	return { departments, departmentsFetch, departmentCreate }
})
