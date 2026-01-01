<script setup lang="ts">
import BaseIconBtn from '../BaseIconBtn.vue'
import { languageMenu } from '@/config/menu'
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
const handleLogout = async ()=> await authStore.authLogout()

</script>

<template>
  <v-menu location="bottom" transition="slide-y-transition">
    <template v-slot:activator="{ props }">
      <base-icon-btn icon="mdi-dots-vertical" class="d-sm-none" v-bind="props"></base-icon-btn>
    </template>

    <v-list>
      <v-list-group value="languageBtn" @click.stop>
        <template v-slot:activator="{ props }">
          <v-list-item v-bind="props" title="Language"></v-list-item>
        </template>
        <v-list-item
          v-for="item in languageMenu"
          :key="item.value"
          :value="item.value"
          :title="item.title"
        >
        </v-list-item>
      </v-list-group>

      <v-list-item value="logoutBtn" title="Logout" @click="handleLogout"></v-list-item>
    </v-list>
  </v-menu>
</template>
