import type { commonGender, commonLocale } from './common'

/**
 * Employee related types
 */

export interface EmployeeForm {
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

