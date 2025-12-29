<script setup lang="ts">
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';

const router = useRouter()

const authStore = useAuthStore();


const logout = async () => {
    const response = await authStore.authLogout();

    if (response?.status == 200) {
      router.push({ name: 'login' })
    }
}

const user = authStore.user
</script>
<template>
  <v-layout class="rounded rounded-md border">
    <v-app-bar title="Application bar">
      <v-btn color="primary" variant="elevated" @click="logout()">logout</v-btn>
    </v-app-bar>

    <v-navigation-drawer permanent rail>
      <v-list>
        <v-list-item prepend-avatar="https://randomuser.me/api/portraits/women/85.jpg" :subtitle="user?.email"
          :title="user?.name"></v-list-item>
      </v-list>

      <v-divider></v-divider>

      <v-list density="compact" nav>
        <v-list-item prepend-icon="mdi-folder" title="My Files" value="myfiles"></v-list-item>
        <v-list-item prepend-icon="mdi-account-multiple" title="Shared with me" value="shared"></v-list-item>
        <v-list-item prepend-icon="mdi-star" title="Starred" value="starred"></v-list-item>
      </v-list>
    </v-navigation-drawer>

    <v-bottom-navigation grow>
  <v-btn value="recent">
    <v-icon>mdi-history</v-icon>

    <span>Recent</span>
  </v-btn>

  <v-btn value="favorites">
    <v-icon>mdi-heart</v-icon>

    <span>Favorites</span>
  </v-btn>

  <v-btn value="nearby">
    <v-icon>mdi-map-marker</v-icon>

    <span>Nearby</span>
  </v-btn>

    <v-btn value="nearby">
    <v-icon>mdi-map-marker</v-icon>

    <span>Nearby</span>
  </v-btn>

    <v-btn value="nearby">
    <v-icon>mdi-map-marker</v-icon>

    <span>Nearby</span>
  </v-btn>
</v-bottom-navigation>

    <v-main>
      <v-container>
        <v-sheet border="dashed md" color="surface-light" height="200" rounded="lg" width="100%"></v-sheet>
      </v-container>
    </v-main>


  </v-layout>
</template>
