import { mount, VueWrapper } from '@vue/test-utils'
import { beforeEach, describe, expect, it } from 'vitest'
import LayoutBar from '@/components/layout/LayoutBar'
import defaultConfig from '@/config/default'

describe('LayoutBar.vue', () => {
	let wrapper: VueWrapper<any>

	beforeEach(() => {
		wrapper = mount(LayoutBar, {
			global: {
				stubs: {
					'v-app-bar': {
						template: '<div><slot/></div>',
					},
					'v-app-bar-title': {
						props: ['textColor'],
						template: '<div :class="textColor"><slot/></div>',
					},
				},
			},
			props: {
				textColor: 'primary',
			},
		})
	})

	it('render app name', () => {
		expect(wrapper.text()).toContain(defaultConfig.appName)
	})

	it('receive correct textColor Prop', () => {
		const barTitle = wrapper.find('[data-testId="app-bar-title"]')
		expect(barTitle.classes()).toContain('primary')
	})
})

