<script lang="ts" setup>
import { computed } from 'vue'

interface Props {
	messages: Record<string, string>
	ignore?: string[]
}

const props = withDefaults(defineProps<Props>(), {
	ignore: () => []
})

const filterMessages = computed(() => {
	const ignoreSet = new Set(props.ignore)

	return Object.entries(props.messages).reduce((acc: string[], [key, value]) => {
		if (!ignoreSet.has(key) && value.length) {
			acc.push(value)
		}
		return acc
	}, [])
})
</script>

<template>
	<v-expand-transition>
		<v-alert v-if="filterMessages.length" color="red-lighten-4" density="comfortable" class="mb-2 text-error" v-bind="$attrs">
			<ul class="d-flex ga-2 flex-column">
				<template v-for="(message, index) in filterMessages" :key="index">
					<li>{{ $t(message) }}</li>
				</template>
			</ul>
		</v-alert>
	</v-expand-transition>
</template>

<style scoped>
	ul {
		list-style-type: disc;
	}
	li {
		margin-left: 1em;
	}
</style>
