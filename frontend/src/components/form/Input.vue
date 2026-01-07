<template>
  <v-text-field
    v-bind="$attrs"
    validate-on="blur"
    density="compact"
  >
  <template v-slot:message="{message}">{{ $t(message) }}</template>
  </v-text-field>
</template>
