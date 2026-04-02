import defaultConfig from "@/config/default"
import { email, imageSize, imageType, minLength, noSpecialChars, phone, required } from "@/utils/validationRule"

const messages = {
	email: {
		required: 'employee.validate.email.required',
		format	: 'employee.validate.email.format',
	},
	fullName: {
		required: 'employee.validate.fullName.required',
		noSpecialChars: 'employee.validate.fullName.noSpecialChars',
	},
	phone: {
		required: 'employee.validate.phone.required',
		min: 'employee.validate.phone.min',
		format: 'employee.validate.phone.format',
	},
	role: {
		required: 'employee.validate.role.required',
	},
	department: {
		required: 'employee.validate.department.required',
	},
	position: {
		required: 'employee.validate.position.required',
	},
	gender: {
		required: 'employee.validate.gender.required',
	},
	birthDate: {
		required: 'employee.validate.birthDate.required',
	},
	province: {
		required: 'employee.validate.province.required',
	},
	ward: {
		required: 'employee.validate.ward.required',
	},
	address: {
		required: 'employee.validate.address.required',
	},
	avatar: {
		size: 'employee.validate.avatar.size',
		format: 'employee.validate.avatar.format',
	},
}

const employeeValidation = {
	email: [
		required(messages.email.required),
		email(messages.email.format),
	],
	role: [
		required(messages.role.required),
	],
	department: [
		required(messages.department.required),
	],
	position: [
		required(messages.position.required),
	],
	fullName: [
		required(messages.fullName.required),
		noSpecialChars(messages.fullName.noSpecialChars),
	],
	gender: [
		required(messages.gender.required),
	],
	birthDate: [
		required(messages.birthDate.required),
	],
	phone: [
		required(messages.phone.required),
		phone(messages.phone.format),
		minLength(messages.phone.min, defaultConfig.minLengthPhone),
	],
	province: [
		required(messages.province.required),
	],
	ward: [
		required(messages.ward.required),
	],
	address: [
		required(messages.address.required),
	],
	avatar: [
		imageType(messages.avatar.format, defaultConfig.validTypesAvatar),
		imageSize(messages.avatar.size, defaultConfig.maxSizeAvatar),
	],
}

export default employeeValidation