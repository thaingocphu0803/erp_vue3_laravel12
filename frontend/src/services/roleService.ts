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
	 * Create a new role
	 * @param payload - Role data
	 */
	create: (payload: object) => api.post('role/create', payload),
}

