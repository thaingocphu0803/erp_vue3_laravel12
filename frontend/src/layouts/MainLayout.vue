<script setup lang="ts">
import LayoutBar from '@/components/layout/LayoutBar.vue';
import LayoutNav from '@/components/layout/LayoutNav.vue';

</script>

<template>
	<v-layout>
		<layout-bar></layout-bar>
		<layout-nav></layout-nav>
		<v-main>
			<router-view></router-view>
		</v-main>
	</v-layout>
</template>