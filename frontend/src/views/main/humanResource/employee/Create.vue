<script setup lang="ts">
import { ref, computed, reactive } from 'vue'
import AppBreadcrumb from '@/components/layout/AppBreadcrumb.vue'
import Form from '@/components/Form.vue'
import Input from '@/components/form/Input.vue'
import BaseBtn from '@/components/BaseBtn.vue'
import ListFilter from '@/components/list/ListFilter.vue'

// Account Settings
const email = ref('')
const role = ref(null)

// Basic Profile
const fullName = ref('')
const employeeCode = ref('')
const phone = ref('')
const address = ref('')
const province = ref(null)
const ward = ref(null)

const avatarPreview = ref('')
const avatarFile = ref<File | null>(null)

// Organization & Position
const department = ref(null)
const position = ref(null)

const isSubmitting = ref(false)

// Validation states (Mock)
const errorMessage = reactive({
	email: '',
	fullName: '',
	role: '',
	department: '',
	position: ''
})

// Mock Data for Dropdowns
const roles = [
	{ id: 1, name: 'Admin' },
	{ id: 2, name: 'Manager' },
	{ id: 3, name: 'Staff' }
]

const departments = [
	{ id: 1, name: 'IT' },
	{ id: 2, name: 'Khách hàng cá nhân' },
	{ id: 3, name: 'Marketing' }
]

const positions = [
	{ id: 1, name: 'Giám đốc' },
	{ id: 2, name: 'Trưởng phòng' },
	{ id: 3, name: 'Developer' }
]

const provinces = [
	{ id: 1, name: 'Hà Nội' },
	{ id: 2, name: 'Hồ Chí Minh' },
	{ id: 3, name: 'Đà Nẵng' }
]

const wards = [
	{ id: 1, name: 'Phường 1' },
	{ id: 2, name: 'Phường 2' },
	{ id: 3, name: 'Phường 3' }
]

// Computed for Avatar Initials
const avatarInitials = computed(() => {
	if (!fullName.value) return '?'
	return fullName.value.charAt(0).toUpperCase()
})

// Methods
const handleAvatarChange = (event: Event) => {
	const target = event.target as HTMLInputElement
	if (target.files && target.files[0]) {
		avatarFile.value = target.files[0]
		avatarPreview.value = URL.createObjectURL(target.files[0])
	}
}

const triggerAvatarUpload = () => {
    document.getElementById('avatar-upload')?.click()
}

const saveEmployee = () => {
	isSubmitting.value = true
	// Mock basic validation before mock submit
	let isValid = true
	if(!email.value) { errorMessage.email = "Vui lòng nhập Email"; isValid = false } else errorMessage.email = ''
	if(!fullName.value) { errorMessage.fullName = "Vui lòng nhập họ và tên"; isValid = false } else errorMessage.fullName = ''
	if(!role.value) { errorMessage.role = "Vui lòng chọn vai trò"; isValid = false } else errorMessage.role = ''
	if(!department.value) { errorMessage.department = "Vui lòng chọn phòng ban"; isValid = false } else errorMessage.department = ''
	if(!position.value) { errorMessage.position = "Vui lòng chọn chức vụ"; isValid = false } else errorMessage.position = ''

	if(!isValid) {
		isSubmitting.value = false;
		return
	}

	// Simulate API call
	setTimeout(() => {
		console.log('Form data:', {
			email: email.value,
			role: role.value,
			fullName: fullName.value,
			employeeCode: employeeCode.value,
			phone: '+84 ' + phone.value,
			province: province.value,
			ward: ward.value,
			address: address.value,
			department: department.value,
			position: position.value
		})
		isSubmitting.value = false
	}, 1000)
}

const cancel = () => {
	// Navigate back logic here
	console.log("Cancel clicked")
}

// Inline Creators mock
const createRole = () => console.log('Create Role clicked')
const createDepartment = () => console.log('Create Department clicked')
const createPosition = () => console.log('Create Position clicked')

</script>

