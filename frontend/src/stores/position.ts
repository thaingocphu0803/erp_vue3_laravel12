import api from "@/services/api";
import { defineStore } from "pinia";
import { ref } from "vue";

export const usePositionStore = defineStore('position', () => {

	const isFetched = ref<boolean>(false);

	const positionCreate = async (payload: object) => {
		try {
			const response = await api.post('position/create', payload)
			isFetched.value = false
			return response
		} catch (error: any) {
			throw error
		}
	}

	return {positionCreate}
})
