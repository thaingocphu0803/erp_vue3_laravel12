import { computed, ref } from 'vue'
import { defineStore } from 'pinia'
import { authService } from '@/services/authService'
import router from '@/router'
import type { User } from '@/types/auth'

export const useAuthStore = defineStore('auth', () => {
	const user = ref<User | null>(null)

	const isInitialized = ref<boolean>(false)

	const isLoggedin = computed<boolean>(() => !!user.value)

	const authFetch = async () => {
		try {
			if (isInitialized.value) return

			const response = await authService.me()
			user.value = response.data.data?.user
		} catch (error: any) {
			console.log('fetch user api error', error)
			user.value = null
		} finally {
			isInitialized.value = true
		}
	}

	const authLogin = async (credentials: object) => {
		const response = await authService.login(credentials)
		user.value = response.data.data?.user
		isInitialized.value = true

		return response
	}

	const authLogout = async () => {
		try {
			const response = await authService.logout()

			return response
		} catch (error: any) {
			console.log('Logout api error', error)
			throw error
		} finally {
			user.value = null
			router.push({ name: 'login' })
		}
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
		clearAuth,
	}
})

