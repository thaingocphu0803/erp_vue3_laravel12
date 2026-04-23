import { defineStore } from 'pinia'
import { ref, watch } from 'vue'
import i18n from '@/plugins/vueI18n'

/**
 * Store for handling application locale/language with persistence
 */
export const useLocaleStore = defineStore(
	'locale',
	() => {
		const locale = ref<string>('vi')

		/**
		 * Change application language
		 * @param newLocale - 'vi' | 'en'
		 */
		const setLocale = (newLocale: string) => {
			locale.value = newLocale
		}

		// Synchronize i18n instance when locale changes
		watch(
			locale,
			(newLocale) => {
				const global = i18n.global as any
				if (global.locale.value !== newLocale) {
					global.locale.value = newLocale
				}
			},
			{ immediate: true },
		)

		return {
			locale,
			setLocale,
		}
	},
	{
		persist: true, // Enable pinia-plugin-persistedstate
	},
)

