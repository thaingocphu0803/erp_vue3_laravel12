import api from './api'

/**
 * Service handling Employee-related API calls
 */
export const employeeService = {
	/**
	 * Create a new employee with FormData (supports avatar upload)
	 * @param payload - Employee data
	 */
	create: (payload: any) => {
		const formData = new FormData()

		Object.entries(payload).forEach(([key, value]) => {
			if (value !== null && value !== undefined) {
				// Handle array values (like role_ids)
				if (Array.isArray(value)) {
					value.forEach((val) => formData.append(`${key}[]`, val))
				} else {
					formData.append(key, value as string | Blob)
				}
			}
		})

		return api.post('employee/create', formData, {
			headers: {
				'Content-Type': 'multipart/form-data',
			},
		})
	},

	/**
	 * Get paginate employee data
	 * @param params - Filter parameters
	 */
	getPaginate: async (params: object) => {
		const response = await api.get('employee/index', { params })
		return response
	},
}

