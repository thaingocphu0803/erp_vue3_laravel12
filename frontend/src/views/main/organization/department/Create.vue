<script setup lang="ts">
import AppBreadcrumb from '@/components/layout/AppBreadcrumb.vue'
import DepartmentForm from '../../../../components/form/commonForm/DepartmentForm.vue'
import router from '@/router'
import defaultConfig from '@/config/default'

const handleSuccess = () => {
	router.push({ name: 'org.department' })
}

const handleCancel = () => {
	router.back()
}
</script>

<template>
	<app-breadcrumb />

	<v-container :max-width="defaultConfig.maxWidthForm">
		<department-form @success="handleSuccess" @cancel="handleCancel"/>
	</v-container>
</template>
