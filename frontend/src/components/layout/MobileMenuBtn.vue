<script setup lang="ts">
import BaseIconBtn from '../BaseIconBtn.vue'
import { languageMenu } from '@/config/menu'
import { useAuthStore } from '@/stores/auth'
import { changeLanguage } from '@/composables/useLanguage'

const authStore = useAuthStore()
const handleLogout = async () => await authStore.authLogout()
</script>

<template>
	<v-menu location="bottom" transition="slide-y-transition">
		<template v-slot:activator="{ props: menuProps }">
			<base-icon-btn
				icon="mdi-dots-vertical"
				class="d-sm-none"
				v-bind="menuProps"
			></base-icon-btn>
		</template>

		<v-list>
			<v-list-group value="languageBtn" @click.stop>
				<template v-slot:activator="{ props }">
					<v-list-item v-bind="props" :title="$t('common.button.language')"></v-list-item>
				</template>
				<v-list-item
					v-for="item in languageMenu"
					:key="item.value"
					:value="item.value"
					:title="$t(item.title)"
					@click="changeLanguage(item.value)"
				>
				</v-list-item>
			</v-list-group>

			<v-list-item
				value="logoutBtn"
				:title="$t('common.button.logout')"
				@click="handleLogout"
			></v-list-item>
		</v-list>
	</v-menu>
</template>

