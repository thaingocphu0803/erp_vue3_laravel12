/**
 * Position related types
 */

export interface Position {
	id: number
	name: string
}

export interface PositionFormData {
	name: string
	department_id: number | null
	description: string
	parent_id: number | null
}

export interface PositionFormError {
	name: string
	department_id: string
	description: string
	parent_id: string
	getDepartmentList: string
	getPositionList: string
}

export interface PositionItem extends Position {
	department_name?: string
	status?: string
}

