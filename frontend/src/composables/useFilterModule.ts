import { t } from '@/plugins/vueI18n'
import type { commonStatus, commonGender, commonLocale } from '@/types/common'
import { computed } from 'vue'

interface Status {
	name: string
	id: commonStatus
	color: 'success' | 'error'
}

interface Gender {
	name: string
	id: commonGender
}

interface Locale {
	name: string
	id: commonLocale
}

export const useFilterModule = () => {
	// Status filter
	const statuses = computed((): Status[] => [
		{ name: t('common.status.active'), id: 'A', color: 'success' },
		{ name: t('common.status.inActive'), id: 'X', color: 'error' },
	])

	// Status Map filter
	const statusMap = computed(
		(): Record<string, Status> => Object.fromEntries(statuses.value.map((s) => [s.id, s])),
	)

	// Gender filter
	const genders = computed((): Gender[] => [
		{ name: t('common.gender.male'), id: 'MALE' },
		{ name: t('common.gender.female'), id: 'FEMALE' },
	])

	// Locale filter
	const locales = computed((): Locale[] => [
		{ name: t('common.language.english'), id: 'en' },
		{ name: t('common.language.vietnamese'), id: 'vi' },
	])

	return { statuses, statusMap, genders, locales }
}
