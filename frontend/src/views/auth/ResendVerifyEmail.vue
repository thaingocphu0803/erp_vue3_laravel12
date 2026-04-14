<script setup lang="ts">
import LayoutBar from '@/components/layout/LayoutBar.vue'
import ThemeSwitch from '@/components/ThemeSwitch.vue'
import Form from '@/components/Form.vue'
import BaseBtn from '@/components/BaseBtn.vue'
import LanguageBtn from '@/components/layout/LanguageBtn.vue'
import { provide, ref } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useToastStore } from '@/stores/toast'
import AppToast from '@/components/layout/AppToast.vue'
import { useI18n } from 'vue-i18n'

const title: string = 'auth.title.resendVerifyEmail'
const LanguageBtnColor: string = 'blue-gray-draken-4'

provide('LanguageBtnColor', LanguageBtnColor)

const route = useRoute()
const toast = useToastStore()
const { authResendVerifyEmail } = useAuthStore()
const i18n = useI18n()

const loading = ref<boolean>(false)

const handleResend = async () => {
	try {
		loading.value = true

		const id = route.query.id
		const hash = route.query.hash

		if (!id || !hash) {
			toast.show(i18n.t('auth.validate.verify.invalidToken'), 'error')
			return
		}

		const response = await authResendVerifyEmail({ id: Number(id), hash: String(hash) })
		toast.show(i18n.t(response.data.messageCode), 'success')
	} catch (error: any) {
		console.error('Resend error:', error)
		const message = error.response?.data?.messageCode || 'auth.alert.error.invalidAuth'
		toast.show(i18n.global.t(message), 'error')
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

		<v-main class="mx-auto my-auto" max-width="420px">
			<Form :title @submit-form="handleResend">
				<div class="text-center mb-6">
					<v-icon
						color="warning"
						icon="mdi-alert-circle-outline"
						size="64"
						class="mb-4"
					/>
					<p class="text-body-1 text-medium-emphasis">
						{{ $t('auth.validate.verify.invalidToken') }}
					</p>
				</div>

				<base-btn :title :loading="loading" type="submit" block />
			</Form>
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

