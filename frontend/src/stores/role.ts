import { roleService } from '@/services/roleService'
import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useRoleStore = defineStore('role', () => {
	const isFetched = ref<boolean>(false)

	const roles = ref<any[]>([])

	const rolesFetch = async () => {
		if (isFetched.value) return

		const response = await roleService.getList()
		roles.value = response.data.data
		isFetched.value = true
	}

	const roleCreate = async (payload: object) => {
		const response = await roleService.create(payload)
		isFetched.value = false
		return response
	}

	return { roles, rolesFetch, roleCreate }
})
