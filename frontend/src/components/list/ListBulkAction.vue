<script lang="ts" setup>
import { ref } from 'vue'
import BaseConfirmModal from '../BaseConfirmModal.vue'
import BaseBtn from '../BaseBtn.vue'
import CONFIG from '@/config/constants'
import { useThrottleStore } from '@/stores/throttle'
import { storeToRefs } from 'pinia'

interface Props {
	selectedItems: number[]
	isBulkProccessing: string | null
}

const props = defineProps<Props>()

const showDeleteDialog = ref(false)

const emit = defineEmits(['bulkDelete', 'bulkActive', 'bulkInactive'])

const { isDisabled } = storeToRefs(useThrottleStore())

// handle bulk delete
const handleBulkDelete = () => {
	emit('bulkDelete')
	showDeleteDialog.value = false
}

// handle bulk active
const handleBulkActive = () => {
	emit('bulkActive', CONFIG.active)
}

// handle bulk inactive
const handleBulkInactive = () => {
	emit('bulkInactive', CONFIG.inactive)
}
</script>

<template>
	<v-expand-transition>
		<v-card
			v-show="selectedItems.length > 0"
			class="elevation-1 mb-4 pa-4"
			color="grey-lighten-3"
		>
			<v-row dense align="center">
				<v-col cols="12" class="d-flex align-center flex-wrap ga-2">
					<span class="mr-4 font-weight-medium">
						{{
							$t('common.action.bulkAction.selectedItems', {
								count: selectedItems.length,
							})
						}}
					</span>

					<base-btn
						class="text-none"
						color="primary"
						density="comfortable"
						variant="flat"
						prepend-icon="mdi-check-circle"
						title="common.btn.active"
						:loading="isBulkProccessing === CONFIG.active"
						:disabled="
							(isBulkProccessing && isBulkProccessing !== CONFIG.active) ||
							isDisabled(`roleBulkUpdateStatus:${CONFIG.active}`)
						"
						@click.stop="handleBulkActive"
					/>

					<base-btn
						class="text-none"
						color="primary"
						density="comfortable"
						variant="flat"
						prepend-icon="mdi-minus-circle"
						title="common.btn.inactive"
						:loading="isBulkProccessing === CONFIG.inactive"
						:disabled="
							(isBulkProccessing && isBulkProccessing !== CONFIG.inactive) ||
							isDisabled(`roleBulkUpdateStatus:${CONFIG.inactive}`)
						"
						@click.stop="handleBulkInactive"
					/>

					<base-btn
						class="text-none"
						color="primary"
						density="comfortable"
						variant="flat"
						prepend-icon="mdi-delete"
						title="common.btn.delete"
						:loading="isBulkProccessing === CONFIG.delete"
						:disabled="
							(isBulkProccessing && isBulkProccessing !== CONFIG.delete) ||
							isDisabled('roleBulkDelete')
						"
						@click.stop="showDeleteDialog = true"
					/>
				</v-col>
			</v-row>
		</v-card>
	</v-expand-transition>

	<!-- Bulk Delete Confirm Modal -->
	<base-confirm-modal
		v-model="showDeleteDialog"
		:title="$t('common.confirmModal.delete.title')"
		:content="
			$t('common.action.bulkAction.deleteModal.content', {
				count: selectedItems.length,
			})
		"
		:titleConfirmBtn="$t('common.btn.delete')"
		@cancel="showDeleteDialog = false"
		@confirm="handleBulkDelete"
	></base-confirm-modal>
</template>

