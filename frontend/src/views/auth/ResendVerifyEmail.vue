<script setup lang="ts">
import LayoutBar from '@/components/layout/LayoutBar.vue'
import ThemeSwitch from '@/components/ThemeSwitch.vue'
import BaseBtn from '@/components/BaseBtn.vue'
import LanguageBtn from '@/components/layout/LanguageBtn.vue'
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { useToastStore } from '@/stores/toast'
import AppToast from '@/components/layout/AppToast.vue'
import CONFIG from '@/config/constants'
import { useThrottleStore } from '@/stores/throttle'
import { storeToRefs } from 'pinia'
import ThrottleAlert from '@/components/ThrottleAlert.vue'
import SYSTEM from '@/config/system'
import { authService } from '@/services/authService'

import type { VerifyEmailParam } from '@/types/auth'
import { formatLaravelRetryAfter } from '@/utils/errorHandler'

const title: string = 'auth.title.resendVerifyEmail'

const route = useRoute()
const toast = useToastStore()

const loading = ref<boolean>(false)

const { throttle, isDisabled } = storeToRefs(useThrottleStore())

const { initThrottle, startThrottle } = useThrottleStore()

const handleResendEmailVerification = async () => {
	try {
		loading.value = true

		const payload: VerifyEmailParam = {
			id: route.query.id ? Number(route.query.id) : null,
			hash: route.query.hash ? String(route.query.hash) : '',
		}

		if (!payload.id || !payload.hash) {
			toast.show('auth.validate.verify.invalidToken', 'error')
			return
		}

		const response = await authService.resendVerifyEmail(payload)
		toast.show(response.data.messageCode, 'success')
	} catch (error: any) {
		if (error.status === SYSTEM.SERVER_ERROR.UNPROCESSABLE_ENTITY) {
			toast.show(error.response.data.messageCode, 'error')
		}

		if (error.status === SYSTEM.SERVER_ERROR.TOO_MANY_REQUESTS) {
			throttle.value['resendEmail'] = formatLaravelRetryAfter(error)
			startThrottle('resendEmail')
		}
	} finally {
		loading.value = false
	}
}

onMounted(() => {
	initThrottle('resendEmail')
})
</script>

<template>
	<v-layout>
		<!-- Header -->
		<layout-bar>
			<language-btn />
			<theme-switch />
		</layout-bar>

		<!-- Content -->
		<v-main class="mx-auto my-auto" :max-width="CONFIG.maxWidthForm">
			<v-card density="comfortable" class="border d-flex flex-column justify-center align-center ga-5 pa-5">
				<!-- Icon -->
				<v-icon color="warning" icon="mdi-emoticon-dead-outline" size="72" />

				<!-- Description -->
				<p class="text-body-1 text-medium-emphasis text-center">
					{{ $t('common.state.expiredLink') }}
				</p>

				<!-- Button -->
				<base-btn :title :loading="loading" type="button" class="mt-5" :disabled="isDisabled('resendEmail')"
					@click.prevent="handleResendEmailVerification" />

				<!-- Throttle Alert -->
				<throttle-alert :show="isDisabled('resendEmail')" :time="throttle['resendEmail'] || 0" />
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
