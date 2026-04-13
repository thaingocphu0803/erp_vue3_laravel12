<script setup lang="ts">
import LayoutBar from '@/components/layout/LayoutBar.vue'
import ThemeSwitch from '@/components/ThemeSwitch.vue'
import Form from '@/components/Form.vue'
import Input from '@/components/form/Input.vue'
import BaseBtn from '@/components/BaseBtn.vue'
import { reactive } from 'vue'
import authValidation from '@/composables/validation/useAuthValidation'
import LanguageBtn from '@/components/layout/LanguageBtn.vue'
import ErrorAlert from '@/components/form/ErrorAlert.vue'

interface RegisterForm {
  password: string
  password_confirmation: string
}

interface ErrorMessage {
  password: string
  password_confirmation: string
}

const RegisterData = reactive<RegisterForm>({
  password: '',
  password_confirmation: '',
})

const errorMessage = reactive<ErrorMessage>({
  password: '',
  password_confirmation: '',
})
</script>

<template>
  <v-layout>
    <layout-bar>
      <language-btn />
      <theme-switch />
    </layout-bar>

    <v-main class="mx-auto my-auto" max-width="420px">
      <Form :title="$t('auth.title.createPassword')">
        <error-alert :messages="errorMessage" class="text-center"></error-alert>

        <Input :label="$t('auth.input.newPassword')" name="password" type="password" v-model="RegisterData.password"
          :rules="authValidation.password" />

        <Input :label="$t('auth.input.confirmNewPassword')" name="password_confirmation" type="password"
          v-model="RegisterData.password_confirmation" :rules="authValidation.passwordConfirm(RegisterData.password)" />

        <base-btn :title="$t('common.btn.confirm')" />
      </Form>
    </v-main>
  </v-layout>
</template>
