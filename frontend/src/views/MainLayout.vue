<script setup lang="ts">
import LayoutBar from '@/components/layout/LayoutBar.vue';
import LayoutNav from '@/components/layout/LayoutNav.vue';
import NotificationBtn from '@/components/layout/NotificationBtn.vue';
import LanguageBtn from '@/components/layout/LanguageBtn.vue';
import ThemeSwitch from '@/components/ThemeSwitch.vue';
import BaseBtn from '@/components/BaseBtn.vue';
import MobileMenuBtn from '@/components/layout/MobileMenuBtn.vue';
import { useAuthStore } from '@/stores/auth';
import { ref } from 'vue';

const logoutBtnTitle: string = 'common.button.logout'

const authStore = useAuthStore()

const isOpen = ref<boolean>()

const handleLogout = async () => await authStore.authLogout()

const handleNavDisplay = () => {
	isOpen.value = !isOpen.value
}

</script>

<template>
	<v-layout>
		<layout-bar text-color="text-primary">
			<template #icon>
				<v-app-bar-nav-icon class="d-sm-none" color="primary" @click="handleNavDisplay"></v-app-bar-nav-icon>
			</template>

			<notification-btn />

			<language-btn />

			<theme-switch color="primary" base-color="primary" />

			<base-btn :title="logoutBtnTitle" color="primary" append-icon="mdi-logout" @click="handleLogout"
				class="text-capitalize d-none d-sm-flex"></base-btn>
			<mobile-menu-btn />
		</layout-bar>

		<layout-nav v-model="isOpen"></layout-nav>

		<v-main>
			<router-view></router-view>
		</v-main>
	</v-layout>
</template>