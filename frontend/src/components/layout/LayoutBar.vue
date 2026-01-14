<script setup lang="ts">
import systemConfig from '@/config/system'

interface Props {
  textColor?: string
}

const props = defineProps<Props>()
</script>

<template>
  <v-app-bar v-bind="$attrs">
    <template v-slot:prepend>
      <slot name="icon"></slot>
    </template>

    <v-app-bar-title :class="props.textColor" data-testId="app-bar-title">{{ systemConfig.appName }}</v-app-bar-title>

    <div class="d-flex align-center justify-center ga-5 mr-5">
		<slot></slot>
    </div>
  </v-app-bar>
</template>
