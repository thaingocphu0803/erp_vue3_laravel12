<script setup lang="ts">
import BaseIconBtn from '../BaseIconBtn.vue'
import { languageMenu } from '@/config/menu'
import { changeLanguage } from '@/composables/useLanguage';

const tooltip: string = 'common.button.language'
</script>
<template>
	<v-menu location="bottom center" transition="slide-y-transition">
		<template v-slot:activator="{ props: menuProps }">
			<base-icon-btn v-bind="menuProps" :tooltip icon="mdi-translate" class="d-none d-sm-block"></base-icon-btn>
		</template>

		<v-list>
			<v-list-item v-for="item in languageMenu" :key="item.value" :value="item.value" :title="$t(item.title)"
				@click="changeLanguage(item.value)">
			</v-list-item>
		</v-list>
	</v-menu>
</template>
