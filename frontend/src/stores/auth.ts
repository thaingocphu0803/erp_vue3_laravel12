import { computed, ref } from 'vue'
import { defineStore } from 'pinia'
import api from '@/services/api'
import router from '@/router'

export const useAuthStore = defineStore('auth', () => {
  interface User {
    name: string
    email: string
    avatar: string
  }

  const user = ref<User | null>(null)

  const isInitialized = ref<boolean>(false)

  const isLoggedin = computed<boolean>(() => !!user.value)

  const authFetch = async () => {
    try {
      if (isInitialized.value) return

      const response = await api.get('auth/me')
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
      clearAuth()
      console.log('Login api error', error)
      throw error
    }
  }

  const authLogout = async () => {
    try {
      const response = await api.get('auth/logout')
      if (response.status === 200) {
        router.push({ name: 'login' })
      }
    } catch (error: any) {
      console.log('Logout api error', error)
    } finally {
      user.value = null
    }
  }

  const clearAuth = () => {
    user.value = null
    isInitialized.value = false
  }

  return { user, isInitialized, isLoggedin, authFetch, authLogin, authLogout, clearAuth }
})
