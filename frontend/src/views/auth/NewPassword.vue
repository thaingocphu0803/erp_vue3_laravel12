<script setup lang="ts">
import LayoutBar from '@/components/layout/LayoutBar.vue'
import ThemeSwitch from '@/components/ThemeSwitch.vue'
import Form from '@/components/Form.vue'
import Input from '@/components/form/Input.vue'
import BaseBtn from '@/components/BaseBtn.vue'
import { reactive, ref } from 'vue'
import useAuthValidation from '@/composables/validation/useAuthValidation'
import LanguageBtn from '@/components/layout/LanguageBtn.vue'
import ErrorAlert from '@/components/form/ErrorAlert.vue'
import { useRoute } from 'vue-router'
import router from '@/router'
import { mapLaravelError } from '@/utils/errorHandler'
import { useToastStore } from '@/stores/toast'
import SYSTEM from '@/config/system'
import { authService } from '@/services/authService'

import type { NewPasswordFormData, NewPasswordFormError } from '@/types/auth'

const route = useRoute()
const toast = useToastStore()

const { authValidation } = useAuthValidation()

const loading = ref<boolean>(false)
const visible = ref<boolean>(false)

const newPasswordFormData = reactive<NewPasswordFormData>({
	password: '',
	password_confirmation: '',
	id: null,
	hash: '',
})

const errorMessage = reactive<NewPasswordFormError>({
	password: '',
	id: '',
	hash: '',
})

const handleCreatePassword = async () => {
	try {
		loading.value = true

		if (!route.query.id || !route.query.hash) {
			errorMessage.id = 'auth.validate.verify.invalidToken'
			errorMessage.hash = 'auth.validate.verify.invalidToken'
			return
		}

		newPasswordFormData.id = Number(route.query.id)
		newPasswordFormData.hash = route.query.hash as string

		const response = await authService.createPassword(newPasswordFormData)
		router.push({ name: 'login' })
		toast.show(response.data.messageCode, 'success')
	} catch (error: any) {
		if (error.status === SYSTEM.SERVER_ERROR.UNPROCESSABLE_ENTITY) {
			mapLaravelError(errorMessage, error)
		}
	} finally {
		loading.value = false
	}
}
</script>

<template>
	<v-layout>
		<!-- layout bar -->
		<layout-bar>
			<language-btn />
			<theme-switch />
		</layout-bar>

		<!-- layout main -->
		<v-main class="mx-auto my-auto" max-width="420px">
			<Form title="auth.title.createPassword" @submit-form="handleCreatePassword">
				<!-- error alert -->
				<error-alert :messages="errorMessage" class="text-center"></error-alert>

				<!-- new password -->
				<Input
					:label="$t('auth.input.newPassword')"
					name="password"
					:type="visible ? 'text' : 'password'"
					:append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
					@click:append-inner="visible = !visible"
					v-model="newPasswordFormData.password"
					:rules="authValidation.password"
				/>

				<!-- confirm new password -->
				<Input
					:label="$t('auth.input.confirmNewPassword')"
					name="password_confirmation"
					:type="visible ? 'text' : 'password'"
					v-model="newPasswordFormData.password_confirmation"
					:rules="authValidation.passwordConfirm(newPasswordFormData.password)"
				/>

				<!-- submit -->
				<base-btn title="common.btn.confirm" type="submit" :loading="loading" />
			</Form>
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

