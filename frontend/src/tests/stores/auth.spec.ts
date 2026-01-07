import { useAuthStore } from '@/stores/auth'
import { createPinia, setActivePinia } from 'pinia'
import { beforeEach, describe, expect, it, vi } from 'vitest'

vi.mock('@/services/api', () => ({
	default: {
		baseURL: '/api',
		get: vi.fn(),
		post: vi.fn(),
		interceptors: {
			response: {
				use: vi.fn(),
				reject: vi.fn(),
			},
		},
	},
}))

vi.mock('@/router', () => ({
	default: {
		push: vi.fn(),
	},
}))
describe('pinia auth store', () => {
	beforeEach(() => {
		setActivePinia(createPinia())
		vi.clearAllMocks()
	})
	it('initial state', () => {
		const authStore = useAuthStore()

		expect(authStore.user).toBeNull()
		expect(authStore.isInitialized).toBeFalsy()
		expect(authStore.isLoggedin).toBeFalsy()
	})
})
