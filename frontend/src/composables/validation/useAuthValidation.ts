import CONFIG from '@/config/constants'
import {
	email,
	hasLowerLetter,
	hasNumber,
	hasSpecialChar,
	hasUpperLetter,
	minLength,
	required,
	sameAs,
} from '@/utils/validationRule'

/**
 * Composable for Authentication Validation Rules
 * Provides validation rules for name, email, password, and password confirmation.
 */
export default function useAuthValidation() {
	const messages = {
		email: {
			required: 'auth.validate.email.required',
			format: 'auth.validate.email.format',
		},
		name: {
			required: 'auth.validate.name.required',
		},
		confirmPassword: {
			required: 'auth.validate.confirmPassword.required',
			confirm: 'auth.validate.confirmPassword.confirmed',
		},
		password: {
			required: 'auth.validate.password.required',
			hasUpperLetter: 'auth.validate.password.hasUpperLetter',
			hasLowerLetter: 'auth.validate.password.hasLowerLetter',
			min: 'auth.validate.password.min',
			hasNumber: 'auth.validate.password.hasNumber',
			hasSpecialChar: 'auth.validate.password.hasSpecialChar',
		},
	}

	const authValidation = {
		/** Validation for name input */
		name: [required(messages.name.required)],

		/** Validation for email input */
		email: [required(messages.email.required), email(messages.email.format)],

		/** Validation for password input */
		password: [
			required(messages.password.required),
			minLength(messages.password.min, CONFIG.minLengthPassword),
			hasLowerLetter(messages.password.hasLowerLetter),
			hasUpperLetter(messages.password.hasUpperLetter),
			hasNumber(messages.password.hasNumber),
			hasSpecialChar(messages.password.hasSpecialChar),
		],

		/**
		 * Validation for confirm password input
		 * @param password - The password to compare with
		 */
		passwordConfirm: (password: string) => [
			required(messages.confirmPassword.required),
			sameAs(messages.confirmPassword.confirm, password),
		],
	}

	return { authValidation }
}

