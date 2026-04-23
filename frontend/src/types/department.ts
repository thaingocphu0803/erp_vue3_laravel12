/**
 * Department related types
 */

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
	code?: string
	parent_name?: string
	status?: string
}

