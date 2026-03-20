import api from '@/services/api'
import { defineStore } from 'pinia'

export const useRoleStore = defineStore('role', () => {

	const roleCreate = async (payload: object) => {
		try {
			const response = await api.post('role/create', payload)
			return response
		} catch (error: any) {
			throw error
		}
	}

	return { roleCreate }
})
