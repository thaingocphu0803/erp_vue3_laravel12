<script setup lang="ts">
import AuthBar from '@/components/auth/AuthBar.vue'
import Form from '@/components/Form.vue'
import Input from '@/components/form/Input.vue'
import Checkbox from '@/components/form/CheckBox.vue'
import Button from '@/components/form/Button.vue'
import authValidation from '@/composables/validations/auth'
import { reactive, ref } from 'vue'
import router from '@/router'
import { useAuthStore } from '@/stores/auth'
import { useRoute, type RouteLocationRaw } from 'vue-router'

interface LoginForm {
  email: string
  password: string
  rememberMe: Boolean
}

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
    <auth-bar next-route="register" btn-title="register" />
    <v-main class="mx-auto my-auto" max-width="420px">
      <Form title="login" @submit-form="handleLogin">
        <v-alert
          v-if="errorMessage.length"
          color="red-lighten-4"
          density="comfortable"
          class="text-error text-center"
        >
          {{ errorMessage }}
        </v-alert>

        <Input
          label="Email"
          name="email"
          placeholder="example@gmail.com"
          :rules="authValidation.email"
          v-model="LoginData.email"
        />
        <Input
          label="Password"
          name="password"
          type="password"
          :rules="authValidation.password"
          v-model="LoginData.password"
        />
        <Checkbox
          label="Remember me"
          name="remember_me"
          v-model="LoginData.rememberMe"
          :false-value="checkboxData.falseValue"
          :true-value="checkboxData.trueValue"
        />
        <Button title="login" :loading="loading" />
      </Form>
    </v-main>
  </v-layout>
</template>
