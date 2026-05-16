import { employeeService } from '@/services/employeeService'
import { defineStore } from 'pinia'
import { ref } from 'vue'
import type { EmployeeItem } from '@/types/employee'

export const useEmployeeStore = defineStore('employee', () => {
	const isFetchedEmployee = ref<boolean>(false)

	const employeeIndex = ref<EmployeeItem[]>()

	const employeePaginate = async (params: object) => {
		const response = await employeeService.getPaginate(params)
		employeeIndex.value = response.data.data
		return response
	}

	const employeeCreate = async (payload: object) => {
		const response = await employeeService.create(payload)
		return response
	}

	return {
		isFetchedEmployee,
		employeeIndex,
		employeeCreate,
		employeePaginate,
	}
})

