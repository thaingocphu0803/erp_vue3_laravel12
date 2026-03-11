<script setup lang="ts">
import { watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRoute } from 'vue-router'
import systemConfig from '@/config/system'

const { t, locale } = useI18n()

const route = useRoute()

watch([locale, () => route.path], () => {
  const baseTitle = systemConfig.appName
  const metaTitle = route.meta.title ? t(route.meta.title as string) : ''
  document.title = metaTitle ? `${metaTitle} - ${baseTitle}` : baseTitle
}, { immediate: true })

</script>

<template>
  <v-app>
    <router-view />
  </v-app>
</template>
