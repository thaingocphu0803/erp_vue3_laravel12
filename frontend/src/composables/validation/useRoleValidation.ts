import { required, noSpecialChars } from '@/utils/validationRule'

/**
 * Composable for Role Validation Rules
 */
export default function useRoleValidation() {
	const messages = {
		name: {
			required: 'role.validate.name.required',
			noSpecialChars: 'role.validate.name.noSpecialChars',
		},
	}

	const roleValidation = {
		/** Validation for role name input (required + alphanumeric only) */
		name: [required(messages.name.required), noSpecialChars(messages.name.noSpecialChars)],
	}

	return { roleValidation }
}

