import i18n from '@/plugins/vueI18n'
import { setLanguage } from '@/utils/language'

// change UI language following the locale
export const changeLanguage = (locale: string) => {
	if (typeof locale !== 'string') return

	setLanguage(locale)

	const global = i18n.global as any

	global.locale.value = locale
}

