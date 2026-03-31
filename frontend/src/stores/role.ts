import api from '@/services/api'
import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useRoleStore = defineStore('role', () => {

	const isFetched = ref<boolean>(false)

	// const roles = ref<>()

	const roleCreate = async (payload: object) => {
		try {
			const response = await api.post('role/create', payload)
			return response
		} catch (error: any) {
			throw error
		}
	}

	const rolesFetch = async () => {
		try {
			const response = await api.get
		} catch (error: any) {
			console.log(error)
		}
	}


	return { roleCreate }
})
