import type { PropertyAny, PropertyString } from '@/types/common'
import { useRoute, useRouter } from 'vue-router'

export const useRouteQuery = () => {
	const route = useRoute()
	const router = useRouter()

	const updateQueryParams = (params: PropertyAny) => {
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

	const replaceQueryParams = (params: PropertyAny) => {
		const query: PropertyString = {}
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
