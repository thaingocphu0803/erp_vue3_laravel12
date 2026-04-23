import CONFIG from '@/config/constants'

// map laravel error to target
export const mapLaravelError = (target: any, error: any) => {
	const serverError = error.response?.data?.errors

	if (!serverError) return

	Object.keys(serverError).forEach((key) => {
		if (key in target) {
			target[key] = serverError[key][0]
		}
	})
}

export const formatLaravelRetryAfter = (error: any) => {
	const retryAfter = error.response?.headers['retry-after']

	return retryAfter ? Number(retryAfter) : CONFIG.retryAfter
}

