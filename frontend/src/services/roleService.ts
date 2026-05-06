import api from './api'

/**
 * Service handling Role-related API calls
 */
export const roleService = {
	/**
	 * Fetch list of all roles
	 */
	getList: () => api.get('role/list'),

	/**
	 * Fetch paginate list of all roles
	 */
	getPaginate: (payload: object) => api.get('role/index', { params: payload }),

	/**
	 * Create a new role
	 * @param payload - Role data
	 */
	create: (payload: object) => api.post('role/create', payload),

	/**
	 * Delete a role by ID
	 * @param roleId - The ID of the role to delete
	 */
	delete: (roleId: number) => api.delete(`role/${roleId}`),

	/**
	 * Delete multiple roles by IDs
	 * @param roleIds - The IDs of the roles to delete
	 */
	bulkDelete: (roleIds: number[]) => api.post('role/bulk-delete', roleIds),
}

