<script setup lang="ts">
import LayoutBar from '@/components/layout/LayoutBar.vue'
import ThemeSwitch from '@/components/ThemeSwitch.vue'
import Form from '@/components/Form.vue'
import Input from '@/components/form/Input.vue'
import Checkbox from '@/components/form/CheckBox.vue'
import BaseBtn from '@/components/BaseBtn.vue'
import LanguageBtn from '@/components/layout/LanguageBtn.vue'
import authValidation from '@/composables/validation/useAuthValidation'
import ErrorAlert from '@/components/form/ErrorAlert.vue'
import { provide, reactive, ref } from 'vue'
import router from '@/router'
import { useAuthStore } from '@/stores/auth'
import { useRoute, type RouteLocationRaw } from 'vue-router'
import { mapLaravelError } from '@/utils/errorHandler'

interface LoginForm {
	email: string
	password: string
	rememberMe: Boolean
}

interface ErrorMessage {
	unauthorized: string
	email: string,
	password: string
	rememberMe: string
}

const title: string = 'auth.title.login'
const LanguageBtnColor: string = 'blue-gray-draken-4'

provide('LanguageBtnColor', LanguageBtnColor)

const LoginData = reactive<LoginForm>({
	email: '',
	password: '',
	rememberMe: false,
})

const errorMessage = reactive<ErrorMessage>({
	unauthorized: '',
	email: '',
	password: '',
	rememberMe: ''
})

const checkboxData = {
	falseValue: false,
	trueValue: true,
}

const route = useRoute()

const redirect = ref<RouteLocationRaw>('')

const loading = ref<boolean>(false)

const handleLogin = async () => {
	try {
		loading.value = true

		const { authLogin } = useAuthStore()
		const response = await authLogin(LoginData)

		redirect.value = (route.query.redirect as string) || { name: 'dashboard' }
		router.replace(redirect.value)

	} catch (error: any) {
		const status = error.response.status

		if (status === 401) {
			errorMessage.unauthorized = error.response.data.messageCode
		} else if (status == 422) {
			mapLaravelError(errorMessage, error)
		}
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
			<Form :title @submit-form="handleLogin">
				<error-alert :messages="errorMessage" class="text-center"></error-alert>

				<Input :label="$t('auth.input.email')" name="email" placeholder="example@gmail.com"
					:rules="authValidation.email" v-model="LoginData.email"/>

				<Input :label="$t('auth.input.password')" name="password" type="password"
					:rules="authValidation.password" v-model="LoginData.password"/>

				<Checkbox :label="$t('auth.input.rememberMe')" name="remember_me" v-model="LoginData.rememberMe"
					:false-value="checkboxData.falseValue" :true-value="checkboxData.trueValue" />

				<base-btn :title :loading="loading" type="submit" block />
			</Form>
		</v-main>
	</v-layout>
</template>

<style scoped>
	:deep(ul){
		list-style-type: none;
		padding-left: unset;
	}
	:deep(li){
		margin-left: unset;
	}
</style>
