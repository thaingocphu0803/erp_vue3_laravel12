<script setup lang="ts">
import { ref } from 'vue'
import type { VForm } from 'vuetify/components'

interface Props {
  title: string
}

const props = defineProps<Props>()

const formRef = ref<VForm | null>(null)

const emit = defineEmits(['submit-form'])

const handleSubmit = async () => {
  if (!formRef.value) return

  let { valid } = await formRef.value.validate()

  if (valid) {
    emit('submit-form')
  }
}
</script>

<template>
  <v-card density="comfortable" border>
    <v-card-title class="text-uppercase text-center">{{ props.title }}</v-card-title>

    <v-card-text class="mt-5">
      <v-form
        @submit.prevent="handleSubmit"
        ref="formRef"
        class="d-flex flex-column ga-3"
        v-bind:="$attrs"
      >
        <slot></slot>
      </v-form>
    </v-card-text>
  </v-card>
</template>
