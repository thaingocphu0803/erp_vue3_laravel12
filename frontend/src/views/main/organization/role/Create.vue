<script setup lang="ts">
import AppBreadcrumb from '@/components/layout/AppBreadcrumb.vue'
import RoleForm from '@/components/form/formModal/RoleForm.vue'
import router from '@/router'
import CONFIG from '@/config/constants'

// handle success
const handleSuccess = () => {
	router.push({ name: 'org.role' })
}

// handle cancel
const handleCancel = () => {
	router.back()
}
</script>

<template>
	<!-- Breadcrumb -->
	<app-breadcrumb />

	<!-- Main Container -->
	<v-container :max-width="CONFIG.maxWidthForm">
		<!-- Role Form -->
		<role-form @save="handleSuccess" @cancel="handleCancel" />
	</v-container>
</template>

