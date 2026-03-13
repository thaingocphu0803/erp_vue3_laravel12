<script setup lang="ts">
import Form from '@/components/Form.vue'
import Input from '@/components/form/Input.vue'
import BaseBtn from '@/components/BaseBtn.vue'
import Textarea from '@/components/form/Textarea.vue'
import ListFilter from '@/components/list/ListFilter.vue'
import positionValidation from '@/composables/validation/usePositionValidation'
import DepartmentForm from './DepartmentForm.vue'
import { reactive, ref } from 'vue'
import { useDepartmentStore } from '@/stores/department'
import { storeToRefs } from 'pinia'
import { mapLaravelError } from '@/utils/errorHandler'
import { useToastStore } from '@/stores/toast'
import defaultConfig from '@/config/default'

interface PositionForm {
	name: string
	department_id: number | null
	description: string
	permissions: number[]
}

interface ValidateMessage {
	name: string
}

interface Permission {
	id: number
	name: string
	label: string
}

interface PermissionGroup {
	key: string
	label: string
	permissions: Permission[]
}

const emit = defineEmits(['success', 'cancel'])

const departmentStore = useDepartmentStore()
const toast = useToastStore()
const { departments } = storeToRefs(departmentStore)

const loading = ref<boolean>(false)
const disabledSelect = ref<boolean>(false)
const getParentErrorMessage = ref<string>('')

// Mock permission groups – replace with API data when ready
const permissionGroups = ref<PermissionGroup[]>([
	{
		key: 'employee',
		label: 'Nhân viên',
		permissions: [
			{ id: 1, name: 'employee.view', label: 'Xem' },
			{ id: 2, name: 'employee.create', label: 'Thêm mới' },
			{ id: 3, name: 'employee.update', label: 'Cập nhật' },
			{ id: 4, name: 'employee.delete', label: 'Xóa' },
		],
	},
	{
		key: 'department',
		label: 'Phòng ban',
		permissions: [
			{ id: 5, name: 'department.view', label: 'Xem' },
			{ id: 6, name: 'department.create', label: 'Thêm mới' },
			{ id: 7, name: 'department.update', label: 'Cập nhật' },
			{ id: 8, name: 'department.delete', label: 'Xóa' },
		],
	},
	{
		key: 'position',
		label: 'Chức vụ',
		permissions: [
			{ id: 9, name: 'position.view', label: 'Xem' },
			{ id: 10, name: 'position.create', label: 'Thêm mới' },
			{ id: 11, name: 'position.update', label: 'Cập nhật' },
			{ id: 12, name: 'position.delete', label: 'Xóa' },
		],
	},
	{
		key: 'report',
		label: 'Báo cáo',
		permissions: [
			{ id: 13, name: 'report.view', label: 'Xem' },
			{ id: 14, name: 'report.export', label: 'Xuất file' },
		],
	},
])

// Expand/collapse per group – employee expanded by default
const expandedGroups = ref<Record<string, boolean>>({
	employee: true,
})

const toggleGroup = (key: string) => {
	expandedGroups.value[key] = !expandedGroups.value[key]
}

// Auto-check employee group by default
const defaultEmployeeIds = permissionGroups.value
	.find(g => g.key === 'employee')!
	.permissions.map(p => p.id)

const positionData = reactive<PositionForm>({
	name: '',
	department_id: null,
	description: '',
	permissions: [...defaultEmployeeIds],
})

const errorMessage = reactive<ValidateMessage>({
	name: '',
})

// Per-group checkbox helpers
const isGroupAllChecked = (group: PermissionGroup) =>
	group.permissions.every(p => positionData.permissions.includes(p.id))

const isGroupIndeterminate = (group: PermissionGroup) => {
	const checkedCount = group.permissions.filter(p => positionData.permissions.includes(p.id)).length
	return checkedCount > 0 && checkedCount < group.permissions.length
}

const toggleGroupAll = (group: PermissionGroup, val: boolean | null) => {
	group.permissions.forEach(p => {
		const idx = positionData.permissions.indexOf(p.id)
		if (val && idx === -1) positionData.permissions.push(p.id)
		if (!val && idx !== -1) positionData.permissions.splice(idx, 1)
	})
}

const showDepartmentDialog = ref<boolean>(false)

const onDepartmentSuccess = () => {
	showDepartmentDialog.value = false
	getDepartmentList()
}

const getDepartmentList = async () => {
	try {
		loading.value = true
		await departmentStore.departmentsFetch()
	} catch (error: any) {
		if (error.status === 400 || error.status === 500) {
			getParentErrorMessage.value = error.response?.data?.messageCode
		}
		if (error.status === 400) {
			disabledSelect.value = true
		}
	} finally {
		loading.value = false
	}
}

const handleSubmit = async () => {
	try {
		// TODO: replace with positionStore.positionCreate(positionData)
		toast.show('position.message.createSuccess', 'success')
		emit('success')
	} catch (error: any) {
		if (error.status === 422) {
			mapLaravelError(errorMessage, error)
		}
		toast.show(error.response?.data?.messageCode, 'error')
	}
}

