<script setup lang="ts">
import LayoutBar from '@/components/layout/LayoutBar.vue'
import ThemeSwitch from '@/components/ThemeSwitch.vue'
import BaseBtn from '@/components/BaseBtn.vue'
import LanguageBtn from '@/components/layout/LanguageBtn.vue'
import { onMounted, provide, ref } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useToastStore } from '@/stores/toast'
import AppToast from '@/components/layout/AppToast.vue'
import defaultConfig from '@/config/default'
import { useThrottleStore } from '@/stores/throttle'
import { storeToRefs } from 'pinia'
import ThrottleAlert from '@/components/ThrottleAlert.vue'

interface Payload {
	id: number | null
	hash: string
}

const title: string = 'auth.title.resendVerifyEmail'
const LanguageBtnColor: string = 'blue-gray-draken-4'

provide('LanguageBtnColor', LanguageBtnColor)

const route = useRoute()
const toast = useToastStore()
const { authResendVerifyEmail } = useAuthStore()

const loading = ref<boolean>(false)

const { throttle, isDisabled } = storeToRefs(useThrottleStore())

const { initThrottle, startThrottle } = useThrottleStore()

const handleResendEmailVerification = async () => {
	try {
		loading.value = true

		const payload: Payload = {
			id: route.query.id ? Number(route.query.id) : null,
			hash: route.query.hash ? String(route.query.hash) : '',
		}

		if (!payload.id || !payload.hash) {
			toast.show('auth.validate.verify.invalidToken', 'error')
			return
		}

		const response = await authResendVerifyEmail(payload)
		toast.show(response.data.messageCode, 'success')
	} catch (error: any) {
		if (error.status === 422) {
			toast.show(error.response.data.messageCode, 'error')
		}

		if (error.status === 429) {
			throttle.value = error.response.headers['retry-after'] as number
			startThrottle()
		}
	} finally {
		loading.value = false
	}
}

onMounted(() => {
	initThrottle()
})
</script>

<template>
	<v-layout>
		<layout-bar>
			<language-btn />
			<theme-switch />
		</layout-bar>

		<v-main class="mx-auto my-auto" :max-width="defaultConfig.maxWidthForm">
			<v-card density="comfortable" class="border d-flex flex-column justify-center align-center ga-5 pa-5">
				<v-icon color="warning" icon="mdi-emoticon-dead-outline" size="72" />

				<p class="text-body-1 text-medium-emphasis text-center">
					{{ $t('common.state.expiredLink') }}
				</p>

				<base-btn :title :loading="loading" type="button" class="mt-5" :disabled="isDisabled"
					@click.prevent="handleResendEmailVerification" />

				<throttle-alert :show="isDisabled" :time="throttle" />
			</v-card>
		</v-main>

		<app-toast />
	</v-layout>
</template>

<style scoped>
:deep(ul) {
	list-style-type: none;
	padding-left: unset;
}

:deep(li) {
	margin-left: unset;
}
</style>
