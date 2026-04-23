<script setup lang="ts">
import { inject } from 'vue'

interface Props {
	tooltip?: string
	icon: string
}

const props = defineProps<Props>()

const LanguageBtnColor = inject<string>('LanguageBtnColor', 'primary')
</script>

<template>
	<v-btn :color="LanguageBtnColor" icon density="comfortable" v-bind="$attrs">
		<v-tooltip v-if="props.tooltip" activator="parent" location="bottom">{{
			$t(props.tooltip)
		}}</v-tooltip>
		<v-icon size="large">{{ props.icon }}</v-icon>
	</v-btn>
</template>

