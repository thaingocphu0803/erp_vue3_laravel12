<script setup lang="ts">
import { computed, reactive } from 'vue'
import AppBreadcrumb from '@/components/layout/AppBreadcrumb.vue'
import Form from '@/components/Form.vue'
import BaseBtn from '@/components/BaseBtn.vue'
import defaultConfig from '@/config/default'
import employeeValidation from '@/composables/validation/useEmployeeValidation'
import AccountSection from '@/views/main/humanResource/employee/components/AccountSection.vue'
import ProfileSection from '@/views/main/humanResource/employee/components/ProfileSection.vue'
import OrganizationSection from '@/views/main/humanResource/employee/components/OrganizationSection.vue'
import ErrorAlert from '@/components/form/ErrorAlert.vue'
import { useEmployeeStore } from '@/stores/employee'
import { mapLaravelError } from '@/utils/errorHandler'
import { useToastStore } from '@/stores/toast'
import router from '@/router'

interface EmployeeForm {
	avatar: File | null
	email: string
	role_ids: number[]
	name: string
	code: string
	gender: string | null
	birth_date: string | null
	phone_number: string
	province_code: string | null
	ward_code: string | null
	address: string
	department_id: number | null
	position_id: number | null
	is_leader: boolean
}

interface ErrorMessage {
	avatar: string
	email: string
	name: string
	role_ids: string
	department_id: string
	position_id: string
	gender: string
	birth_date: string
	phone_number: string
	province_code: string
	ward_code: string
	address: string
	is_leader: string
	code: string
}

const { employeeCreate } = useEmployeeStore()
const toast = useToastStore()

const employeeData = reactive<EmployeeForm>({
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
})

// Validation states (Mock)
const errorMessage = reactive<ErrorMessage>({
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
})

const sections = [
	{ id: 1, title: 'employee.section.account' },
	{ id: 2, title: 'employee.section.profile' },
	{ id: 3, title: 'employee.section.organization' },
]

const avatarReview = computed<string>(() => {
	if (employeeData.avatar) {
		return URL.createObjectURL(employeeData.avatar)
	}
	return defaultConfig.avatar
})

const handleSubmit = async () => {
	try {
		const response = await employeeCreate(employeeData)
		toast.show(response.data.messageCode, 'success')
		router.push({ name: 'hr.employee' })
	} catch (error: any) {
		if (error.status === 422) {
			mapLaravelError(errorMessage, error)
			return
		}
		toast.show(error.response?.data?.messageCode, 'error')
	}
}

const cancel = () => {
	router.back()
}
</script>

<template>
	<app-breadcrumb class="mb-2" />

	<v-container class="employee-create mx-auto" max-width="900px">
		<Form title="employee.title.create" @submit-form="handleSubmit">
			<error-alert :messages="errorMessage" />
			<!-- Avatar Upload (Centered) -->
			<div class="d-flex flex-column align-center justify-center mb-6">
				<v-avatar
					variant="plain"
					color="primary"
					size="150"
					class="mb-3 text-h3 text-white font-weight-bold"
					:image="avatarReview"
				/>

				<v-file-input
					v-model="employeeData.avatar"
					accept="image/png, image/jpeg, image/jpg"
					:label="$t('employee.input.uploadAvatar')"
					variant="solo-inverted"
					density="compact"
					prepend-icon="mdi-camera"
					glow
					icon-color="primary"
					class="mt-2"
					min-width="250px"
					validate-on="blur"
					:rules="employeeValidation.avatar"
				>
					<template v-slot:message="{ message }">{{ $t(message) }}</template>
				</v-file-input>
				<span class="text-caption text-grey mt-1">{{
					$t('employee.tooltip.uploadAvatar')
				}}</span>
			</div>

			<v-divider class="mb-6"></v-divider>
			<template v-for="section in sections" :key="section.id">
				<h4 class="text-h6 font-weight-bold mb-4 text-primary">{{ $t(section.title) }}</h4>

				<!-- Section: Account -->
				<account-section
					v-if="section.id === 1"
					v-model:email="employeeData.email"
					v-model:role_ids="employeeData.role_ids"
				/>

				<!-- Section: Basic Profile -->
				<profile-section
					v-else-if="section.id === 2"
					v-model:name="employeeData.name"
					v-model:code="employeeData.code"
					v-model:gender="employeeData.gender"
					v-model:birth_date="employeeData.birth_date"
					v-model:phone_number="employeeData.phone_number"
					v-model:address="employeeData.address"
					v-model:ward_code="employeeData.ward_code"
					v-model:province_code="employeeData.province_code"
				/>

				<!-- Section: Organization & Position -->
				<organization-section
					v-else
					v-model:department_id="employeeData.department_id"
					v-model:position_id="employeeData.position_id"
					v-model:is_leader="employeeData.is_leader"
				/>

				<v-divider v-if="section.id !== sections.length" class="my-6"></v-divider>
			</template>

			<!-- Actions: Cancel (red) + Create (blue) at the bottom -->
			<v-row dense justify="space-between" class="mt-8">
				<v-col cols="auto">
					<BaseBtn
						title="common.btn.cancel"
						color="red-darken-1"
						@click.prevent="cancel"
					/>
				</v-col>
				<v-col cols="auto">
					<BaseBtn title="common.btn.create" color="primary" type="submit" />
				</v-col>
			</v-row>
		</Form>
	</v-container>
</template>

