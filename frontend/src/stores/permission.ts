import { permissionService } from '@/services/permissionService'
import { defineStore } from 'pinia'
import { ref } from 'vue'
import type { PermissionGroup } from '@/types/role'

export const usePermissionStore = defineStore('permission', () => {
	const permissionGroup = ref<PermissionGroup>({})

	const isFetched = ref<boolean>(false)

	const permissionFetch = async () => {
		if (isFetched.value) return

		const response = await permissionService.getList()
		isFetched.value = true
		permissionGroup.value = response.data?.data
	}
	return { permissionFetch, permissionGroup }
})

