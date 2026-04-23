import { required, noSpecialChars } from '@/utils/validationRule'

/**
 * Composable for Position Validation Rules
 */
export default function usePositionValidation() {
	const messages = {
		name: {
			required: 'position.validate.name.required',
			noSpecialChars: 'position.validate.name.noSpecialChars',
		},
	}

	const positionValidation = {
		/** Validation for position name input (required + alphanumeric only) */
		name: [required(messages.name.required), noSpecialChars(messages.name.noSpecialChars)],
	}

	return { positionValidation }
}
