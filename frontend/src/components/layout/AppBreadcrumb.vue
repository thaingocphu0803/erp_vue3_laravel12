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

	const routesMatch = route.matched.filter((routeItem) => routeItem.meta && routeItem.meta.title)

	return routesMatch.map((routeItem, index) => ({
			title: routeItem.meta.title as string,
			to: { name: routeItem.name as string },
			disabled: index === routesMatch.length - 1 || !!routeItem.meta.disabled,
	}))
})
</script>

<template>
	<v-breadcrumbs :items="breadcrumbItems" class="text-caption text-sm-body-1 text-md-h6" color="primary">
		<template v-slot:divider>
			<v-icon icon="mdi-chevron-right"></v-icon>
		</template>
		<template v-slot:title="{ item }">
			<v-breadcrumbs-item
				:to="item.to"
				:disabled="item.disabled"
				class="text-capitalize">
				{{ $t(item.title) }}
			</v-breadcrumbs-item>
		</template>
	</v-breadcrumbs>
</template>
