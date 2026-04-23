interface ERROR_CONFIG {
	icon: string
	code: number
	title: string
}

type SERVER_ERROR_CODE = 500 | 404 | 401 | 422 | 429

type ERROR_MAP = Record<string, ERROR_CONFIG>

type SERVER_ERROR = Record<string, SERVER_ERROR_CODE>

const SYSTEM = {
	ERROR_MAP: {
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
	} as ERROR_MAP,

	SERVER_ERROR: {
		INTERNAL_SERVER_ERROR: 500,
		NOT_FOUND: 404,
		UNAUTHORIZED: 401,
		UNPROCESSABLE_ENTITY: 422,
		TOO_MANY_REQUESTS: 429,
	} as SERVER_ERROR,
}

export default SYSTEM

