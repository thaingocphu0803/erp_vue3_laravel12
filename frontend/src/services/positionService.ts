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
}
