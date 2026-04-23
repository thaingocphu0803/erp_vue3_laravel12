<script setup lang="ts">
import BaseIconBtn from '../BaseIconBtn.vue'
import CONFIG from '@/config/constants'
import BaseBtn from '../BaseBtn.vue'

const tooltip = 'common.button.notification'
const title = 'common.button.seeAll'
const subtitle = 'common.subtitle.notification'
</script>

<template>
	<!-- Menu Notification -->
	<v-menu location="bottom center" transition="slide-y-transition">
		<!-- Activator -->
		<template v-slot:activator="{ props: menuProps }">
			<base-icon-btn
				:tooltip
				icon="mdi-bell-badge"
				class="d-none d-sm-block"
				v-bind="menuProps"
			></base-icon-btn>
		</template>

		<!-- Notification List -->
		<v-list max-width="420px" density="comfortable" class="pb-0" lines="two">
			<v-list-subheader :title="$t(subtitle)" sticky></v-list-subheader>

			<v-divider></v-divider>

			<!-- Notification Item -->
			<v-list-item :prepend-avatar="CONFIG.avatar" density="comfortable">
				<v-list-item-title>Thai Ngoc Phu</v-list-item-title>
				<v-list-item-subtitle>
					I'll be in your neighborhood doing errands this weekend. Do you want to hang
					out?
				</v-list-item-subtitle>
			</v-list-item>

			<v-list-item :prepend-avatar="CONFIG.avatar" density="comfortable">
				<v-list-item-title>Thai Ngoc Phu</v-list-item-title>
				<v-list-item-subtitle>
					I'll be in your neighborhood doing errands this weekend. Do you want to hang
					out?
				</v-list-item-subtitle>
			</v-list-item>

			<v-divider></v-divider>

			<!-- See All Button -->
			<base-btn block tile :title></base-btn>
		</v-list>
	</v-menu>
</template>
