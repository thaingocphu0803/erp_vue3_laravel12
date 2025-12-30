<script setup lang="ts">
interface props {
	tooltip?: string,
	icon: string
}

const props = defineProps<props>()

</script>

<template>
	<v-btn color="primary" icon density="comfortable" v-bind="$attrs">
        <v-tooltip v-if="props.tooltip" activator="parent" location="bottom">{{ props.tooltip }}</v-tooltip>
        <v-icon size="large">{{ props.icon }}</v-icon>
    </v-btn>
</template>