import { required, noSpecialChars } from '@/utils/validationRule'

const messages = {
	name: {
		required: 'role.validate.name.required',
		noSpecialChars: 'role.validate.name.noSpecialChars',
	},
}

const roleValidation = {
	// validation department name input (required + alphanumeric only)
	name: [
		required(messages.name.required),
		noSpecialChars(messages.name.noSpecialChars),
	],
}

export default roleValidation
