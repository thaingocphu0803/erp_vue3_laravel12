import { roleService } from '@/services/roleService'
import type { commonStatus } from '@/types/common'
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

	const roleDelete = async (id: number) => {
		const response = await roleService.delete(id)
		isFetched.value = false
		isFetchPaginate.value = false
		return response
	}

	const roleBulkDelete = async (ids: number[]) => {
		const payload = { ids }
		const response = await roleService.bulkDelete(payload)
		isFetched.value = false
		isFetchPaginate.value = false
		return response
	}

	const roleBulkUpdateStatus = async (ids: number[], status: commonStatus) => {
		const payload = { ids, status }
		const response = await roleService.bulkUpdateStatus(payload)
		isFetched.value = false
		isFetchPaginate.value = false
		return response
	}

	return {
		roles,
		roleIndex,
		rolesPaginate,
		rolesFetch,
		roleCreate,
		roleDelete,
		roleBulkDelete,
		roleBulkUpdateStatus,
	}
})

