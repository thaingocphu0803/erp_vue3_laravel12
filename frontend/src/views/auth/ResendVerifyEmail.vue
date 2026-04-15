<script setup lang="ts">
import LayoutBar from '@/components/layout/LayoutBar.vue'
import ThemeSwitch from '@/components/ThemeSwitch.vue'
import Form from '@/components/Form.vue'
import BaseBtn from '@/components/BaseBtn.vue'
import LanguageBtn from '@/components/layout/LanguageBtn.vue'
import { onMounted, provide, reactive, ref } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useToastStore } from '@/stores/toast'
import AppToast from '@/components/layout/AppToast.vue'
import defaultConfig from '@/config/default'
import { removeRetryAfter, setRetryAfter, getRetryAfter } from '@/utils/dateFormat'

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

const isDisabled = ref<boolean>(false)

const retryAfter = ref<number>(0)

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
			isDisabled.value = true

			retryAfter.value = error.response.headers['retry-after'] as number
			handleCountdown()
		}
	} finally {
		loading.value = false
	}
}

const handleCountdown = () => {
	const interval = setInterval(() => {
		retryAfter.value--
		setRetryAfter(String(retryAfter.value))
		if (retryAfter.value === 0) {
			clearInterval(interval)
			removeRetryAfter()
			isDisabled.value = false
		}
	}, 1000)
}

onMounted(() => {
	if (getRetryAfter()) {
		isDisabled.value = true
		retryAfter.value = Number(getRetryAfter())
		handleCountdown()
	}
})
</script>

<template>
	<v-layout>
		<layout-bar>
			<language-btn />
			<theme-switch />
		</layout-bar>

		<v-main class="mx-auto my-auto" :max-width="defaultConfig.maxWidthForm">
			<v-card
				density="comfortable"
				class="border d-flex flex-column justify-center align-center ga-5 pa-5"
			>
				<v-icon color="warning" icon="mdi-emoticon-dead-outline" size="72" />

				<p class="text-body-1 text-medium-emphasis text-center">
					{{ $t('common.state.expiredLink') }}
				</p>

				<base-btn
					:title
					:loading="loading"
					type="button"
					class="mt-5"
					color="primary"
					:disabled="isDisabled"
					@click.prevent="handleResendEmailVerification"
				/>

				<span v-if="isDisabled" class="text-red-lighten-2">
					{{ $t('common.throttle.tooManyRequests', { time: retryAfter }) }}
				</span>
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

