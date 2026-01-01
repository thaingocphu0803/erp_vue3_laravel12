<script setup lang="ts">
	import BaseIconBtn from '../BaseIconBtn.vue';
	import { languageMenu } from '@/config/menu';
</script>
<template>
	<v-menu location="bottom"  transition="slide-y-transition">
		<template v-slot:activator="{ props}">
				<base-icon-btn v-bind="props" tooltip="Languages" icon="mdi-translate" class="d-none d-sm-block"></base-icon-btn>
		</template>

		<v-list>
			<v-list-item v-for="item in languageMenu" :key="item.value" :value="item.value" :title="item.title">
				
			</v-list-item>
		</v-list>
	</v-menu>
</template>