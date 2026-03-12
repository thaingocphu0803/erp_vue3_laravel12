import { required, noSpecialChars, alphanumeric } from '@/utils/validationRule'

const messages = {
	name: {
		required: 'position.validate.name.required',
		noSpecialChars: 'position.validate.name.noSpecialChars',
	},
}

const positionValidation = {
	// validation department name input (required + alphanumeric only)
	name: [
		required(messages.name.required),
		noSpecialChars(messages.name.noSpecialChars),
	],
}

export default positionValidation
