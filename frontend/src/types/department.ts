/**
 * Department related types
 */

import type { commonStatus, FilterParams } from './common'

export interface Department {
	id: number
	name: string
	leader_id: number | null
}

export interface DepartmentFormData {
	name: string
	code: string | null
	parent_id: number | null
	description: string
}

export interface DepartmentFormError {
	name: string
	code: string
	parent_id: string
	description: string
	getDepartmentList: string
}

export interface DepartmentItem extends Department {
	code: string
	employees_count: number
	parent_name: string
	leader_name: string
	created_by: string
	status: commonStatus
}

export interface DepartmentFilterParams extends FilterParams {}

