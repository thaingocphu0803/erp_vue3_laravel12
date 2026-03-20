import { useAuthStore } from '@/stores/auth'
import { createRouter, createWebHistory } from 'vue-router'
import Login from '@/views/auth/Login.vue'
import MainLayout from '@/views/MainLayout.vue'
import RouteViewLayout from '@/views/RouteViewLayout.vue'

const router = createRouter({
	history: createWebHistory(import.meta.env.BASE_URL),
	routes: [
		{
			path: '/login',
			component: Login,
			name: 'login',
			meta: { guestOnly: true, title: 'auth.title.login' },
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
					meta: { title: 'common.module.dashboard' },
				},
				{
					path: 'attendance',
					name: 'attendance',
					component: () => import('@/views/main/Attendance.vue'),
					meta: { title: 'common.module.attendance' },
				},
				{
					path: '',
					redirect: { name: 'org.department' },
					meta: { title: 'common.module.organization', disabled: true },
					children: [
						{
							path: 'position',
							name: 'org.position',
							component: RouteViewLayout,
							redirect: { name: 'org.position.index' },
							meta: { title: 'common.subModule.position' },
							children: [
								{
									path: '',
									name: 'org.position.index',
									component: () =>
										import('@/views/main/organization/position/Index.vue'),
								},
								{
									path: 'create',
									name: 'org.position.create',
									component: () =>
										import('@/views/main/organization/position/Create.vue'),
									meta: { title: 'common.action.position.create' },
								},
							],
						},
						{
							path: 'role',
							name: 'org.role',
							component: RouteViewLayout,
							redirect: { name: 'org.role.index' },
							meta: { title: 'common.subModule.role' },
							children: [
								{
									path: '',
									name: 'org.role.index',
									component: () =>
										import('@/views/main/organization/role/Index.vue'),
								},
								{
									path: 'create',
									name: 'org.role.create',
									component: () =>
										import('@/views/main/organization/role/Create.vue'),
									meta: { title: 'common.action.role.create' },
								},
							],
						},
						{
							path: 'department',
							name: 'org.department',
							redirect: { name: 'org.department.index' },
							component: RouteViewLayout,
							meta: { title: 'common.subModule.department' },
							children: [
								{
									path: '',
									name: 'org.department.index',
									component: () =>
										import('@/views/main/organization/department/Index.vue'),
								},
								{
									path: 'create',
									name: 'org.department.create',
									component: () =>
										import('@/views/main/organization/department/Create.vue'),
									meta: { title: 'common.action.department.create' },
								},
							],
						},
					],
				},
				{
					path: '',
					redirect: { name: 'hr.employee' },
					meta: { title: 'common.module.humanResource', disabled: true },
					children: [
						{
							path: 'employee',
							name: 'hr.employee',
							component: () => import('@/views/main/humanResource/employee/List.vue'),
							meta: { title: 'common.subModule.employee' },
						},
					],
				},
				{
					path: 'profile',
					name: 'profile',
					component: () => import('@/views/main/Profile.vue'),
					meta: { title: 'common.module.myProfile' },
				},
				{
					path: 'message',
					name: 'message',
					component: () => import('@/views/main/Message.vue'),
					meta: { title: 'common.module.message' },
				},
				{
					path: 'setting',
					name: 'setting',
					component: () => import('@/views/main/Setting.vue'),
					meta: { title: 'common.module.setting' },
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
