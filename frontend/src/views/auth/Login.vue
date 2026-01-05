<script setup lang="ts">
import LayoutBar from '@/components/layout/LayoutBar.vue'
import ThemeSwitch from '@/components/ThemeSwitch.vue'
import Form from '@/components/Form.vue'
import Input from '@/components/form/Input.vue'
import Checkbox from '@/components/form/CheckBox.vue'
import BaseBtn from '@/components/BaseBtn.vue'
import LanguageBtn from '@/components/layout/LanguageBtn.vue'
import authValidation from '@/composables/useAuthValidation'
import { provide, reactive, ref } from 'vue'
import router from '@/router'
import { useAuthStore } from '@/stores/auth'
import { useRoute, type RouteLocationRaw } from 'vue-router'

interface LoginForm {
	email: string
	password: string
	rememberMe: Boolean
}

const title: string = 'auth.title.login'

const LanguageBtnColor: string = 'blue-gray-draken-4'

provide('LanguageBtnColor', LanguageBtnColor)

const LoginData = reactive<LoginForm>({
	email: '',
	password: '',
	rememberMe: false,
})

const checkboxData = {
	falseValue: false,
	trueValue: true,
}

const route = useRoute()

const redirect = ref<RouteLocationRaw>('')

const loading = ref<boolean>(false)

const errorMessage = ref<string>('')

const handleLogin = async () => {
	try {
		loading.value = true

		const { authLogin } = useAuthStore()
		const response = await authLogin(LoginData)
		if (response.status === 200) {
			redirect.value = (route.query.redirect as string) || { name: 'dashboard' }
			router.replace(redirect.value)
		}
	} catch (error: any) {
		const status = error.response.status

		if (status === 422 || status === 401) {
			errorMessage.value = error.response.data.messageCode
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
				<v-alert v-if="errorMessage.length" color="red-lighten-4" density="comfortable"
					class="text-error text-center">
					{{ $t(errorMessage) }}
				</v-alert>

				<Input :label="$t('auth.input.email')" name="email" placeholder="example@gmail.com"
					:rules="authValidation.email" v-model="LoginData.email" />
				<Input :label="$t('auth.input.password')" name="password" type="password"
					:rules="authValidation.password" v-model="LoginData.password" />
				<Checkbox :label="$t('auth.input.rememberMe')" name="remember_me" v-model="LoginData.rememberMe"
					:false-value="checkboxData.falseValue" :true-value="checkboxData.trueValue" />
				<base-btn :title :loading="loading" type="submit" block />
			</Form>
		</v-main>
	</v-layout>
</template>