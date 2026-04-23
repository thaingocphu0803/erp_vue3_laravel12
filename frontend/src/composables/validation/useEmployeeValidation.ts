import CONFIG from '@/config/constants'
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

/**
 * Composable for Employee Validation Rules
 */
export default function useEmployeeValidation() {
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
			required: 'employee.validate.birthDate.required',
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
		/** Validation for email input */
		email: [required(messages.email.required), email(messages.email.format)],

		/** Validation for role selection */
		role: [
			isArray(messages.role.format),
			atLeastOne(messages.role.atLeastOne),
			distinct(messages.role.distinct),
		],

		/** Validation for department selection */
		department: [required(messages.department.required)],

		/** Validation for position selection */
		position: [required(messages.position.required)],

		/** Validation for employee name */
		name: [required(messages.name.required), noSpecialChars(messages.name.noSpecialChars)],

		/** Validation for gender */
		gender: [required(messages.gender.required)],

		/** Validation for birth date */
		birthDate: [required(messages.birthDate.required)],

		/** Validation for phone number */
		phone: [
			required(messages.phone.required),
			phone(messages.phone.format),
			size(messages.phone.size, CONFIG.sizePhone),
		],

		/** Validation for province */
		province: [required(messages.province.required)],

		/** Validation for ward */
		ward: [required(messages.ward.required)],

		/** Validation for address */
		address: [required(messages.address.required)],

		/** Validation for avatar file */
		avatar: [
			imageType(messages.avatar.format, CONFIG.validTypesAvatar),
			imageSize(messages.avatar.size, CONFIG.maxSizeAvatar),
		],
	}

	return { employeeValidation }
}


