import api from '@/services/api'
import type { suportedScopes } from '@/types/common'
import { defineStore } from 'pinia'
import { ref } from 'vue'

interface Permission {
	id: number
	name: string
	slug: string
	supported_scopes: suportedScopes[]
}

export type PermissionItem = Record<number, suportedScopes>

export type PermissionGroup = Record<string, Permission[]>

export type RolePermission = Record<number, suportedScopes>

export const usePermissionStore = defineStore('permission', () => {
	const permissionGroup = ref<PermissionGroup>()

	const isFetched = ref<boolean>(false)

	const permissionFetch = async () => {
		if (isFetched.value) return

		const response = await api.get('permission/index')
		isFetched.value = true
		permissionGroup.value = response.data?.data
	}
	return { permissionFetch, permissionGroup }
})
