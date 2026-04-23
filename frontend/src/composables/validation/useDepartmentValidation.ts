import { required, noSpecialChars } from '@/utils/validationRule'

/**
 * Composable for Department Validation Rules
 */
export default function useDepartmentValidation() {
	const messages = {
		name: {
			required: 'department.validate.name.required',
			noSpecialChars: 'department.validate.name.noSpecialChars',
		},
	}

	const departmentValidation = {
		/** Validation for department name input (required + alphanumeric only) */
		name: [required(messages.name.required), noSpecialChars(messages.name.noSpecialChars)],
	}

	return { departmentValidation }
}

