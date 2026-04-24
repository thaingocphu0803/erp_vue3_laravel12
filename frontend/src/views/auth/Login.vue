<script setup lang="ts">
import LayoutBar from '@/components/layout/LayoutBar.vue'
import ThemeSwitch from '@/components/ThemeSwitch.vue'
import Form from '@/components/Form.vue'
import Input from '@/components/form/Input.vue'
import Checkbox from '@/components/form/CheckBox.vue'
import BaseBtn from '@/components/BaseBtn.vue'
import LanguageBtn from '@/components/layout/LanguageBtn.vue'
import useAuthValidation from '@/composables/validation/useAuthValidation'
import ErrorAlert from '@/components/form/ErrorAlert.vue'
import { onMounted, provide, reactive, ref } from 'vue'
import router from '@/router'
import { useAuthStore } from '@/stores/auth'
import { useRoute, type RouteLocationRaw } from 'vue-router'
import { formatLaravelRetryAfter, mapLaravelError } from '@/utils/errorHandler'
import AppToast from '@/components/layout/AppToast.vue'
import { useToastStore } from '@/stores/toast'
import { useThrottleStore } from '@/stores/throttle'
import { storeToRefs } from 'pinia'
import ThrottleAlert from '@/components/ThrottleAlert.vue'
import SYSTEM from '@/config/system'

import type { LoginFormData, LoginFormError } from '@/types/auth'
import CONFIG from '@/config/constants'

const title: string = 'auth.title.login'
const LanguageBtnColor: string = 'blue-gray-draken-4'

provide('LanguageBtnColor', LanguageBtnColor)

const toast = useToastStore()

const loginFormData = reactive<LoginFormData>({
	email: '',
	password: '',
	rememberMe: false,
})

const errorMessage = reactive<LoginFormError>({
	unauthorized: '',
	email: '',
	password: '',
	rememberMe: '',
})

const checkboxData = {
	falseValue: false,
	trueValue: true,
}

const route = useRoute()

const redirect = ref<RouteLocationRaw>('')

const loading = ref<boolean>(false)

const visible = ref<boolean>(false)

const { throttle, isDisabled } = storeToRefs(useThrottleStore())
const { initThrottle, startThrottle } = useThrottleStore()
const { authValidation } = useAuthValidation()

const { authLogin, clearAuth } = useAuthStore()

const handleLogin = async () => {
	try {
		loading.value = true

		const response = await authLogin(loginFormData)

		redirect.value = (route.query.redirect as string) || { name: 'dashboard' }

		router.replace(redirect.value)

		toast.show(response.data.messageCode, 'success')
	} catch (error: any) {
		clearAuth()

		if (error.status === SYSTEM.SERVER_ERROR.UNAUTHORIZED) {
			errorMessage.unauthorized = error.response.data.messageCode
		}

		if (error.status === SYSTEM.SERVER_ERROR.UNPROCESSABLE_ENTITY) {
			mapLaravelError(errorMessage, error)
		}

		if (error.status === SYSTEM.SERVER_ERROR.TOO_MANY_REQUESTS) {
			throttle.value['login'] = formatLaravelRetryAfter(error)
			startThrottle('login')
		}

		if (error.status === SYSTEM.SERVER_ERROR.INTERNAL_SERVER_ERROR) {
			const message = 'auth.alert.error.login'
			toast.show(message, 'error')
		}
	} finally {
		loading.value = false
	}
}

onMounted(() => {
	initThrottle('login')
})
</script>

<template>
	<v-layout>
		<!-- layout bar -->
		<layout-bar>
			<language-btn />
			<theme-switch />
		</layout-bar>

		<!-- layout main -->
		<v-main class="mx-auto my-auto" :max-width="CONFIG.maxWidthAuthForm">
			<Form :title @submit-form="handleLogin">
				<!-- error alert -->
				<error-alert :messages="errorMessage" class="text-center"></error-alert>

				<!-- email -->
				<Input
					:label="$t('auth.input.email')"
					name="email"
					placeholder="example@gmail.com"
					:rules="authValidation.email"
					v-model="loginFormData.email"
				/>

				<!-- password -->
				<Input
					:label="$t('auth.input.password')"
					name="password"
					:type="visible ? 'text' : 'password'"
					:append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
					@click:append-inner="visible = !visible"
					:rules="authValidation.password"
					v-model="loginFormData.password"
				/>

				<!-- remember me -->
				<Checkbox
					:label="$t('auth.input.rememberMe')"
					name="remember_me"
					v-model="loginFormData.rememberMe"
					:false-value="checkboxData.falseValue"
					:true-value="checkboxData.trueValue"
				/>

				<!-- submit -->
				<base-btn
					:title
					:loading="loading"
					type="submit"
					block
					:disabled="isDisabled('login')"
				/>
			</Form>

			<!-- throttle alert -->
			<v-row justify="center" dense>
				<throttle-alert :show="isDisabled('login')" :time="throttle['login'] || 0" />
			</v-row>
		</v-main>

		<!-- toast -->
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

