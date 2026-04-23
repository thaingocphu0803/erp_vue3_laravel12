<script setup lang="ts">
import { watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRoute } from 'vue-router'
import CONFIG from './config/constants'
const { t, locale } = useI18n()
const route = useRoute()

// watch route change
watch(
	[locale, () => route.path],
	() => {
		const baseTitle = CONFIG.appName
		const metaTitle = route.meta.title ? t(route.meta.title as string) : ''
		document.title = metaTitle ? `${metaTitle} - ${baseTitle}` : baseTitle
	},
	{ immediate: true },
)
</script>

<template>
	<!-- App -->
	<v-app>
		<!-- Router View -->
		<router-view />
	</v-app>
</template>
