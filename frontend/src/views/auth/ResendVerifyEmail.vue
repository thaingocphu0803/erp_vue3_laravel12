<script setup lang="ts">
import LayoutBar from '@/components/layout/LayoutBar.vue'
import ThemeSwitch from '@/components/ThemeSwitch.vue'
import Form from '@/components/Form.vue'
import BaseBtn from '@/components/BaseBtn.vue'
import LanguageBtn from '@/components/layout/LanguageBtn.vue'
import { provide, reactive, ref } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useToastStore } from '@/stores/toast'
import AppToast from '@/components/layout/AppToast.vue'
import defaultConfig from '@/config/default'

interface Payload {
	id: number | null
	hash: string
}

const payload: Payload = {
	id: null,
	hash: '',
}

const title: string = 'auth.title.resendVerifyEmail'
const LanguageBtnColor: string = 'blue-gray-draken-4'

provide('LanguageBtnColor', LanguageBtnColor)

const route = useRoute()
const toast = useToastStore()
const { authResendVerifyEmail } = useAuthStore()

const loading = ref<boolean>(false)

const handleResend = async () => {
	try {
		loading.value = true

		payload.id = Number(route.query.id)
		payload.hash = String(route.query.hash)

		if (!payload.id || !payload.hash) {
			toast.show('auth.validate.verify.invalidToken', 'error')
			return
		}

		const response = await authResendVerifyEmail(payload)
		toast.show(response.data.messageCode, 'success')
	} catch (error: any) {
		console.error('Resend error:', error)
		const message = error.response?.data?.messageCode || 'auth.alert.error.invalidAuth'
		toast.show(message, 'error')
	} finally {
		loading.value = false
	}
}
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

				<base-btn :title :loading="loading" type="button" class="mt-5" color="primary"
					@click.prevent="handleResend" />
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
