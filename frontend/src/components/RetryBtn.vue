<script setup lang="ts">
interface Props {
	onlyIcon?: boolean
}

const props = defineProps<Props>()
</script>

<template>
	<v-btn
		v-if="props.onlyIcon"
		icon="mdi-refresh"
		v-bind="$attrs"
		color="primary"
		variant="text"
		density="compact"
	></v-btn>

	<v-btn v-else color="primary" variant="outlined" prepend-icon="mdi-refresh" v-bind="$attrs">
		{{ $t('common.btn.retry') }}
	</v-btn>
</template>

