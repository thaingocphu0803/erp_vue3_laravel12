import api from './api'

/**
 * Service handling Authentication-related API calls
 */
export const authService = {
	/**
	 * Get current authenticated user info
	 */
	me: () => api.get('auth/me'),

	/**
	 * Login user
	 * @param credentials - email and password
	 */
	login: (credentials: object) => api.post('auth/login', credentials),

	/**
	 * Logout current user
	 */
	logout: () => api.post('auth/logout'),

	/**
	 * Create/Reset password
	 * @param payload - password data
	 */
	createPassword: (payload: object) => api.post('auth/create-password', payload),

	/**
	 * Resend verification email
	 * @param payload - email data
	 */
	resendVerifyEmail: (payload: object) => api.post('auth/resend-verify-email', payload),
}

