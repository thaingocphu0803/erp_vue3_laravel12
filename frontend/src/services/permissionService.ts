import api from './api'

/**
 * Service handling Permission-related API calls
 */
export const permissionService = {
	/**
	 * Fetch list of all permissions (grouped)
	 */
	getList: () => api.get('permission/index'),
}
