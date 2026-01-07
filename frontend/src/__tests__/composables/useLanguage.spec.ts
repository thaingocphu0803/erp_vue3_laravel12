import { changeLanguage } from '@/composables/useLanguage'
import i18n from '@/plugins/vueI18n'
import { setLanguage } from '@/utils/language'
import { beforeEach, describe, expect, it, vi } from 'vitest'

vi.mock('@/plugins/vueI18n', () => ({
	default: {
		global: {
			locale: { value: 'en' },
		},
	},
}))

vi.mock('@/utils/language', () => ({
	setLanguage: vi.fn(),
}))
describe('changeLanguage function', () => {
	const i18nGlobal = i18n.global as { locale: { value: string } }

	beforeEach(() => {
		vi.clearAllMocks()
		i18nGlobal.locale.value = 'en'
	})

	it('not work if the locale is not a string', () => {
		const invalidLocale = [{}, null, undefined, 123]

		invalidLocale.forEach((locale: any) => {
			changeLanguage(locale)
		})

		expect(setLanguage).not.toHaveBeenCalled()
		expect(i18nGlobal.locale.value).toBe('en')
	})

	it('work if the locale is not a string', () => {
		const newLocale = 'vi'
		changeLanguage(newLocale)

		expect(setLanguage).toHaveBeenCalled()
		expect((i18n.global as any).locale.value).toBe(newLocale)
	})
})
