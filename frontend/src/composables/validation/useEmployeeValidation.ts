import defaultConfig from '@/config/default'
import {
	atLeastOne,
	distinct,
	email,
	imageSize,
	imageType,
	isArray,
	noSpecialChars,
	phone,
	required,
	size,
} from '@/utils/validationRule'

const messages = {
	email: {
		required: 'employee.validate.email.required',
		format: 'employee.validate.email.format',
	},
	name: {
		required: 'employee.validate.name.required',
		noSpecialChars: 'employee.validate.name.noSpecialChars',
	},
	phone: {
		required: 'employee.validate.phoneNumber.required',
		size: 'employee.validate.phoneNumber.size',
		format: 'employee.validate.phoneNumber.format',
	},
	role: {
		format: 'employee.validate.roleId.format',
		atLeastOne: 'employee.validate.roleId.atLeastOne',
		distinct: 'employee.validate.roleId.distinct',
	},
	department: {
		required: 'employee.validate.departmentId.required',
	},
	position: {
		required: 'employee.validate.positionId.required',
	},
	gender: {
		required: 'employee.validate.gender.required',
	},
	birthDate: {
		required: 'employee.validate.birthdate.required',
	},
	province: {
		required: 'employee.validate.provinceCode.required',
	},
	ward: {
		required: 'employee.validate.wardCode.required',
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
	email: [required(messages.email.required), email(messages.email.format)],
	role: [
		isArray(messages.role.format),
		atLeastOne(messages.role.atLeastOne),
		distinct(messages.role.distinct),
	],
	department: [required(messages.department.required)],
	position: [required(messages.position.required)],
	name: [required(messages.name.required), noSpecialChars(messages.name.noSpecialChars)],
	gender: [required(messages.gender.required)],
	birthDate: [required(messages.birthDate.required)],
	phone: [
		required(messages.phone.required),
		phone(messages.phone.format),
		size(messages.phone.size, defaultConfig.sizePhone),
	],
	province: [required(messages.province.required)],
	ward: [required(messages.ward.required)],
	address: [required(messages.address.required)],
	avatar: [
		imageType(messages.avatar.format, defaultConfig.validTypesAvatar),
		imageSize(messages.avatar.size, defaultConfig.maxSizeAvatar),
	],
}

export default employeeValidation

