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
import { onMounted, reactive, ref } from 'vue'
import { useDepartmentStore } from '@/stores/department'
import { storeToRefs } from 'pinia'
import { mapLaravelError, formatLaravelRetryAfter } from '@/utils/errorHandler'
import { useToastStore } from '@/stores/toast'
import usePositionValidation from '@/composables/validation/usePositionValidation'
import CONFIG from '@/config/constants'
import { usePositionStore } from '@/stores/position'
import { useThrottleStore } from '@/stores/throttle'
import RequiredLabel from './RequiredLabel.vue'
import RetryBtn from '@/components/RetryBtn.vue'
import ThrottleAlert from '@/components/ThrottleAlert.vue'
import SYSTEM from '@/config/system'

import type { PositionFormData, PositionFormError } from '@/types/position'

// title
const title = 'position.title.create'

// emits
const emit = defineEmits(['save', 'cancel'])

// department stores
const { departmentsFetch } = useDepartmentStore()
const { departments } = storeToRefs(useDepartmentStore())

// position stores
const { positionCreate, positionFetch } = usePositionStore()
const { positions } = storeToRefs(usePositionStore())

// validation rules
const { positionValidation } = usePositionValidation()

// throttle
const { throttle, isDisabled } = storeToRefs(useThrottleStore())
const { initThrottle, startThrottle } = useThrottleStore()

// toast
const toast = useToastStore()

// loading state
const loadingDepartment = ref<boolean>(false)
const loadingPosition = ref<boolean>(false)

// error state
const isPositionError = ref<boolean>(false)
const isDepartmentError = ref<boolean>(false)

// position form data
const positionFormData = reactive<PositionFormData>({
	name: '',
	department_id: null,
	description: '',
	parent_id: null,
})

// error message
const errorMessage = reactive<PositionFormError>({
	name: '',
	department_id: '',
	description: '',
	parent_id: '',
	getDepartmentList: '',
	getPositionList: '',
})

// dialog state
const showDepartmentDialog = ref<boolean>(false)

// handle getDepartmentList
const getDepartmentList = async () => {
	if (isDisabled.value('departmentFetch')) return

	try {
		loadingDepartment.value = true
		await departmentsFetch()
		errorMessage.getDepartmentList = ''
		isDepartmentError.value = false
	} catch (error: any) {
		errorMessage.getDepartmentList = 'common.error.fetchDataFailed'
		isDepartmentError.value = true

		if (error.status === SYSTEM.SERVER_ERROR.TOO_MANY_REQUESTS) {
			throttle.value['departmentFetch'] = formatLaravelRetryAfter(error)
			startThrottle('departmentFetch')
		}
	} finally {
		loadingDepartment.value = false
	}
}

// handle getPositionList
const getPositionList = async () => {
	if (isDisabled.value('positionFetch')) return

	try {
		loadingPosition.value = true
		await positionFetch()
		errorMessage.getPositionList = ''
		isPositionError.value = false
	} catch (error: any) {
		errorMessage.getPositionList = 'common.error.fetchDataFailed'
		isPositionError.value = true

		if (error.status === SYSTEM.SERVER_ERROR.TOO_MANY_REQUESTS) {
			throttle.value['positionFetch'] = Number(error.response.headers['retry-after'])
			startThrottle('positionFetch')
		}
	} finally {
		loadingPosition.value = false
	}
}

// handle handleSubmit
const handleSubmit = async () => {
	try {
		const response = await positionCreate(positionFormData)
		toast.show(response.data.messageCode, 'success')
		emit('save')
	} catch (error: any) {
		if (error.status === SYSTEM.SERVER_ERROR.UNPROCESSABLE_ENTITY) {
			mapLaravelError(errorMessage, error)
			return
		}

		if (error.status === SYSTEM.SERVER_ERROR.TOO_MANY_REQUESTS) {
			throttle.value['positionCreate'] = formatLaravelRetryAfter(error)
			startThrottle('positionCreate')
		}

		if (error.status === SYSTEM.SERVER_ERROR.INTERNAL_SERVER_ERROR) {
			const messageCode = 'position.alert.error.create'
			toast.show(messageCode, 'error')
		}
	}
}

