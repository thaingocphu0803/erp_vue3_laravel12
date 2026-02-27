<script setup lang="ts">
import { computed } from 'vue'
import { useRoute, type RouteLocationRaw } from 'vue-router'

interface breadcrumbItem {
	title: string
	disabled: boolean
	to: RouteLocationRaw
}
const route = useRoute()

const breadcrumbItems = computed<breadcrumbItem[]>(() => {
	const items = [
		{
			title: 'common.module.dashboard',
			to: { name: 'dashboard' },
			disabled: route.name === 'dashboard',
		},
	]

	const routesMatch = route.matched.filter((routeItem) => routeItem.meta && routeItem.meta.title)

	routesMatch.map((routeItem, index) => {
		items.push({
			title: routeItem.meta.title as string,
			to: { name: routeItem.name as string },
			disabled: index === routesMatch.length - 1 || !!routeItem.meta.disabled,
		})
	})

	return items
})
</script>

<template>
	<v-breadcrumbs :items="breadcrumbItems" class="text-caption text-sm-body-1 text-md-h6">
		<template v-slot:divider>
			<v-icon icon="mdi-chevron-right"></v-icon>
		</template>
		<template v-slot:title="{ item }">
			{{ $t(item.title) }}
		</template>
	</v-breadcrumbs>
</template>
