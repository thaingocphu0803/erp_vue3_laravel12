import api from '@/services/api'
import { defineStore } from 'pinia'

export const useEmployeeStore = defineStore('employee', () => {
	const employeeCreate = async (payload: object) => {
		let formData = new FormData()

		Object.entries(payload).forEach(([key, value]) => {
			if (value !== null && value !== undefined) {
				formData.append(key, value)
			}
		})

		const response = await api.post('employee/create', formData, {
			headers: {
				'Content-Type': 'multipart/form-data',
			},
		})

		return response
	}

	return {
		employeeCreate,
	}
})

