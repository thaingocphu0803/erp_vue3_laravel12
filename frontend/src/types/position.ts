/**
 * Position related types
 */

import type { commonStatus, FilterParams } from './common'

export interface Position {
	id: number
	name: string
}

export interface PositionFormData {
	name: string
	department_id: number | null
	description: string
	parent_id: number | null
	code: string
}

export interface PositionFormError {
	name: string
	department_id: string
	description: string
	parent_id: string
	code: string
	getDepartmentList: string
	getPositionList: string
}

export interface PositionItem extends Position {
	code: string
	employees_count: number
	department_name: string
	parent_name: string
	created_by: string
	status: commonStatus
}

export interface PositionFilterParams extends FilterParams {
	department: number | null
}

