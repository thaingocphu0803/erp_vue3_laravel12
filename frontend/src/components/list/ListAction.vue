<script lang="ts" setup>
import BaseIconBtn from '@/components/BaseIconBtn.vue'

const emit = defineEmits(['delete', 'edit'])

const handleDelete = () => {
	emit('delete')
}

const handleEdit = () => {
	emit('edit')
}
</script>

<template>
	<v-btn-group density="comfortable" variant="outlined" divided>
		<!-- edit icon button -->
		<base-icon-btn
			icon="mdi-pencil"
			tooltip="common.btn.edit"
			@click="handleEdit"
		></base-icon-btn>

		<!-- delete icon button -->
		<base-icon-btn
			icon="mdi-delete"
			color="error"
			tooltip="common.btn.delete"
			@click="handleDelete"
		></base-icon-btn>
	</v-btn-group>
</template>

