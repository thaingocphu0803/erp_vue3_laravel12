<script setup lang="ts">
import BaseIconBtn from '../BaseIconBtn.vue'
import { languageMenu } from '@/config/menu'
import { useAuthStore } from '@/stores/auth'
import { useLocaleStore } from '@/stores/locale'

const authStore = useAuthStore()
const { setLocale } = useLocaleStore()
const handleLogout = async () => await authStore.authLogout()
</script>

<template>
	<v-menu location="bottom" transition="slide-y-transition">
		<!-- Activator -->
		<template v-slot:activator="{ props: menuProps }">
			<base-icon-btn
				icon="mdi-dots-vertical"
				class="d-sm-none"
				v-bind="menuProps"
			></base-icon-btn>
		</template>

		<v-list>
			<!-- Group Menu -->
			<v-list-group value="languageBtn" @click.stop>
				<template v-slot:activator="{ props }">
					<v-list-item v-bind="props" :title="$t('common.button.language')"></v-list-item>
				</template>
				<!-- Sub Menu -->
				<v-list-item
					v-for="item in languageMenu"
					:key="item.value"
					:value="item.value"
					:title="$t(item.title)"
					@click="setLocale(item.value)"
					data-testId="mobile-language-item"
				>
				</v-list-item>
			</v-list-group>

			<!-- Single Menu -->
			<v-list-item
				value="logoutBtn"
				:title="$t('common.button.logout')"
				@click="handleLogout"
				data-testId="mobile-logout-item"
			></v-list-item>
		</v-list>
	</v-menu>
</template>

