import CONFIG from '@/config/constants'

// map laravel error to target
export const mapLaravelError = (target: any, error: any) => {
	const serverError = error.response?.data?.errors

	if (!serverError) return

	Object.keys(target).forEach((key) => {
		if (key in serverError) {
			target[key] = serverError[key][0]
		} else {
			target[key] = ''
		}
	})
}

export const formatLaravelRetryAfter = (error: any) => {
	const retryAfter = error.response?.headers['retry-after']

	return retryAfter ? Number(retryAfter) : CONFIG.retryAfter
}

