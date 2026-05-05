<script setup lang="ts">
interface Props {
	show: boolean
	time: number
}

const props = defineProps<Props>()
</script>

<template>
	<div v-if="props.show" class="text-red-lighten-2 mt-2">
		{{ $t('common.throttle.tooManyRequests', { time: props.time }) }}
	</div>
</template>
