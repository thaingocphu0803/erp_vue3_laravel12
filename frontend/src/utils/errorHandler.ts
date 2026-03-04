export const mapLaravelError = (target: any, error: any) => {
	const serverError = error.response?.data?.errors

	if (!serverError) return

	Object.keys(serverError).forEach((key) => {
		if (key in target) {
			target[key] = serverError[key][0]
		}
	})
}
