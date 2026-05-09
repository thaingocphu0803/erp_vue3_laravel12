import api from './api'

/**
 * Service handling Position-related API calls
 */
export const positionService = {
	/**
	 * Fetch list of all positions
	 */
	getList: () => api.get('position/list'),

	/**
	 * Fetch positions belonging to a specific department
	 * @param departmentId - ID of the department
	 */
	getListByDepartment: (departmentId: number) =>
		api.get('position/list-by-department', {
			params: { department_id: departmentId },
		}),

	/**
	 * Create a new position
	 * @param payload - Position data
	 */
	create: (payload: object) => api.post('position/create', payload),

	/**
	 * Paginate positions
	 * @param params - Pagination parameters
	 */
	getPaginate: (params: object) => api.get('position/index', { params }),

	/**
	 * Delete a position
	 * @param id - ID of the position to delete
	 */
	delete: (id: number) => api.delete(`position/delete/${id}`),

	/**
	 * Update bulk status of positions
	 * @param payload - Array of position IDs and new status
	 */
	bulkUpdateStatus: (payload: object) => api.post('position/bulk-update-status', payload),

	/**
	 * Bulk delete positions
	 * @param payload - Array of position IDs to delete
	 */
	bulkDelete: (payload: object) => api.post('position/bulk-delete', payload),
}

