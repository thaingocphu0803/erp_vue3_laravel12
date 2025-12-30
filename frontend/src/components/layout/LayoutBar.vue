<script setup lang="ts">
import systemConfig from '@/config/system'
import ThemeSwitch from '../ThemeSwitch.vue'
import NotificationBtn from './NotificationBtn.vue'
import LanguageBtn from './LanguageBtn.vue'
import MobileMenuBtn from './MobileMenuBtn.vue'

</script>

<template>
  <v-app-bar>
    <template v-slot:prepend>
      <v-app-bar-nav-icon color="primary" class="d-sm-none"> </v-app-bar-nav-icon>
    </template>
    <v-app-bar-title class="text-primary">{{ systemConfig.appName }}</v-app-bar-title>

    <div class="d-flex align-center justify-center ga-5 mr-5">
      <notification-btn />
      <language-btn />

      <theme-switch color="primary" base-color="primary" />

      <v-btn
        color="primary"
        variant="flat"
        append-icon="mdi-logout"
        class="text-capitalize d-none d-sm-flex"
      >
        logout
      </v-btn>

	  <mobile-menu-btn/>
    </div>
  </v-app-bar>
</template>
