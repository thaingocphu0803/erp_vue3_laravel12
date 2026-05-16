import type { commonGender, commonLocale, commonStatus, FilterParams } from './common'

/**
 * Employee related types
 */
export interface EmployeeItem {
	id: number
	name: string
	avatar: string
	email: string
	code: string
	department_name: string
	position_name: string
	created_by: string
}

export interface EmployeeFormData {
	avatar: File | null
	email: string
	role_ids: number[]
	name: string
	code: string
	gender: commonGender | null
	birth_date: string | null
	phone_number: string
	province_code: string | null
	ward_code: string | null
	address: string
	department_id: number | null
	position_id: number | null
	is_leader: boolean
	locale: commonLocale
}

export interface EmployeeFormError {
	avatar: string
	email: string
	name: string
	role_ids: string
	department_id: string
	position_id: string
	gender: string
	birth_date: string
	phone_number: string
	province_code: string
	ward_code: string
	address: string
	is_leader: string
	code: string
	locale: string
}

export interface EmployeeFilterParams extends FilterParams {
	department_id: number | null
	position_id: number | null
}

