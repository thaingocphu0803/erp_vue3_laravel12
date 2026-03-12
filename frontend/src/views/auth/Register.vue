<script setup lang="ts">
import LayoutBar from '@/components/layout/LayoutBar.vue'
import ThemeSwitch from '@/components/ThemeSwitch.vue'
import Form from '@/components/Form.vue'
import Input from '@/components/form/Input.vue'
import BaseBtn from '@/components/BaseBtn.vue'
import { reactive } from 'vue'
import authValidation from '@/composables/validation/useAuthValidation'

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
    <layout-bar>
      <theme-switch />
    </layout-bar>
    
    <v-main class="mx-auto my-auto" max-width="420px">
      <Form title="register">
        <Input label="Name" name="name" placeholder="William beyers" v-model="RegisterData.name"
          :rules="authValidation.name" />
        <Input label="Email" name="email" placeholder="example@gmail.com" v-model="RegisterData.email"
          :rules="authValidation.email" />
        <Input label="Password" name="password" type="password" v-model="RegisterData.password"
          :rules="authValidation.password" />
        <Input label="Confirm password" name="password_confirmed" type="password" v-model="RegisterData.confirmPassword"
          :rules="authValidation.passwordConfirm(RegisterData.password)" />
        <base-btn title="register" />
      </Form>
    </v-main>
  </v-layout>
</template>