<template>
	<app-breadcrumb class="mb-2" />

	<v-container class="employee-create mx-auto" style="max-width: 900px;">
		<v-card class="elevation-2 pa-4">
			<Form title="employee.title.create" @submit-form="saveEmployee">
				
				<!-- Avatar Upload (Centered) -->
				<div class="d-flex flex-column align-center justify-center mb-6">
					<v-avatar
						color="primary"
						size="100"
						class="mb-3 text-h3 text-white font-weight-bold"
						:image="avatarPreview ? avatarPreview : undefined"
					>
						<span v-if="!avatarPreview">{{ avatarInitials }}</span>
					</v-avatar>
					<input 
						type="file" 
						id="avatar-upload" 
						class="d-none" 
						accept="image/png, image/jpeg, image/jpg"
						@change="handleAvatarChange"
					>
					<v-btn
						color="primary"
						variant="outlined"
						size="small"
						class="text-none"
						prepend-icon="mdi-camera"
						@click="triggerAvatarUpload"
					>
						Tải ảnh lên
					</v-btn>
					<span class="text-caption text-grey mt-2">(Định dạng hỗ trợ: JPG, PNG)</span>
				</div>

				<v-divider class="mb-6"></v-divider>

				<!-- Section: Account Settings -->
				<h4 class="text-h6 font-weight-bold mb-4 text-primary">{{ $t('employee.section.account') }}</h4>
				<v-row dense>
					<v-col cols="12" sm="6">
						<Input
							v-model="email"
							:error-messages="errorMessage.email"
							placeholder="example@company.com"
							required
						>
							<template #label>{{ $t('employee.input.email') }} <span class="text-error">*</span></template>
						</Input>
					</v-col>
					<v-col cols="12" sm="6">
						<v-row dense align="center">
							<v-col class="flex-grow-1">
								<list-filter
									label="Vai trò"
									v-model="role"
									:items="roles"
									searchable
									item-title="name"
									item-value="id"
									:error-messages="errorMessage.role"
								/>
							</v-col>
							<v-col cols="auto" class="mb-4">
								<v-btn icon="mdi-plus" size="small" color="primary" variant="tonal" @click.prevent="createRole" title="Tạo vai trò"></v-btn>
							</v-col>
						</v-row>
					</v-col>
					<v-col cols="12">
						<v-alert
							type="info"
							variant="tonal"
							density="compact"
							class="text-caption mt-2"
						>
							Mật khẩu sẽ được hệ thống tạo tự động và gửi qua email cho nhân sự sau khi lưu thành công.
						</v-alert>
					</v-col>
				</v-row>

				<v-divider class="my-6"></v-divider>

				<!-- Section: Basic Profile -->
				<h4 class="text-h6 font-weight-bold mb-4 text-primary">{{ $t('employee.section.profile') }}</h4>
				<v-row dense>
					<v-col cols="12" sm="6">
						<Input
							v-model="fullName"
							:error-messages="errorMessage.fullName"
							required
						>
							<template #label>{{ $t('employee.input.fullName') }} <span class="text-error">*</span></template>
						</Input>
					</v-col>
					<v-col cols="12" sm="6">
						<Input
							v-model="employeeCode"
							:label="$t('employee.input.employeeCode')"
							placeholder="Tự động sinh nếu để trống"
						/>
					</v-col>

					<!-- Phone -->
					<v-col cols="12" sm="6">
						<Input
							v-model="phone"
							:label="$t('employee.input.phone')"
							placeholder="Ví dụ: 987654321"
							prefix="+84 "
						/>
					</v-col>

					<!-- Address Split (1 row) -->
					<v-col cols="12" sm="4">
						<list-filter
							label="Tỉnh/Thành phố"
							v-model="province"
							:items="provinces"
							searchable
							item-title="name"
							item-value="id"
						/>
					</v-col>
					<v-col cols="12" sm="4">
						<list-filter
							label="Phường/Xã"
							v-model="ward"
							:items="wards"
							searchable
							item-title="name"
							item-value="id"
						/>
					</v-col>
					<v-col cols="12" sm="4">
						<Input
							v-model="address"
							:label="$t('employee.input.address')"
							placeholder="Số nhà, Tên đường..."
						/>
					</v-col>
				</v-row>

				<v-divider class="my-6"></v-divider>

				<!-- Section: Organization & Position -->
				<h4 class="text-h6 font-weight-bold mb-4 text-primary">{{ $t('employee.section.organization') }}</h4>
				<v-row dense>
					<v-col cols="12" sm="6">
						<v-row dense align="center">
							<v-col class="flex-grow-1">
								<list-filter
									label="Phòng ban"
									v-model="department"
									:items="departments"
									searchable
									item-title="name"
									item-value="id"
									:error-messages="errorMessage.department"
								/>
							</v-col>
							<v-col cols="auto" class="mb-4">
								<v-btn icon="mdi-plus" size="small" color="primary" variant="tonal" @click.prevent="createDepartment" title="Tạo phòng ban"></v-btn>
							</v-col>
						</v-row>
					</v-col>
					<v-col cols="12" sm="6">
						<v-row dense align="center">
							<v-col class="flex-grow-1">
								<list-filter
									label="Chức vụ"
									v-model="position"
									:items="positions"
									searchable
									item-title="name"
									item-value="id"
									:error-messages="errorMessage.position"
								/>
							</v-col>
							<v-col cols="auto" class="mb-4">
								<v-btn icon="mdi-plus" size="small" color="primary" variant="tonal" @click.prevent="createPosition" title="Tạo chức vụ"></v-btn>
							</v-col>
						</v-row>
					</v-col>
				</v-row>

				<!-- Actions: Cancel (red) + Create (blue) at the bottom -->
				<v-row dense justify="space-between" class="mt-8">
					<v-col cols="auto">
						<BaseBtn title="common.btn.cancel" color="red-darken-1" @click.prevent="cancel" />
					</v-col>
					<v-col cols="auto">
						<BaseBtn title="common.btn.create" color="primary" type="submit" :loading="isSubmitting" />
					</v-col>
				</v-row>

			</Form>
		</v-card>
	</v-container>
</template>

<style scoped>
/* No specific styles needed anymore since grid is simple and buttons are at the bottom */
</style>