<script setup lang="ts">
import AuthBar from '@/components/auth/AuthBar.vue'
import Form from '@/components/Form.vue'
import Input from '@/components/form/Input.vue'
import Button from '@/components/form/Button.vue'
import { reactive } from 'vue'
import authValidation from '@/composables/validations/auth'

interface RegisterForm {
  name: string
  email: string
  password: string
  confirmPassword: string
}

const RegisterData = reactive<RegisterForm>({
  name: '',
  email: '',
  password: '',
  confirmPassword: '',
})
</script>

<template>
  <v-layout>
    <auth-bar next-route="login" btn-title="login" />
    <v-main class="mx-auto my-auto" max-width="420px">
      <Form title="register">
        <Input
          label="Name"
          name="name"
          placeholder="William beyers"
          v-model="RegisterData.name"
          :rules="authValidation.name"
        />
        <Input
          label="Email"
          name="email"
          placeholder="example@gmail.com"
          v-model="RegisterData.email"
          :rules="authValidation.email"
        />
        <Input
          label="Password"
          name="password"
		  type="password"
          v-model="RegisterData.password"
          :rules="authValidation.password"
        />
        <Input
          label="Confirm password"
          name="password_confirmed"		  
		  type="password"
          v-model="RegisterData.confirmPassword"
          :rules="authValidation.passwordConfirm(RegisterData.password)"
        />
        <Button title="register" />
      </Form>
    </v-main>
  </v-layout>
</template>
