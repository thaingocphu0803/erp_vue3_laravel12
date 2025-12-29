import { computed, ref } from 'vue'
import { defineStore } from 'pinia'
import api, { getCsrfCookie } from '@/services/api'

export const useAuthStore = defineStore('auth', () => {
  interface User {
    name: string
    email: string
  }

  const user = ref<User | null>(null)

  const isInitialized = ref<boolean>(false)

  const isLoggedin = computed<boolean>(() => !!user.value)

  const authFetch = async () => {
    try {
      if (isInitialized.value) return

      await getCsrfCookie()

      const response = await api.get('user/me')
      user.value = response.data.data?.user
    } catch (error: any) {
      console.log('etch user api error', error)
      clearAuth()
    } finally {
      isInitialized.value = true
    }
  }

  const authLogin = async (credentials: object) => {
    try {
      const response = await api.post('auth/login', credentials)
      user.value = response.data.data?.user
      isInitialized.value = true

      return response
    } catch (error: any) {
      console.log('Login api error', error)
      clearAuth()
      throw error
    }
  }
	
	const authLogout = async () => {
		try {
			const response = await api.get('auth/logout')
			return response
		} catch (error :any) {
      console.log('Logout api error', error)
	  }finally{
      clearAuth();
    }
  }

  const clearAuth = () => {
    user.value = null
    isInitialized.value = false
  }

  return { user, isInitialized, isLoggedin, authFetch, authLogin, authLogout, clearAuth }
})
