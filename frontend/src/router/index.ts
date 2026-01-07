import { useAuthStore } from '@/stores/auth'
import { createRouter, createWebHistory } from 'vue-router'
import Login from '@/views/auth/Login.vue'
import MainLayout from '@/views/MainLayout.vue'

const router = createRouter({
	history: createWebHistory(import.meta.env.BASE_URL),
	routes: [
		{
			path: '/login',
			component: Login,
			name: 'login',
			meta: { guestOnly: true },
		},
		{
			path: '/',
			component: MainLayout,
			redirect: { name: 'dashboard' },
			meta: { requiresAuth: true },
			children: [
				{
					path: 'dashboard',
					name: 'dashboard',
					component: () => import('@/views/main/Dashboard.vue'),
				},
				{
					path: 'attendance',
					name: 'attendance',
					component: () => import('@/views/main/Attendance.vue'),
				},
				{
					path: 'employee',
					name: 'employee',
					component: () => import('@/views/main/Employee.vue'),
				},
				{
					path: 'profile',
					name: 'profile',
					component: () => import('@/views/main/Profile.vue'),
				},
				{
					path: 'message',
					name: 'message',
					component: () => import('@/views/main/Message.vue'),
				},
				{
					path: 'setting',
					name: 'setting',
					component: () => import('@/views/main/Setting.vue'),
				},
			],
		},
	],
})

router.beforeEach(async (to, from) => {
	const authStore = useAuthStore()

	await authStore.authFetch()

	if (to.meta.requiresAuth && !authStore.isLoggedin) {
		return { name: 'login', query: { redirect: to.fullPath } }
	}

	if (to.meta.guestOnly && authStore.isLoggedin) {
		return { name: 'dashboard' }
	}
})

export default router

