<script setup lang="ts">
import { computed } from 'vue'
import { useToastStore } from '@/stores/toast'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
const toast = useToastStore()

const color = computed(() => {
	const map: Record<string, string> = {
		success: 'success',
		error: 'error',
		warning: 'warning',
		info: 'info',
	}
	return map[toast.type] ?? 'info'
})

const icon = computed(() => {
	const map: Record<string, string> = {
		success: 'mdi-check-circle-outline',
		error: 'mdi-alert-circle-outline',
		warning: 'mdi-alert-outline',
		info: 'mdi-information-outline',
	}
	return map[toast.type] ?? 'mdi-information-outline'
})
</script>

<template>
	<v-snackbar
		v-model="toast.visible"
		:timeout="toast.duration"
		:color="color"
		location="top right"
		rounded="lg"
	>

			<div class="d-flex ga-2 align-center">
				<v-icon :icon="icon" size="20"/>
				<span class="text-body-1 font-weight-medium">{{ $t(toast.message) }}</span>
			</div>

		<template #actions>
			<v-btn icon size="small" variant="text" @click="toast.hide()">
				<v-icon icon="mdi-close" size="18" />
			</v-btn>
		</template>
	</v-snackbar>
</template>
