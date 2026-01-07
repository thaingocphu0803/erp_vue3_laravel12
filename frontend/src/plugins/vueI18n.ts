import { createI18n, useI18n } from 'vue-i18n'
import { createVueI18nAdapter } from 'vuetify/locale/adapters/vue-i18n'
import { en, vi } from 'vuetify/locale'
import authEn from '@/languages/english/auth.json'
import authVi from '@/languages/vietnamese/auth.json'
import commonEn from '@/languages/english/common.json'
import commonVi from '@/languages/vietnamese/common.json'
import { getLanguage } from '@/utils/language'

const messages = {
	en: {
		$vuetify: { ...en },
		...authEn,
		...commonEn
	},
	vi: {
		$vuetify: { ...vi },
		...authVi,
		...commonVi
	},
}

const i18n = createI18n({
	legacy: false,
	locale: getLanguage(),
	fallbackLocale: 'vi',
	messages,
})

export const t = (key: string, params?: any) => i18n.global.t(key, params)

export const i18nLocale = {
	adapter: createVueI18nAdapter({ i18n, useI18n }),
}

export default i18n
