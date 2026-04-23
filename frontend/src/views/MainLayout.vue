<script setup lang="ts">
import LayoutBar from '@/components/layout/LayoutBar.vue'
import LayoutNav from '@/components/layout/LayoutNav.vue'
import NotificationBtn from '@/components/layout/NotificationBtn.vue'
import LanguageBtn from '@/components/layout/LanguageBtn.vue'
import ThemeSwitch from '@/components/ThemeSwitch.vue'
import BaseBtn from '@/components/BaseBtn.vue'
import MobileMenuBtn from '@/components/layout/MobileMenuBtn.vue'
import { useAuthStore } from '@/stores/auth'
import AppToast from '@/components/layout/AppToast.vue'
import { ref } from 'vue'

const logoutBtnTitle: string = 'common.button.logout'

const authStore = useAuthStore()

const isOpen = ref<boolean>()

// handle logout
const handleLogout = async () => await authStore.authLogout()

// handle navigation display
const handleNavDisplay = () => {
	isOpen.value = !isOpen.value
}
</script>

<template>
	<!-- Main Layout -->
	<v-layout>
		<!-- Layout Bar -->
		<layout-bar text-color="text-primary">
			<!-- Icon -->
			<template #icon>
				<v-app-bar-nav-icon
					class="d-md-none"
					color="primary"
					@click="handleNavDisplay"
				></v-app-bar-nav-icon>
			</template>

			<!-- Notification Button -->
			<notification-btn />

			<!-- Language Button -->
			<language-btn />

			<!-- Theme Switch -->
			<theme-switch color="primary" base-color="primary" />

			<!-- Logout Button -->
			<base-btn
				:title="logoutBtnTitle"
				color="primary"
				append-icon="mdi-logout"
				@click="handleLogout"
				class="text-capitalize d-none d-sm-flex"
			>
			</base-btn>

			<!-- Mobile Menu Button -->
			<mobile-menu-btn />
		</layout-bar>

		<!-- Layout Navigation -->
		<layout-nav v-model="isOpen"></layout-nav>

		<!-- Main Content -->
		<v-main>
			<!-- Router View -->
			<router-view></router-view>

			<!-- Toast -->
			<app-toast />
		</v-main>
	</v-layout>
</template>

