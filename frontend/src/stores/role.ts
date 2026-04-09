import api from '@/services/api'
import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useRoleStore = defineStore('role', () => {
	const isFetched = ref<boolean>(false)

	const roles = ref<any[]>([])

	const rolesFetch = async () => {
		if (isFetched.value) return

		const response = await api.get('role/list')
		roles.value = response.data.data
		isFetched.value = true
	}

	const roleCreate = async (payload: object) => {
		const response = await api.post('role/create', payload)
		isFetched.value = false
		return response
	}

	return { roles, rolesFetch, roleCreate }
})
