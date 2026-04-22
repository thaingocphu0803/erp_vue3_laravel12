import { computed, ref } from 'vue'
import { defineStore } from 'pinia'
import api from '@/services/api'
import router from '@/router'

interface User {
	name: string
	email: string
	avatar: string
}

export const useAuthStore = defineStore('auth', () => {
	const user = ref<User | null>(null)

	const isInitialized = ref<boolean>(false)

	const isLoggedin = computed<boolean>(() => !!user.value)

	const authFetch = async () => {
		try {
			if (isInitialized.value) return

			const response = await api.get('auth/me')
			user.value = response.data.data?.user
		} catch (error: any) {
			console.log('fetch user api error', error)
			user.value = null
		} finally {
			isInitialized.value = true
		}
	}

	const authLogin = async (credentials: object) => {
		const response = await api.post('auth/login', credentials)
		user.value = response.data.data?.user
		isInitialized.value = true

		return response
	}

	const authLogout = async () => {
		try {
			const response = await api.post('auth/logout')

			return response
		} catch (error: any) {
			console.log('Logout api error', error)
			throw error
		} finally {
			user.value = null
			router.push({ name: 'login' })
		}
	}

	const authCreatePassword = async (payload: object) => {
		const response = await api.post(`auth/create-password`, payload)
		return response
	}

	const authResendVerifyEmail = async (payload: object) => {
		const response = await api.post(`auth/resend-verify-email`, payload)
		return response
	}

	const clearAuth = () => {
		user.value = null
		isInitialized.value = false
	}

	return {
		user,
		isInitialized,
		isLoggedin,
		authFetch,
		authLogin,
		authLogout,
		authCreatePassword,
		authResendVerifyEmail,
		clearAuth,
	}
})

