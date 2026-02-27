<script setup lang="ts">
interface Props {
	title: string
}

const props = defineProps<Props>()
</script>

<template>
	<div class="d-flex justify-space-between align-center mb-4">
		<h2>{{ $t(props.title) }}</h2>
		<slot name="prepend"></slot>
	</div>
</template>


