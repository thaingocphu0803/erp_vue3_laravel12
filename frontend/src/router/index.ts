import { useAuthStore } from '@/stores/auth'
import { createRouter, createWebHistory } from 'vue-router'
  
const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/login',
      component: () => import('@/views/Login.vue'),
      name: 'login',
      meta: { guestOnly: true },
    },
    {
      path: '/register',
      component: () => import('@/views/Register.vue'),
      name: 'register',
      meta: { guestOnly: true },
    },
    {
      path: '/dashboard',
      alias: '/',
      component: () => import('@/views/Dashboard.vue'),
      name: 'dashboard',
      meta: { requiresAuth: true },
    },
  ],
})

router.beforeEach(async (to, from) => {
	const authStore = useAuthStore()

  if (!authStore.isInitialized) await authStore.authFetch()

  if (to.meta.requiresAuth && !authStore.isLoggedin) {
    return { name: 'login', query: {redirect: to.fullPath} }
  }

  if (to.meta.guestOnly && authStore.isLoggedin) {
    return { name: 'dashboard' }
  }
})

export default router
