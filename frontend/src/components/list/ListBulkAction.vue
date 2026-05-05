<script lang="ts" setup>
import { ref } from 'vue'
import BaseConfirmModal from '../BaseConfirmModal.vue'
import BaseBtn from '../BaseBtn.vue'

interface Props {
	selectedItems: number[]
}

const props = defineProps<Props>()

const bulkDeleteDialog = ref(false)
const loading = ref(false)
</script>

<template>
	<v-expand-transition>
		<v-card v-if="selectedItems.length > 0" class="elevation-1 mb-4 pa-4">
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
						color="success"
						variant="flat"
						prepend-icon="mdi-check-circle"
						title="common.btn.active"
					/>

					<base-btn
						color="grey"
						variant="flat"
						prepend-icon="mdi-minus-circle"
						title="common.btn.inactive"
					/>

					<base-btn
						color="error"
						variant="flat"
						prepend-icon="mdi-delete"
						title="common.btn.delete"
						@click="bulkDeleteDialog = true"
					/>
				</v-col>
			</v-row>
		</v-card>
	</v-expand-transition>

	<!-- Bulk Delete Confirm Modal -->
	<base-confirm-modal
		:loading
		v-model="bulkDeleteDialog"
		:title="$t('common.confirmModal.delete.title')"
		:content="
			$t('common.action.bulkAction.deleteModal.content', {
				count: selectedItems.length,
			})
		"
		:titleConfirmBtn="$t('common.btn.delete')"
		@cancel="bulkDeleteDialog = false"
	></base-confirm-modal>
</template>

