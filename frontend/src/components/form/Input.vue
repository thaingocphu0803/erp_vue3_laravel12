<template>
	<v-text-field v-bind="$attrs" validate-on="blur" density="compact" variant="outlined">
		<template v-slot:message="{ message }">{{ $t(message) }}</template>

		<template v-for="(_, name) in $slots" #[name]="slotProps">
			<slot :name="name" v-bind="slotProps ?? {}"></slot>
		</template>
	</v-text-field>
</template>
