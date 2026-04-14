<script setup lang="ts">
import LayoutBar from '@/components/layout/LayoutBar.vue'
import ThemeSwitch from '@/components/ThemeSwitch.vue'
import Form from '@/components/Form.vue'
import Input from '@/components/form/Input.vue'
import BaseBtn from '@/components/BaseBtn.vue'
import { reactive, ref } from 'vue'
import authValidation from '@/composables/validation/useAuthValidation'
import LanguageBtn from '@/components/layout/LanguageBtn.vue'
import ErrorAlert from '@/components/form/ErrorAlert.vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import router from '@/router'
import { mapLaravelError } from '@/utils/errorHandler'
import { useToastStore } from '@/stores/toast'

interface CreatePasswordForm {
	password: string
	password_confirmation: string
	id: number | null
	hash: string
}

interface ErrorMessage {
	password: string
	id: string
	hash: string
}

const route = useRoute()
const { authCreatePassword } = useAuthStore()
const toast = useToastStore()

const loading = ref<boolean>(false)
const visible = ref<boolean>(false)

const CreatePasswordData = reactive<CreatePasswordForm>({
	password: '',
	password_confirmation: '',
	id: null,
	hash: '',
})

const errorMessage = reactive<ErrorMessage>({
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

		CreatePasswordData.id = Number(route.query.id)
		CreatePasswordData.hash = route.query.hash as string

		const response = await authCreatePassword(CreatePasswordData)
		router.push({ name: 'login' })
		toast.show(response.data.messageCode, 'success')
	} catch (error: any) {
		if (error.status === 422) {
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
			<Form title="auth.title.createPassword" @submit-form="handleCreatePassword">
				<error-alert :messages="errorMessage" class="text-center"></error-alert>

				<Input
					:label="$t('auth.input.newPassword')"
					name="password"
					:type="visible ? 'text' : 'password'"
					:append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
					@click:append-inner="visible = !visible"
					v-model="CreatePasswordData.password"
					:rules="authValidation.password"
				/>

				<Input
					:label="$t('auth.input.confirmNewPassword')"
					name="password_confirmation"
					:type="visible ? 'text' : 'password'"
					v-model="CreatePasswordData.password_confirmation"
					:rules="authValidation.passwordConfirm(CreatePasswordData.password)"
				/>

				<base-btn title="common.btn.confirm" type="submit" :loading="loading" />
			</Form>
		</v-main>
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

