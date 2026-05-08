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

	/**
	 * Get paginate departments
	 * @param payload - Pagination data
	 */
	getPaginate: (payload: object) => api.get('department/index', { params: payload }),

	/**
	 * Delete a department
	 * @param id - Department ID
	 */
	delete: (id: number) => api.delete(`department/${id}`),

	/**
	 * Bulk delete department
	 * @param payload - Department IDs
	 */
	bulkDelete: (payload: object) => api.post('department/bulk-delete', payload),

	/**
	 * Bulk update department status
	 * @param payload - Department IDs and status
	 */
	bulkUpdateStatus: (payload: object) => api.post('department/bulk-update-status', payload),
}

