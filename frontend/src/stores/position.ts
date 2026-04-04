import api from '@/services/api'
import { defineStore } from 'pinia'
import { ref } from 'vue'

interface Position {
	id: number
	name: string
}

export const usePositionStore = defineStore('position', () => {
	const positions = ref<Position[]>()

	const isFetched = ref<boolean>(false)

	const positionCreate = async (payload: object) => {
		try {
			const response = await api.post('position/create', payload)
			isFetched.value = false
			return response
		} catch (error: any) {
			throw error
		}
	}

	const positionFetch = async () => {
		try {
			if (isFetched.value) return

			const params = { model: 'positions' }
			const response = await api.get('lookup/list', { params })
			positions.value = response.data.data
			isFetched.value = true
			return response
		} catch (error: any) {
			throw error
		}
	}

	return { positionCreate, positionFetch, positions, isFetched }
})