const handleCancel = () => {
	emit('cancel')
}
</script>

<template>
	<Form title="position.title.create" @submit-form="handleSubmit">
		<!-- Row 1: Position Name -->
		<v-row dense>
			<v-col cols="12">
				<Input
					name="name"
					:rules="positionValidation.name"
					v-model="positionData.name"
					maxlength="100"
					counter
					:error-messages="errorMessage.name"
				>
					<template #label>
						{{ $t('position.input.positionName') }}
						<span class="text-error">*</span>
					</template>
				</Input>
			</v-col>
		</v-row>

		<!-- Row 2: Department Select with tooltip inside (append-inner) -->
		<v-row dense>
			<v-col cols="12">
				<list-filter
					:label="getParentErrorMessage.length ? $t(getParentErrorMessage) : $t('position.input.selectDepartment')"
					v-model="positionData.department_id"
					:items="departments"
					searchable
					item-title="name"
					item-value="id"
					:loading
					:disabled="disabledSelect"
					@click="getDepartmentList"
				>
					<template #prepend-item>
						<v-list-item
							@click="showDepartmentDialog = true"
							color="primary"
							class="text-primary"
						>
							<template #prepend>
								<v-icon icon="mdi-plus-circle-outline" />
							</template>
							<v-list-item-title class="font-weight-bold">
								{{ $t('department.title.create') }}
							</v-list-item-title>
						</v-list-item>
						<v-divider class="mb-2" />
					</template>
					<template #append-inner>
						<v-tooltip location="top" :text="$t('position.tooltip.unselectDepartment')">
							<template #activator="{ props }">
								<v-icon
									v-bind="props"
									icon="mdi-information-outline"
									size="18"
									color="grey-darken-1"
								/>
							</template>
						</v-tooltip>
					</template>
				</list-filter>
			</v-col>
		</v-row>

		<!-- Row 3: Description -->
		<v-row dense>
			<v-col cols="12">
				<Textarea
					:label="$t('position.input.positionDesc')"
					name="description"
					v-model="positionData.description"
				></Textarea>
			</v-col>
		</v-row>

		<!-- Row 4: Permission Groups -->
		<v-row dense class="mt-2">
			<v-col cols="12">
				<div class="text-subtitle-2 font-weight-medium mb-2">
					{{ $t('position.input.permissionForPossision') }}
				</div>

				<v-card
					v-for="group in permissionGroups"
					:key="group.key"
					variant="elevated"
					class="mb-3 elevation-1"
				>
					<!-- Group header: select-all + collapse toggle -->
					<div
						class="d-flex align-center px-3 py-1 cursor-pointer"
						@click="toggleGroup(group.key)"
					>
						<!-- Prevent click on header from triggering collapse when clicking checkbox -->
						<v-checkbox
							density="compact"
							hide-details
							:model-value="isGroupAllChecked(group)"
							:indeterminate="isGroupIndeterminate(group)"
							@update:model-value="toggleGroupAll(group, $event)"
							color="primary"
							@click.stop
						>
							<template #label>
								<span class="font-weight-medium">{{ group.label }}</span>
							</template>
						</v-checkbox>

						<v-spacer />

						<v-icon :icon="expandedGroups[group.key] ? 'mdi-chevron-up' : 'mdi-chevron-down'" />
					</div>

					<!-- Collapsible body -->
					<v-expand-transition>
						<div v-show="expandedGroups[group.key]">
							<v-divider />
							<v-card-text class="pa-3">
								<v-row dense>
									<v-col
										v-for="permission in group.permissions"
										:key="permission.id"
										cols="6"
										sm="3"
									>
										<v-checkbox
											density="compact"
											hide-details
											v-model="positionData.permissions"
											:value="permission.id"
											:label="permission.label"
											color="primary"
										/>
									</v-col>
								</v-row>
							</v-card-text>
						</div>
					</v-expand-transition>
				</v-card>
			</v-col>
		</v-row>

		<!-- Actions: Cancel + Create -->
		<v-row dense justify="space-between" class="mt-2">
			<v-col cols="auto">
				<BaseBtn
					title="common.btn.cancel"
					color="red-darken-1"
					@click.prevent="handleCancel"
				/>
			</v-col>
			<v-col cols="auto">
				<BaseBtn
					title="common.btn.create"
					color="primary"
					type="submit"
				/>
			</v-col>
		</v-row>
	</Form>

	<!-- Dialog Create Department -->
	<v-dialog v-model="showDepartmentDialog" :max-width="defaultConfig.maxWidthForm" persistent>
		<v-card class="pa-4 rounded-lg">
			<DepartmentForm @success="onDepartmentSuccess" @cancel="showDepartmentDialog = false" />
		</v-card>
	</v-dialog>
</template>
