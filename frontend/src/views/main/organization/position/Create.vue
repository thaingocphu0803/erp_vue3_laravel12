<script setup lang="ts">
import AppBreadcrumb from '@/components/layout/AppBreadcrumb.vue'
import PositionForm from '@/components/form/formModal/PositionForm.vue'
import router from '@/router'
import CONFIG from '@/config/constants'

// Handle success
const handleSuccess = () => {
	router.push({ name: 'org.position' })
}

// Handle cancel
const handleCancel = () => {
	router.back()
}
</script>

<template>
	<app-breadcrumb />

	<v-container :max-width="CONFIG.maxWidthForm">
		<!-- Position Form -->
		<position-form @save="handleSuccess" @cancel="handleCancel" />
	</v-container>
</template>

