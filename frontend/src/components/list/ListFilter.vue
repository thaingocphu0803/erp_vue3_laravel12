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
		hide-details="auto"
		clearable
	>
		<template v-slot:message="{ message }">{{ $t(message) }}</template>

		<template v-for="(_, name) in $slots" #[name]="slotProps">
			<slot :name="name" v-bind="slotProps ?? {}" />
		</template>
	</v-autocomplete>
	<v-select
		v-else
		v-bind="$attrs"
		variant="outlined"
		density="compact"
		hide-details="auto"
		clearable
	>
		<template v-slot:message="{ message }">{{ $t(message) }}</template>

		<template v-for="(_, name) in $slots" #[name]="slotProps">
			<slot :name="name" v-bind="slotProps ?? {}" />
		</template>
	</v-select>
</template>
