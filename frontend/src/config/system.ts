import type { serverErrorCode } from '@/types/common'

export interface ErrorConfig {
	icon: string
	code: number
	title: string
}

export type ERROR_CONFIG = Record<string, ErrorConfig>

export type SERVER_ERROR = Record<string, serverErrorCode>

const SYSTEM = {
	ERROR_CONFIG: {
		'500': {
			icon: 'mdi-server-off',
			code: 500,
			title: 'common.serverError.internalServerError',
		},
		'404': {
			icon: 'mdi-magnify-close',
			code: 404,
			title: 'common.serverError.notFound',
		},
	} as ERROR_CONFIG,

	SERVER_ERROR: {
		INTERNAL_SERVER_ERROR: 500,
		NOT_FOUND: 404,
		UNAUTHORIZED: 401,
		UNPROCESSABLE_ENTITY: 422,
		TOO_MANY_REQUESTS: 429,
	} as SERVER_ERROR,
}

export default SYSTEM

