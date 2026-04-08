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
import { reactive, ref } from 'vue'
import { useDepartmentStore } from '@/stores/department'
import { storeToRefs } from 'pinia'
import { mapLaravelError } from '@/utils/errorHandler'
import { useToastStore } from '@/stores/toast'
import positionValidation from '@/composables/validation/usePositionValidation'
import defaultConfig from '@/config/default'
import { usePositionStore } from '@/stores/position'
import requiredLabel from './requiredLabel.vue'

interface PositionForm {
	name: string
	department_id: number | null
	description: string
	parent_id: number | null
}

interface ErrorMessage {
	name: string
	department_id: string
	description: string
	parent_id: string
	getDepartmentList: string
	getPositionList: string
}

const emit = defineEmits(['save', 'cancel'])

const toast = useToastStore()

const { departmentsFetch } = useDepartmentStore()
const { departments } = storeToRefs(useDepartmentStore())

const { positionCreate, positionFetch } = usePositionStore()
const { positions } = storeToRefs(usePositionStore())

const loadingDepartment = ref<boolean>(false)
const loadingPosition = ref<boolean>(false)

const positionData = reactive<PositionForm>({
	name: '',
	department_id: null,
	description: '',
	parent_id: null,
})

const errorMessage = reactive<ErrorMessage>({
	name: '',
	department_id: '',
	description: '',
	parent_id: '',
	getDepartmentList: '',
	getPositionList: '',
})

const showDepartmentDialog = ref<boolean>(false)

const getDepartmentList = async () => {
	try {
		loadingDepartment.value = true
		await departmentsFetch()
		errorMessage.getDepartmentList = ''
	} catch (error: any) {
		if (error.status === 422 || error.status === 500) {
			errorMessage.getDepartmentList = error.response?.data?.message
		}
	} finally {
		loadingDepartment.value = false
	}
}

const getPositionList = async () => {
	try {
		loadingPosition.value = true
		await positionFetch()
		errorMessage.getPositionList = ''
	} catch (error: any) {
		if (error.status === 422 || error.status === 500) {
			errorMessage.getPositionList = error.response?.data?.message
		}
	} finally {
		loadingPosition.value = false
	}
}

const handleSubmit = async () => {
	try {
		const response = await positionCreate(positionData)
		toast.show(response.data.message, 'success')
		emit('save')
	} catch (error: any) {
		if (error.status === 422) {
			mapLaravelError(errorMessage, error)
			return
		}
		toast.show(error.response?.data?.message, 'error')
	}
}

const handleCancel = () => {
	emit('cancel')
}
</script>

<template>
	<Form title="position.title.create" @submit-form="handleSubmit">
		<error-alert
			:messages="errorMessage"
			:ignore="['getDepartmentList', 'getPositionList']"
		></error-alert>

		<!-- Row 1: Position Name -->
		<v-row dense>
			<v-col cols="12">
				<Input
					name="name"
					:rules="positionValidation.name"
					v-model="positionData.name"
					:maxlength="defaultConfig.maxLengthName"
					counter
				>
					<template #label>
						<required-label :label="$t('position.input.positionName')"></required-label>
					</template>
				</Input>
			</v-col>
		</v-row>

		<!-- Row 2: Department Select with tooltip inside (append-inner) -->
		<v-row dense>
			<v-col cols="12" sm="6">
				<list-filter
					:label="$t('position.input.selectDepartment')"
					v-model="positionData.department_id"
					:error-messages="errorMessage.getDepartmentList"
					:items="departments"
					searchable
					item-title="name"
					item-value="id"
					:loading="loadingDepartment"
					@click="getDepartmentList"
				>
					<template #prepend-item>
						<create-prepend-item
							title="department.title.create"
							@open-model="showDepartmentDialog = true"
						>
						</create-prepend-item>
						<v-divider />
					</template>
					<template #append-inner>
						<annotation-tooltip text="position.tooltip.unselectDepartment">
						</annotation-tooltip>
					</template>
				</list-filter>
			</v-col>

			<v-col cols="12" sm="6">
				<list-filter
					:label="$t('position.input.supervisor')"
					v-model="positionData.parent_id"
					:error-messages="errorMessage.getPositionList"
					:items="positions"
					searchable
					item-title="name"
					item-value="id"
					:loading="loadingPosition"
					@click="getPositionList"
				>
					<template #append-inner>
						<annotation-tooltip text="position.tooltip.unselectSupervisor">
						</annotation-tooltip>
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
				<BaseBtn title="common.btn.create" color="primary" type="submit" />
			</v-col>
		</v-row>
	</Form>

	<!-- Dialog Create Department -->
	<v-dialog v-model="showDepartmentDialog" :max-width="defaultConfig.maxWidthForm" persistent>
		<v-card class="pa-4 rounded-lg">
			<DepartmentForm
				@save="showDepartmentDialog = false"
				@cancel="showDepartmentDialog = false"
			/>
		</v-card>
	</v-dialog>
</template>
