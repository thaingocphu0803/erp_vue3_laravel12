import type { commonStatus, suportedScopes } from './common'

/**
 * Role and Permission related types
 */

export interface Role {
	id: number
	name: string
}

export interface RoleFormData {
	name: string
	code: string
	description: string
	permissions: RolePermission
}

export interface RoleFormError {
	name: string
	code: string
	description: string
	permissions: string
}

export interface Permission {
	id: number
	name: string
	slug: string
	supported_scopes: suportedScopes[]
}

export interface RoleItem extends Role {
	created_at: string
	created_by: string
	status: commonStatus
}

export type PermissionGroup = Record<string, Permission[]>

export type RolePermission = Record<number, suportedScopes>

