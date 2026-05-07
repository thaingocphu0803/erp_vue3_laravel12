export type commonStatus = 'A' | 'X'

export type bulkActionStatus = commonStatus | 'D'

export type suportedScopes = 'ALL' | 'DEPT' | 'OWN' | 'NONE'

export type commonGender = 'MALE' | 'FEMALE'

export type commonLocale = 'vi' | 'en'

export type PropertyString = Record<string, string>

export type PropertyAny = Record<string, any>

export type SortOrder = 'asc' | 'desc'

export interface FilterParams {
	status: commonStatus | null
	search: string
	itemsPerPage: number
	page: number
	sortKey: string | null
	sortOrder: SortOrder | null
}

