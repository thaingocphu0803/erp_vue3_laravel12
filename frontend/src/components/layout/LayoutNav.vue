<script setup lang="ts">
import { useDisplay } from 'vuetify'
import { useAuthStore } from '@/stores/auth'
import defaultConfig from '@/config/default'
import { navMenu } from '@/config/menu'

const { sm } = useDisplay()
const authStore = useAuthStore()
const user = authStore.user
const avatar = user?.avatar ?? defaultConfig.avatar
</script>
<template>
  <v-navigation-drawer mobile-breakpoint="sm" :rail="sm" v-bind="$attrs">
    <v-list>
      <v-list-item
        :prepend-avatar="avatar"
        :subtitle="user?.email"
        :title="user?.name"
      ></v-list-item>
    </v-list>

    <v-divider></v-divider>

    <v-list density="compact" nav>
      <v-list-item
        v-for="item in navMenu"
        :key="item.value"
        :prepend-icon="item.icon"
        :title="item.title"
        :value="item.value"
      ></v-list-item>
    </v-list>
  </v-navigation-drawer>
  <v-main>

  </v-main>
</template>
