import api from '@/services/api'
import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useRoleStore = defineStore('role', () => {

	const isFetched = ref<boolean>(false)

	const roleCreate = async (payload: object) => {
		try {
			const response = await api.post('role/create', payload)
			isFetched.value = false
			return response
		} catch (error: any) {
			throw error
		}
	}

	return { roleCreate}
})