// handle cancel
const handleCancel = () => {
	emit('cancel')
}

onMounted(() => {
	initThrottle('departmentFetch')
	initThrottle('positionFetch')
	initThrottle('positionCreate')

	if (isDisabled.value('departmentFetch')) {
		isDepartmentError.value = true
	}

	if (isDisabled.value('positionFetch')) {
		isPositionError.value = true
	}
})
</script>

<template>
	<Form :title @submit-form="handleSubmit">
		<!-- Error Message -->
		<error-alert :messages="errorMessage" :ignore="['getDepartmentList', 'getPositionList']"></error-alert>

		<!-- Position Name -->
		<v-row dense>
			<v-col cols="12">
				<Input name="name" :rules="positionValidation.name" v-model="positionFormData.name"
					:maxlength="CONFIG.maxLengthName" counter>
					<template #label>
						<required-label :label="$t('position.input.positionName')"></required-label>
					</template>
				</Input>
			</v-col>
		</v-row>

		<!-- Department Select -->
		<v-row dense>
			<v-col cols="12" md="6">
				<list-filter :label="$t('position.input.selectDepartment')" v-model="positionFormData.department_id"
					:error-messages="isDisabled('departmentFetch') ? '' : errorMessage.getDepartmentList
						" :items="departments" searchable item-title="name" item-value="id" :loading="loadingDepartment"
					@click="getDepartmentList">
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
							:disabled="isDisabled('departmentFetch')"></retry-btn>
					</template>
				</list-filter>

				<throttle-alert :show="isDisabled('departmentFetch')"
					:time="throttle['departmentFetch'] || 0"></throttle-alert>
			</v-col>

			<!-- Supervisor Select -->
			<v-col cols="12" md="6">
				<list-filter :label="$t('position.input.supervisor')" v-model="positionFormData.parent_id"
					:error-messages="isDisabled('positionFetch') ? '' : errorMessage.getPositionList
						" :items="positions" searchable item-title="name" item-value="id" :loading="loadingPosition"
					@click="getPositionList">
					<template #append-inner>
						<annotation-tooltip text="position.tooltip.unselectSupervisor">
						</annotation-tooltip>
					</template>
					<template #append v-if="isPositionError">
						<retry-btn @click.stop="getPositionList" only-icon
							:disabled="isDisabled('positionFetch')"></retry-btn>
					</template>
				</list-filter>

				<throttle-alert :show="isDisabled('positionFetch')"
					:time="throttle['positionFetch'] || 0"></throttle-alert>
			</v-col>
		</v-row>

		<!-- Description -->
		<v-row dense>
			<v-col cols="12">
				<Textarea :label="$t('position.input.positionDesc')" name="description"
					v-model="positionFormData.description"></Textarea>
			</v-col>
		</v-row>

		<!-- Throttle Alert -->
		<v-row dense justify="center">
			<throttle-alert :time="throttle['positionCreate'] || 0" :show="isDisabled('positionCreate')" />
		</v-row>

		<!-- Actions -->
		<v-row dense justify="space-between" class="mt-2">
			<v-col cols="auto">
				<BaseBtn title="common.btn.cancel" color="red-darken-1" @click.prevent="handleCancel" />
			</v-col>
			<v-col cols="auto">
				<BaseBtn title="common.btn.create" color="primary" type="submit"
					:disabled="isDisabled('positionCreate')" />
			</v-col>
		</v-row>
	</Form>

	<!-- Dialog Create Department -->
	<v-dialog v-model="showDepartmentDialog" :max-width="CONFIG.maxWidthForm" persistent>
		<v-card class="pa-4 rounded-lg">
			<DepartmentForm @save="showDepartmentDialog = false" @cancel="showDepartmentDialog = false" />
		</v-card>
	</v-dialog>
</template>
