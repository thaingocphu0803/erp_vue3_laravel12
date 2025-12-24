<script setup lang="ts">
import AuthBar from '@/components/auth/AuthBar.vue'
import Form from '@/components/form/Form.vue'
import Input from '@/components/form/Input.vue'
import Checkbox from '@/components/form/CheckBox.vue'
import Button from '@/components/form/Button.vue'
import authValidation from '@/composables/validations/auth'
import { reactive, ref } from 'vue'
import api from '@/services/api'

type YesNo = 'yes' | 'no'

interface LoginForm {
  email: string
  password: string
  rememberMe: YesNo
}

const LoginData = reactive<LoginForm>({
  email: '',
  password: '',
  rememberMe: 'no',
})

const checkboxData = {
  falseValue: 'no',
  trueValue: 'yes',
}

const loading = ref<boolean>(false)

const handleSubmit = async() => {
	try {
		loading.value = true
		const response = await api.post('login', LoginData)
	} catch (err) {
		console.log(err)
	} finally {
		loading.value =false
	}
}
</script>

<template>
  <v-layout>
    <auth-bar next-route="register" btn-title="register" />
    <v-main class="mx-auto my-auto" max-width="420px">
      <Form title="login" @submit-form="handleSubmit">
		<v-alert color="red-lighten-4" class="text-error">
			oke
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
        <Button type="submit" title="login" :loading="loading"/>
      </Form>
    </v-main>
  </v-layout>
</template>
