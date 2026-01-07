import { mount } from '@vue/test-utils'
import LanguageBtn from '../../../components/layout/LanguageBtn.vue'
import { describe, expect, it, vi } from 'vitest'
import { changeLanguage } from '@/composables/useLanguage'

vi.mock('@/composables/useLanguage', () => ({
	changeLanguage: vi.fn(),
}))

describe('LanguageBtn Layout component', () => {
	it('trigger changeLanguage function when clicking', async () => {
		const wrapper = mount(LanguageBtn, {
			global: {
				mocks: {
					$t: (key: string) => `Translate ${key}`,
				},
				stubs: {
					'v-menu': {
						template: '<div><slot name="activator" :props="{}"></slot><slot/></div>',
					},
					'base-icon-btn': true,
					'v-list': { template: '<ul><slot /></ul>' },
					'v-list-item': {
						props: ['title', 'value'],
						template: '<li @click="$emit(\'click\')">{{title}}</li>',
					},
				},
			},
		})

		// test whether BaseIconBtn component is received correct tooltip
		const iconBtn = wrapper.findComponent({ name: 'BaseIconBtn' })
		expect(iconBtn.props('tooltip')).toBe('common.button.language')

		// test whether list had 2 list item
		const items = wrapper.findAll('li')
		expect(items.length).toEqual(2)
		expect(items[0].text()).toContain('Translate ')

		// test whether changeLanguage function is called
		await items[0].trigger('click')
		expect(changeLanguage).toHaveBeenCalled()
	})
})
