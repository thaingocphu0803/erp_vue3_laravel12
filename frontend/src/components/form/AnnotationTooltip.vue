<script lang="ts" setup>

interface Props {
	text: string
}

const props = defineProps<Props>()

</script>
<template>
	<v-tooltip location="top" :text="$t(props.text)">
		<template #activator="{ props }">
			<v-icon v-bind="props" icon="mdi-information-outline" size="18" color="grey-darken-1" />
		</template>
	</v-tooltip>
</template>
