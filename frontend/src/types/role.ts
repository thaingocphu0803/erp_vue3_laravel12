import type { suportedScopes } from './common'

/**
 * Role and Permission related types
 */

export interface Role {
	id: number
	name: string
	slug: string
}

export interface RoleFormData {
	name: string
	description: string
	permissions: RolePermission
}

export interface RoleFormError {
	name: string
	permissions: string
	description: string
}

export interface Permission {
	id: number
	name: string
	slug: string
	supported_scopes: suportedScopes[]
}

export interface RoleItem extends Role {
	status?: string
}

export type PermissionGroup = Record<string, Permission[]>

export type RolePermission = Record<number, suportedScopes>

