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


const messages = {
	email: {
		required: 'auth.validate.email.required',
		format: 'auth.validate.email.format'
	},
	name: {
		required:'auth.validate.name.required',
	},
	confirmPassword: {
		required:'auth.validate.confirmPassword.required',
		confirm:'auth.validate.confirmPassword.confirm',
	},
	password: {
		required:'auth.validate.password.required',
		hasUpperLetter:'auth.validate.password.hasUpperLetter',
		hasLowerLetter:'auth.validate.password.hasLowerLetter',
		min:'auth.validate.password.min',
		hasNumber:'auth.validate.password.hasNumber',
		hasSpecialChar:'auth.validate.password.hasSpecialChar',
	},
}

// initial authValidation
const authValidation = {
	//validation name input
	name: [required(messages.name.required)],

	// validation email input
	email: [required(messages.email.required), email(messages.email.format)],

	// validation password input
	password: [
		required(messages.password.required),
		minLength(messages.password.min, 8),
		hasLowerLetter(messages.password.hasLowerLetter),
		hasUpperLetter(messages.password.hasUpperLetter),
		hasNumber(messages.password.hasNumber),
		hasSpecialChar(messages.password.hasSpecialChar),
	],

	// validation confirm password input
	passwordConfirm: (password: string) => [
		required(messages.confirmPassword.required),
		sameAs(messages.confirmPassword.confirm, password),
	],
}

export default authValidation

