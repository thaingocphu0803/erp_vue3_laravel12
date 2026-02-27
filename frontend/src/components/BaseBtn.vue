<script setup lang="ts">
interface Props {
  title: string
}

const props = defineProps<Props>()
</script>

<template>
  <v-btn v-bind="$attrs" variant="flat" color="blue-grey-darken-4">
    {{ $t(props.title) }}
  </v-btn>
</template>
