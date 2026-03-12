import { required, noSpecialChars } from '@/utils/validationRule'

const messages = {
	name: {
		required: 'department.validate.name.required',
		noSpecialChars: 'department.validate.name.noSpecialChars',
	},
}

const departmentValidation = {
	// validation department name input (required + alphanumeric only)
	name: [
		required(messages.name.required),
		noSpecialChars(messages.name.noSpecialChars),
	],
}

export default departmentValidation
