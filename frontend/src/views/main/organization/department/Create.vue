<script setup lang="ts">
import AppBreadcrumb from '@/components/layout/AppBreadcrumb.vue'
import DepartmentForm from '@/components/form/formModal/DepartmentForm.vue'
import router from '@/router'
import CONFIG from '@/config/constants'

// Handle success
const handleSuccess = () => {
	router.push({ name: 'org.department' })
}

// Handle cancel
const handleCancel = () => {
	router.back()
}
</script>

<template>
	<app-breadcrumb />

	<v-container :max-width="CONFIG.maxWidthForm">
		<!-- Department Form -->
		<department-form @save="handleSuccess" @cancel="handleCancel" />
	</v-container>
</template>

