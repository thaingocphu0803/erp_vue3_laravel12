<script setup lang="ts">
import AppBreadcrumb from '@/components/layout/AppBreadcrumb.vue'
import PositionForm from '../../../../components/form/commonForm/PositionForm.vue'
import router from '@/router'
import defaultConfig from '@/config/default'

const handleSuccess = () => {
	router.push({ name: 'org.position' })
}

const handleCancel = () => {
	router.back()
}
</script>

<template>
	<app-breadcrumb />

	<v-container :max-width="defaultConfig.maxWidthForm">
		<position-form @success="handleSuccess" @cancel="handleCancel" />
	</v-container>
</template>
