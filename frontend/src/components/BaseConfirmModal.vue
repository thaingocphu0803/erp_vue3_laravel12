<script lang="ts" setup>
import CONFIG from '@/config/constants'

interface Props {
	title: string
	titleIcon?: string
	content: string
	titleConfirmBtn: string
	loading: boolean
}

const props = defineProps<Props>()

const emit = defineEmits(['confirm', 'cancel'])

const handleConfirm = () => {
	emit('confirm')
}

const handleCancel = () => {
	emit('cancel')
}
</script>

<template>
	<!-- Confirm Delete Dialog -->
	<v-dialog :max-width="CONFIG.maxWidthConfirmModal" v-bind="$attrs">
		<v-card>
			<v-card-title class="text-h6 font-weight-bold d-flex align-center text-capitalize">
				<v-icon v-if="titleIcon" color="error" class="mr-2">{{ titleIcon }}</v-icon>
				{{ title }}
			</v-card-title>
			<v-card-text>
				{{ content }}
			</v-card-text>
			<v-card-actions class="pa-4 pt-0">
				<v-spacer></v-spacer>
				<v-btn
					color="grey-darken-1"
					variant="text"
					class="text-none"
					@click="handleCancel"
					>{{ $t('common.btn.cancel') }}</v-btn
				>
				<v-btn
					color="error"
					variant="flat"
					class="text-none"
					:loading
					@click="handleConfirm"
					>{{ titleConfirmBtn }}</v-btn
				>
			</v-card-actions>
		</v-card>
	</v-dialog>
</template>

