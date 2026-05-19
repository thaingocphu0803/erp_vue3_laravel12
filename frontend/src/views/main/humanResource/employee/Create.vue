<script setup lang="ts">
import { computed, onMounted, reactive } from 'vue'
import AppBreadcrumb from '@/components/layout/AppBreadcrumb.vue'
import Form from '@/components/Form.vue'
import BaseBtn from '@/components/BaseBtn.vue'
import CONFIG from '@/config/constants'
import useEmployeeValidation from '@/composables/validation/useEmployeeValidation'
import AccountSection from '@/views/main/humanResource/employee/components/AccountSection.vue'
import ProfileSection from '@/views/main/humanResource/employee/components/ProfileSection.vue'
import OrganizationSection from '@/views/main/humanResource/employee/components/OrganizationSection.vue'
import ErrorAlert from '@/components/form/ErrorAlert.vue'
import { useEmployeeStore } from '@/stores/employee'
import { formatLaravelRetryAfter, mapLaravelError } from '@/utils/errorHandler'
import { useToastStore } from '@/stores/toast'
import { useThrottleStore } from '@/stores/throttle'
import ThrottleAlert from '@/components/ThrottleAlert.vue'
import router from '@/router'
import SYSTEM from '@/config/system'

import type { EmployeeFormData, EmployeeFormError } from '@/types/employee'
import { storeToRefs } from 'pinia'

// employee validation
const { employeeValidation } = useEmployeeValidation()

// employee store
const { employeeCreate } = useEmployeeStore()

// toast store
const toast = useToastStore()

// throttle store
const { throttle, isDisabled } = storeToRefs(useThrottleStore())
const { initThrottle, startThrottle } = useThrottleStore()

// employee form data
const employeeFormData = reactive<EmployeeFormData>({
	avatar: null,
	email: '',
	role_ids: [],
	name: '',
	code: '',
	gender: null,
	birth_date: '',
	phone_number: '',
	province_code: null,
	ward_code: null,
	address: '',
	department_id: null,
	position_id: null,
	is_leader: false,
	locale: 'en',
})

// Validation states (Mock)
const errorMessage = reactive<EmployeeFormError>({
	avatar: '',
	email: '',
	name: '',
	role_ids: '',
	department_id: '',
	position_id: '',
	gender: '',
	birth_date: '',
	phone_number: '',
	province_code: '',
	ward_code: '',
	address: '',
	is_leader: '',
	code: '',
	locale: '',
})

// Sections
const sections = [
	{ id: 1, title: 'employee.section.account' },
	{ id: 2, title: 'employee.section.profile' },
	{ id: 3, title: 'employee.section.organization' },
]

// Review avatar
const avatarReview = computed<string>(() => {
	if (employeeFormData.avatar) {
		return URL.createObjectURL(employeeFormData.avatar)
	}
	return CONFIG.avatar
})

// Handle submit
const handleSubmit = async () => {
	try {
		const response = await employeeCreate(employeeFormData)
		toast.show(response.data.messageCode, 'success')
		router.push({ name: 'hr.employee' })
	} catch (error: any) {
		if (error.status === SYSTEM.SERVER_ERROR.UNPROCESSABLE_ENTITY) {
			mapLaravelError(errorMessage, error)
		}

		if (error.status === SYSTEM.SERVER_ERROR.TOO_MANY_REQUESTS) {
			throttle.value['employeeCreate'] = formatLaravelRetryAfter(error)
			startThrottle('employeeCreate')
		}

		if (error.status === SYSTEM.SERVER_ERROR.INTERNAL_SERVER_ERROR) {
			const messageCode = 'employee.alert.error.create'
			toast.show(messageCode, 'error')
		}
	}
}

// Cancel
const cancel = () => {
	router.back()
}

onMounted(() => {
	initThrottle('employeeCreate')
})
</script>

<template>
	<app-breadcrumb class="mb-2" />

	<!-- Employee Create -->
	<v-container class="employee-create mx-auto" max-width="900px">
		<Form title="employee.title.create" @submit-form="handleSubmit">
			<!-- Error Alert -->
			<error-alert :messages="errorMessage" />

			<!-- Avatar Upload (Centered) -->
			<div class="d-flex flex-column align-center justify-center mb-6">
				<!-- Avatar -->
				<v-avatar variant="plain" color="primary" size="150" class="mb-3 text-h3 text-white font-weight-bold"
					:image="avatarReview" />

				<!-- Upload Avatar Button -->
				<v-file-input v-model="employeeFormData.avatar" accept="image/png, image/jpeg, image/jpg"
					:label="$t('employee.input.uploadAvatar')" variant="solo-inverted" density="compact"
					prepend-icon="mdi-camera" glow icon-color="primary" class="mt-2" min-width="250px"
					validate-on="blur" :rules="employeeValidation.avatar">
					<template v-slot:message="{ message }">{{ $t(message) }}</template>
				</v-file-input>

				<!-- Upload Avatar Tooltip -->
				<span class="text-caption text-grey mt-1">{{
					$t('employee.tooltip.uploadAvatar')
				}}</span>
			</div>

			<v-divider class="mb-6"></v-divider>

			<template v-for="section in sections" :key="section.id">
				<!-- Section Title -->
				<h4 class="text-h6 font-weight-bold mb-4 text-primary">{{ $t(section.title) }}</h4>

				<!-- Account Section -->
				<account-section v-if="section.id === 1" v-model:email="employeeFormData.email"
					v-model:role_ids="employeeFormData.role_ids" />

				<!-- Profile Section -->
				<profile-section v-else-if="section.id === 2" v-model:name="employeeFormData.name"
					v-model:code="employeeFormData.code" v-model:gender="employeeFormData.gender"
					v-model:birth_date="employeeFormData.birth_date"
					v-model:phone_number="employeeFormData.phone_number" v-model:address="employeeFormData.address"
					v-model:ward_code="employeeFormData.ward_code"
					v-model:province_code="employeeFormData.province_code" v-model:locale="employeeFormData.locale" />

				<!-- Organization Section -->
				<organization-section v-else v-model:department_id="employeeFormData.department_id"
					v-model:position_id="employeeFormData.position_id" v-model:is_leader="employeeFormData.is_leader" />

				<v-divider v-if="section.id !== sections.length" class="my-6"></v-divider>
			</template>

			<!-- Throttle Alert -->
			<v-row dense justify="center">
				<throttle-alert :time="throttle['employeeCreate'] || 0" :show="isDisabled('employeeCreate')" />
			</v-row>

			<!-- Actions-->
			<v-row dense justify="space-between" class="mt-8">
				<v-col cols="auto">
					<BaseBtn title="common.btn.cancel" color="red-darken-1" @click.prevent="cancel" />
				</v-col>
				<v-col cols="auto">
					<BaseBtn title="common.btn.create" color="primary" type="submit"
						:disabled="isDisabled('employeeCreate')" />
				</v-col>
			</v-row>
		</Form>
	</v-container>
</template>
