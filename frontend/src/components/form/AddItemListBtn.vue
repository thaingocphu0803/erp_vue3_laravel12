<script lang="ts" setup>
interface Props {
	title: string
}

const props = defineProps<Props>()

const emit = defineEmits(['open-model'])

</script>

<template>
	<v-list-item @click="$emit('open-model')" color="primary" class="text-primary">
		<template #prepend>
			<v-icon icon="mdi-plus-circle-outline" />
		</template>
		<v-list-item-title class="font-weight-bold">
			{{ $t(props.title) }}
		</v-list-item-title>
	</v-list-item>
</template>
