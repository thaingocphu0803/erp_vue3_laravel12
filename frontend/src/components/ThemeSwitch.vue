<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';
import { useTheme } from 'vuetify';

const theme = useTheme()

const themValue = {
	light: 'light',
	dark: 'dark'
}
const defaultTheme = ref<string>(theme.global.name.value);


onMounted(() => {
	let savedTheme = localStorage.getItem('userTheme')

	if (savedTheme) {
		theme.change(savedTheme);
		defaultTheme.value = savedTheme
	}
})

watch(defaultTheme, (newTheme) => {
    theme.change(newTheme);
    localStorage.setItem('userTheme', newTheme);
});
</script>

<template>
  <v-switch
	v-model="defaultTheme"
	:false-value="themValue.light"
	:true-value="themValue.dark"
	v-bind="$attrs"
    class="d-none d-sm-block"
    color="blue-grey-darken-4"
	base-color="blue-grey-darken-4"
    false-icon="mdi-white-balance-sunny"
    hide-details
    density="comfortable"
    true-icon="mdi-weather-night"
    inset
  >
  </v-switch>
</template>
