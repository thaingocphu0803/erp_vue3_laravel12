import api from './api'

/**
 * Service handling Department-related API calls
 */
export const departmentService = {
	/**
	 * Fetch list of all departments
	 */
	getList: () => api.get('department/list'),

	/**
	 * Create a new department
	 * @param payload - Department data
	 */
	create: (payload: object) => api.post('department/create', payload),
}
