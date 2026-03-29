<script setup lang="ts">
import AppBreadcrumb from '@/components/layout/AppBreadcrumb.vue'
import RoleForm from '@/components/form/formModal/RoleForm.vue'
import router from '@/router'
import defaultConfig from '@/config/default'

const handleSuccess = () => {
	router.push({ name: 'org.role' })
}

const handleCancel = () => {
	router.back()
}
</script>

<template>
	<app-breadcrumb />

	<v-container :max-width="defaultConfig.maxWidthForm">
		<role-form @save="handleSuccess" @cancel="handleCancel" />
	</v-container>
</template>
