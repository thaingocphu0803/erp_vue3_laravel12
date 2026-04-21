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
import { onMounted, reactive, ref, watch } from 'vue'
import { useDepartmentStore } from '@/stores/department'
import { storeToRefs } from 'pinia'
import { mapLaravelError } from '@/utils/errorHandler'
import { useToastStore } from '@/stores/toast'
import positionValidation from '@/composables/validation/usePositionValidation'
import defaultConfig from '@/config/default'
import { usePositionStore } from '@/stores/position'
import { useThrottleStore } from '@/stores/throttle'
import requiredLabel from './requiredLabel.vue'
import RetryBtn from '@/components/RetryBtn.vue'
import ThrottleAlert from '@/components/ThrottleAlert.vue'

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

const { throttle, isDisabled } = storeToRefs(useThrottleStore())
const { initThrottle, startThrottle } = useThrottleStore()

const loadingDepartment = ref<boolean>(false)
const loadingPosition = ref<boolean>(false)

const isPositionError = ref<boolean>(false)
const isDepartmentError = ref<boolean>(false)

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
	if (isDisabled.value('position-departmentList')) return

	try {
		loadingDepartment.value = true
		await departmentsFetch()
		errorMessage.getDepartmentList = ''
		isDepartmentError.value = false

	} catch (error: any) {
		isDepartmentError.value = true

		if (error.status === 500) {
			errorMessage.getDepartmentList = 'common.error.fetchDataFailed'
		}

		if (error.status === 429) {
			throttle.value['position-departmentList'] = error.response.headers['retry-after'] as number
			startThrottle('position-departmentList')
		}
	} finally {
		loadingDepartment.value = false
	}
}

const getPositionList = async () => {
	if (isDisabled.value('position-positionList')) return

	try {
		loadingPosition.value = true
		await positionFetch()
		errorMessage.getPositionList = ''
		isPositionError.value = false
	} catch (error: any) {
		isPositionError.value = true

		if (error.status === 500) {
			errorMessage.getPositionList = 'common.error.fetchDataFailed'
		}

		if (error.status === 429) {
			throttle.value['position-positionList'] = error.response.headers['retry-after'] as number
			startThrottle('position-positionList')
		}
	} finally {
		loadingPosition.value = false
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

onMounted(() => {
	initThrottle('position-departmentList')
	initThrottle('position-positionList')
})

watch(() => isDisabled.value('position-departmentList'), (value) => {
	if (value) {
		errorMessage.getDepartmentList = ''
	}
})

watch(() => isDisabled.value('position-positionList'), (value) => {
	if (value) {
		errorMessage.getPositionList = ''
	}
})
</script>

<template>
	<Form title="position.title.create" @submit-form="handleSubmit">
		<error-alert :messages="errorMessage" :ignore="['getDepartmentList', 'getPositionList']"></error-alert>

		<!-- Row 1: Position Name -->
		<v-row dense>
			<v-col cols="12">
				<Input name="name" :rules="positionValidation.name" v-model="positionData.name"
					:maxlength="defaultConfig.maxLengthName" counter>
					<template #label>
						<required-label :label="$t('position.input.positionName')"></required-label>
					</template>
				</Input>
			</v-col>
		</v-row>

		<!-- Row 2: Department Select with tooltip inside (append-inner) -->
		<v-row dense>
			<v-col cols="12" sm="6">
				<list-filter :label="$t('position.input.selectDepartment')" v-model="positionData.department_id"
					:error-messages="errorMessage.getDepartmentList" :items="departments" searchable item-title="name"
					item-value="id" :loading="loadingDepartment" @click="getDepartmentList">
					<template #prepend-item>
						<create-prepend-item title="department.title.create" @open-model="showDepartmentDialog = true">
						</create-prepend-item>
						<v-divider />
					</template>
					<template #append-inner>
						<annotation-tooltip text="position.tooltip.unselectDepartment">
						</annotation-tooltip>
					</template>
					<template #append v-if="isDepartmentError">
						<retry-btn @click.stop="getDepartmentList" only-icon
							:disabled="isDisabled('position-departmentList')"></retry-btn>
					</template>
				</list-filter>
				<throttle-alert :show="isDisabled('position-departmentList')"
					:time="throttle['position-departmentList'] || 0"></throttle-alert>

			</v-col>

			<v-col cols="12" sm="6">
				<list-filter :label="$t('position.input.supervisor')" v-model="positionData.parent_id"
					:error-messages="errorMessage.getPositionList" :items="positions" searchable item-title="name"
					item-value="id" :loading="loadingPosition" @click="getPositionList">
					<template #append-inner>
						<annotation-tooltip text="position.tooltip.unselectSupervisor">
						</annotation-tooltip>
					</template>
					<template #append v-if="isPositionError">
						<retry-btn @click.stop="getPositionList" only-icon
							:disabled="isDisabled('position-positionList')"></retry-btn>
					</template>
				</list-filter>
				<throttle-alert :show="isDisabled('position-positionList')"
					:time="throttle['position-positionList'] || 0"></throttle-alert>

			</v-col>
		</v-row>

		<!-- Row 3: Description -->
		<v-row dense>
			<v-col cols="12">
				<Textarea :label="$t('position.input.positionDesc')" name="description"
					v-model="positionData.description"></Textarea>
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
			<DepartmentForm @save="showDepartmentDialog = false" @cancel="showDepartmentDialog = false" />
		</v-card>
	</v-dialog>
</template>
