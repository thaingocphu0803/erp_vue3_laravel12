import router from '@/router'
import api from '@/services/api'
import { useAuthStore } from '@/stores/auth'
import { email } from '@/utils/validationRule'
import { createPinia, setActivePinia } from 'pinia'
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest'

// mock api service
vi.mock('@/services/api', () => ({
	default: {
		get: vi.fn(),
		post: vi.fn(),
	},
}))

// mock router
vi.mock('@/router', () => ({
	default: {
		push: vi.fn(),
	},
}))

describe('pinia auth store', () => {
	let authStore: ReturnType<typeof useAuthStore>

	beforeEach(() => {
		setActivePinia(createPinia())
		authStore = useAuthStore()
		vi.clearAllMocks()
	})

	afterEach(() => {
		vi.restoreAllMocks()
	})

	it('initial state', () => {
		expect(authStore.user).toBeNull()
		expect(authStore.isInitialized).toBeFalsy()
		expect(authStore.isLoggedin).toBeFalsy()
	})

	describe('login', () => {
		it('login successfully', async () => {
			const mockUser = { name: 'test', email: 'test@gmail.com' }

			vi.mocked(api.post).mockResolvedValue({
				data: { data: { user: mockUser } },
			})

			await authStore.authLogin({ email: 'test@gmail.com', password: 'Test123@@' })

			expect(authStore.user).toEqual(mockUser)
			expect(authStore.isInitialized).toBeTruthy()
			expect(authStore.isLoggedin).toBeTruthy()
		})

		it('login fail', async () => {
			vi.mocked(api.post).mockRejectedValue(new Error('login fail'))

			await expect(
				authStore.authLogin({ email: 'test@gmail.com', password: 'Test123@@' }),
			).rejects.toThrow()

			expect(authStore.user).toBeNull()
			expect(authStore.isInitialized).toBeFalsy()
			expect(authStore.isLoggedin).toBeFalsy()
		})
	})

	describe('fetch auth', () => {
		it('fetch auth successfully', async () => {
			const mockUser = { name: 'test', email: 'test@gmail.com' }

			vi.mocked(api.get).mockResolvedValue({
				data: { data: { user: mockUser } },
			})

			await authStore.authFetch()

			expect(authStore.user).toEqual(mockUser)
			expect(authStore.isInitialized).toBeTruthy()
			expect(authStore.isLoggedin).toBeTruthy()
		})

		it('fetch auth failed', async () => {
			vi.mocked(api.get).mockRejectedValue(new Error('fetch auth fail'))
			const logSpy = vi.spyOn(console, 'log')

			await authStore.authFetch()

			expect(logSpy).toHaveBeenCalledWith(
				expect.stringContaining('fetch user api error'),
				expect.anything(),
			)
			expect(authStore.user).toBeNull()
			expect(authStore.isInitialized).toBeTruthy()
			expect(authStore.isLoggedin).toBeFalsy()
		})
	})

	describe('logout', () => {
		it('logout successfully', async () => {
			vi.mocked(api.post).mockResolvedValue({ status: 200 })

			await authStore.authLogout()

			expect(authStore.user).toBeNull()
			expect(authStore.isLoggedin).toBeFalsy()
			expect(router.push).toHaveBeenCalledWith({ name: 'login' })
		})

		it('logout failed', async () => {
			vi.mocked(api.post).mockRejectedValue(new Error('logout fail'))

			const logSpy = vi.spyOn(console, 'log')

			await authStore.authLogout()

			expect(logSpy).toHaveBeenCalledWith(
				expect.stringContaining('Logout api error'),
				expect.anything(),
			)
		})
	})
})

