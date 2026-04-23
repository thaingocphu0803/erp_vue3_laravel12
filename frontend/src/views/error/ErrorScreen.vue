<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import LayoutBar from '@/components/layout/LayoutBar.vue'
import LanguageBtn from '@/components/layout/LanguageBtn.vue'
import ThemeSwitch from '@/components/ThemeSwitch.vue'
import { t } from '@/plugins/vueI18n'
import SYSTEM from '@/config/system'

const route = useRoute()
const router = useRouter()

const errorConfig = ref(SYSTEM.ERROR_MAP['404'])

const headline = computed(() => {
	return t('common.state.error') + ' ' + String(errorConfig.value?.code)
})

onMounted(() => {
	const queryCode = route.query?.code as string

	if (!queryCode.length) return

	if (SYSTEM.ERROR_MAP[queryCode] === undefined) return

	errorConfig.value = SYSTEM.ERROR_MAP[queryCode]
})

const handleBackToHome = () => {
	router.push({ name: 'dashboard' }).catch(() => {
		router.push({ name: 'login' })
	})
}
</script>

<template>
	<v-layout>
		<layout-bar>
			<language-btn />
			<theme-switch />
		</layout-bar>

		<v-main>
			<v-empty-state
				:headline="headline"
				:title="$t(errorConfig?.title ?? 'common.state.error')"
				:icon="errorConfig?.icon"
			>
				<v-btn color="primary" prepend-icon="mdi-home" @click="handleBackToHome">
					{{ $t('common.btn.backToHome') }}
				</v-btn>
			</v-empty-state>
		</v-main>
	</v-layout>
</template>

