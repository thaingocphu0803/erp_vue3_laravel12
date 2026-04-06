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

interface EmployeeForm {
	avatar: File | null
	email: string
	role: number | null
	fullName: string
	code: string
	gender: string | null
	birthDate: string
	phone: string
	province: string | null
	ward: string | null
	address: string
	department: number | null
	position: number | null
	isLeader: boolean
}

interface ValidateMessage {
	email: string
	fullName: string
	role: string
	department: string
	position: string
	gender: string
	birthDate: string
	phone: string
	province: string
	ward: string
	address: string
}

const employeeData = reactive<EmployeeForm>({
	avatar: null,
	email: '',
	role: null,
	fullName: '',
	code: '',
	gender: null,
	birthDate: '',
	phone: '',
	province: null,
	ward: null,
	address: '',
	department: null,
	position: null,
	isLeader: false,
})

// Validation states (Mock)
const errorMessage = reactive<ValidateMessage>({
	email: '',
	fullName: '',
	role: '',
	department: '',
	position: '',
	gender: '',
	birthDate: '',
	phone: '',
	province: '',
	ward: '',
	address: '',
})

const sections = [
	{ id: 1, title: 'employee.section.account' },
	{ id: 2, title: 'employee.section.profile' },
	{ id: 3, title: 'employee.section.organization' },
]

const computedAvatarPreview = computed<string>(() => {
	if (employeeData.avatar) {
		return URL.createObjectURL(employeeData.avatar)
	}
	return defaultConfig.avatar
})

const handleSubmit = () => {
	try {
		console.log(employeeData)
	} catch (error: any) {
		console.log(error)
	}
}

const cancel = () => {
	console.log('Cancel clicked')
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
					:image="computedAvatarPreview"
				>
				</v-avatar>
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
					v-model:role="employeeData.role"
				/>

				<!-- Section: Basic Profile -->
				<profile-section
					v-else-if="section.id === 2"
					v-model:full-name="employeeData.fullName"
					v-model:code="employeeData.code"
					v-model:gender="employeeData.gender"
					v-model:birth-date="employeeData.birthDate"
					v-model:phone="employeeData.phone"
					v-model:address="employeeData.address"
					v-model:ward="employeeData.ward"
					v-model:province="employeeData.province"
				/>

				<!-- Section: Organization & Position -->
				<organization-section
					v-else
					v-model:department="employeeData.department"
					v-model:position="employeeData.position"
					v-model:is-leader="employeeData.isLeader"
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
