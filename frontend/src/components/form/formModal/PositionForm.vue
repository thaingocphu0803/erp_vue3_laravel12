<script setup lang="ts">
import Form from '@/components/Form.vue'
import Input from '@/components/form/Input.vue'
import BaseBtn from '@/components/BaseBtn.vue'
import Textarea from '@/components/form/Textarea.vue'
import ListFilter from '@/components/list/ListFilter.vue'
import DepartmentForm from './DepartmentForm.vue'
import CreatePrependItem from '../AddItemListBtn.vue'
import AnnotationTooltip from '../AnnotationTooltip.vue'
import ErrorAlert from '../ErrorAlert.vue'
import { computed, onMounted, reactive, ref, watch } from 'vue'
import { useDepartmentStore } from '@/stores/department'
import { storeToRefs } from 'pinia'
import { mapLaravelError } from '@/utils/errorHandler'
import { useToastStore } from '@/stores/toast'
import positionValidation from '@/composables/validation/usePositionValidation'
import defaultConfig from '@/config/default'
import { usePositionStore } from '@/stores/position'
import type { Permission } from '@/stores/position'
import CheckBox from '../CheckBox.vue'

interface PositionForm {
	name: string
	department_id: number | null
	description: string
	permissions: number[]
}

interface ValidateMessage {
	name: string,
	permissions: string,
	department_id: string
}

onMounted(async () => {
	try {
		loadingPermission.value = true
		await permissionFetch()
	} catch (error: any) {
		console.log(error)
	} finally {
		loadingPermission.value = false
	}
})



const emit = defineEmits(['save', 'cancel'])


const { permissionGroup } = storeToRefs(usePositionStore())
const { permissionFetch, positionCreate } = usePositionStore()


const { departmentsFetch } = useDepartmentStore()
const toast = useToastStore()
const { departments } = storeToRefs(useDepartmentStore())

const loadingPermission = ref<boolean>(false)

const loadingDepartment = ref<boolean>(false)

const disabledSelect = ref<boolean>(false)

const getDepartmentErrorMessage = ref<string>('')

const positionData = reactive<PositionForm>({
	name: '',
	department_id: null,
	description: '',
	permissions: [],
})

const errorMessage = reactive<ValidateMessage>({
	name: '',
	permissions: '',
	department_id: ''
})

const showDepartmentDialog = ref<boolean>(false)

const permissionSet = computed(() => new Set(positionData.permissions))

watch(() => positionData.permissions.length, (newLength) => {
	if (newLength === 0) {
		errorMessage.permissions = 'position.validate.permissions.atLeastOne'
	} else {
		errorMessage.permissions = ''
	}
}, { immediate: true })

const onDepartmentSuccess = () => {
	showDepartmentDialog.value = false
}

const isAllSelected = (permissions: Permission[]) => {
	return permissions.every(item => permissionSet.value.has(item.id))
}

const toggleGroup = (permissions: Permission[]) => {
	const groupIds = permissions.map(i => i.id)
	const groupSet = new Set(groupIds)

	if (isAllSelected(permissions)) {
		positionData.permissions = positionData.permissions.filter(id => !groupSet.has(id))
	} else {
		const newIds = groupIds.filter(id => !permissionSet.value.has(id))
		positionData.permissions.push(...newIds)
	}
}

const isIndeterminate = (permissions: Permission[]) => {
	const count = permissions.reduce((acc, permission) => {
		return permissionSet.value.has(permission.id) ? acc + 1 : acc
	}, 0)

	return count > 0 && count < permissions.length
}

const getDepartmentList = async () => {
	try {
		loadingDepartment.value = true
		await departmentsFetch()
	} catch (error: any) {
		if (error.status === 400 || error.status === 500) {
			getDepartmentErrorMessage.value = error.response?.data?.messageCode
		}
		if (error.status === 400) {
			disabledSelect.value = true
		}
	} finally {
		loadingDepartment.value = false
	}
}

const handleSubmit = async () => {
	try {
		const response = await positionCreate(positionData)
		toast.show(response.data.messageCode, 'success')
		emit('save')
	} catch (error: any) {
		if (error.status === 422) {
			mapLaravelError(errorMessage, error)
			return
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

		<error-alert :messages="errorMessage"></error-alert>

		<!-- Row 1: Position Name -->
		<v-row dense>
			<v-col cols="12">
				<Input name="name" :rules="positionValidation.name" v-model="positionData.name"
					:maxlength="defaultConfig.maxLengthName" counter>
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
				<list-filter :label="getDepartmentErrorMessage.length
					? $t(getDepartmentErrorMessage)
					: $t('position.input.selectDepartment')
					" v-model="positionData.department_id" :items="departments" searchable item-title="name" item-value="id"
					:loading="loadingDepartment" :disabled="disabledSelect" @click="getDepartmentList">
					<template #prepend-item>
						<create-prepend-item title="department.title.create" @open-model="showDepartmentDialog = true">
						</create-prepend-item>
						<v-divider />
					</template>
					<template #append-inner>
						<annotation-tooltip text="position.tooltip.unselectDepartment">
						</annotation-tooltip>
					</template>
				</list-filter>
			</v-col>
		</v-row>

		<!-- Row 3: Description -->
		<v-row dense>
			<v-col cols="12">
				<Textarea :label="$t('position.input.positionDesc')" name="description"
					v-model="positionData.description"></Textarea>
			</v-col>
		</v-row>

		<!-- Row 4: Permission Groups -->
		<v-row dense class="mt-2">

			<v-col cols="12" class="text-center">
				<div class="mb-3 text-h5">{{ $t('position.input.assignPermission') }}</div>
				<v-progress-circular indeterminate v-if="loadingPermission"></v-progress-circular>
			</v-col>

			<v-col cols="12">
				<v-expansion-panels v-for="(permissions, module) in permissionGroup" :key="module">
					<v-expansion-panel class="mb-3">
						<v-expansion-panel-title>
							<check-box color="primary" :label="$t(module)" class="font-weight-semibold"
								:model-value="isAllSelected(permissions)" :indeterminate="isIndeterminate(permissions)"
								@click.stop="toggleGroup(permissions)">
							</check-box>
						</v-expansion-panel-title>

						<v-divider></v-divider>

						<v-expansion-panel-text>
							<v-row dense>
								<v-col v-for="permission in permissions" :key="permission.id" cols="6" sm="3">
									<check-box v-model="positionData.permissions" :value="permission.id"
										:label="$t(permission.name)" color="primary" />
								</v-col>
							</v-row>
						</v-expansion-panel-text>
					</v-expansion-panel>
				</v-expansion-panels>
			</v-col>
		</v-row>

		<!-- Actions: Cancel + Create -->
		<v-row dense justify="space-between" class="mt-2">
			<v-col cols="auto">
				<BaseBtn title="common.btn.cancel" color="red-darken-1" @click.prevent="handleCancel" />
			</v-col>
			<v-col cols="auto">
				<BaseBtn title="common.btn.create" color="primary" type="submit" />
			</v-col>
		</v-row>
	</Form>

	<!-- Dialog Create Department -->
	<v-dialog v-model="showDepartmentDialog" :max-width="defaultConfig.maxWidthForm" persistent>
		<v-card class="pa-4 rounded-lg">
			<DepartmentForm @save="onDepartmentSuccess" @cancel="showDepartmentDialog = false" />
		</v-card>
	</v-dialog>
</template>
