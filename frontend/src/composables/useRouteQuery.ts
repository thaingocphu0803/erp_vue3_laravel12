import { useRoute, useRouter } from 'vue-router'

export const useRouteQuery = () => {
	const route = useRoute()
	const router = useRouter()

	const updateQueryParams = (params: Record<string, any>) => {
		const query = { ...route.query }

		for (const key in params) {
			if (params[key] === undefined || params[key] === null || params[key] === '') {
				delete query[key]
			} else {
				query[key] = String(params[key])
			}
		}

		router.push({ query })

		return query
	}

	const clearQueryParams = () => {
		router.push({ query: {} })
		return {}
	}

	const replaceQueryParams = (params: Record<string, any>) => {
		const query: Record<string, string> = {}
		for (const key in params) {
			if (params[key] !== undefined && params[key] !== null && params[key] !== '') {
				query[key] = String(params[key])
			}
		}
		router.push({ query })
		return query
	}

	return { updateQueryParams, clearQueryParams, replaceQueryParams }
}
