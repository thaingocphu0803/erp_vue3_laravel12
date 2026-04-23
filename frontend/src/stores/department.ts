import { departmentService } from '@/services/departmentService'
import { defineStore } from 'pinia'
import { ref } from 'vue'
import type { Department } from '@/types/department'

export const useDepartmentStore = defineStore('deparment', () => {
	const departments = ref<Department[]>([])

	const isFetched = ref<boolean>(false)

	const departmentsFetch = async () => {
		if (isFetched.value) return

		const response = await departmentService.getList()
		departments.value = response.data.data

		isFetched.value = true
	}

	const departmentCreate = async (payload: object) => {
		const response = await departmentService.create(payload)
		isFetched.value = false
		return response
	}

	return { departments, departmentsFetch, departmentCreate }
})
