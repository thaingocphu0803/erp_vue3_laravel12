import { employeeService } from '@/services/employeeService'
import { defineStore } from 'pinia'

export const useEmployeeStore = defineStore('employee', () => {
	const employeeCreate = async (payload: object) => {
		const response = await employeeService.create(payload)
		return response
	}

	return {
		employeeCreate,
	}
})

