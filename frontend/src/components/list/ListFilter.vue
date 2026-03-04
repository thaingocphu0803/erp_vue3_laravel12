<script setup lang="ts">
interface Props {
	searchable?: boolean
}

const props = defineProps<Props>()
</script>

<template>
	<v-autocomplete
		v-if="props.searchable"
		v-bind="$attrs"
		variant="outlined"
		density="compact"
		hide-details
		clearable
	></v-autocomplete>
	<v-select
		v-else
		v-bind="$attrs"
		variant="outlined"
		density="compact"
		hide-details
		clearable
	></v-select>
</template>
