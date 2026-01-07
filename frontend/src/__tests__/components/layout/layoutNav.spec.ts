import { it, expect, vi, describe, beforeEach } from 'vitest'
import { mount } from '@vue/test-utils'
import { createTestingPinia } from '@pinia/testing'
import LayoutNav from '../../../components/layout/LayoutNav.vue'

vi.mock('vuetify', async () => {
	const actual = await vi.importActual('vuetify')
	return {
		...actual,
		useDisplay: () => ({ sm: vi.fn(() => true) }),
	}
})

describe('LayoutNav Layout component', () => {
	let pinia: any

	beforeEach(() => {
		pinia = createTestingPinia({
			createSpy: vi.fn,
			stubActions: true,
			initialState: {
				auth: {
					user: { name: 'test', email: 'test@gmail.com', avatar: '' },
				},
			},
		})
	})

	it('render profile mà không bị loop', () => {
		const wrapper = mount(LayoutNav, {
			global: {
				plugins: [pinia],
				mocks: { $t: (key: string) => key },
				stubs: {
					'v-navigation-drawer': { template: '<nav><slot /></nav>' },
					'v-list': { template: '<ul><slot /></ul>' },
					'v-list-item': { template: '<li><slot /></li>' },
					'v-list-item-title': { template: '<span><slot /></span>' },
					'v-list-item-subtitle': { template: '<span><slot /></span>' },
					'v-divider': true,
				},
			},
		})

		expect(wrapper.text()).toContain('john doe')
		expect(wrapper.text()).toContain('john@gmail.com')

		expect(wrapper.text()).toContain('Dịch:menu.dashboard')

		const userTitle = wrapper.find('.text-capitalize')
		expect(userTitle.exists()).toBe(true)
	})
})
