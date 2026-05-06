import { roleService } from '@/services/roleService'
import type { Role, RoleItem } from '@/types/role'
import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useRoleStore = defineStore('role', () => {
	const isFetched = ref<boolean>(false)

	const isFetchPaginate = ref<boolean>(false)

	const roles = ref<Role[]>([])

	const roleIndex = ref<RoleItem[]>([])

	const rolesPaginate = async (payload: object) => {
		const response = await roleService.getPaginate(payload)
		roleIndex.value = response.data.data
		isFetchPaginate.value = true
		return response
	}

	const rolesFetch = async () => {
		if (isFetched.value) return

		const response = await roleService.getList()
		roles.value = response.data.data
		isFetched.value = true
	}

	const roleCreate = async (payload: object) => {
		const response = await roleService.create(payload)
		isFetched.value = false
		isFetchPaginate.value = false
		return response
	}

	const roleDelete = async (roleId: number) => {
		if (roleId === 0) return

		const response = await roleService.delete(roleId)
		isFetched.value = false
		isFetchPaginate.value = false
		return response
	}

	const roleBulkDelete = async (roleIds: number[]) => {
		const response = await roleService.bulkDelete(roleIds)
		isFetched.value = false
		isFetchPaginate.value = false
		return response
	}

	return { roles, roleIndex, rolesPaginate, rolesFetch, roleCreate, roleDelete, roleBulkDelete }
})

