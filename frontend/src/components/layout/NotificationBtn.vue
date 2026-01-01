<script setup lang="ts">
import BaseIconBtn from '../BaseIconBtn.vue';
</script>

<template>
	<v-menu>
		<template v-slot:activator="{props}">
				<base-icon-btn tooltip="Notification" icon="mdi-bell-badge" class="d-none d-sm-block" v-bind="props"></base-icon-btn>
		</template>

		<v-list>
			<v-list-item>
			</v-list-item>
		</v-list>
	</v-menu>
</template>