<script setup lang="ts">
interface Props {
	label: string
}

const props = defineProps<Props>()
</script>

<template>
	<span>{{ props.label }}</span> <span class="text-error">*</span>
</template>

