<script setup lang="ts">
import Form from '@/components/Form.vue'
import Input from '@/components/form/Input.vue'
import BaseBtn from '@/components/BaseBtn.vue'
import AnnotationTooltip from '../AnnotationTooltip.vue'
import ErrorAlert from '../ErrorAlert.vue'
import useDepartmentValidation from '@/composables/validation/useDepartmentValidation'
import { onMounted, reactive, ref } from 'vue'
import ListFilter from '@/components/list/ListFilter.vue'
import Textarea from '@/components/form/Textarea.vue'
import { useDepartmentStore } from '@/stores/department'
import { storeToRefs } from 'pinia'
import { mapLaravelError } from '@/utils/errorHandler'
import { useToastStore } from '@/stores/toast'
import CONFIG from '@/config/constants'
import RequiredLabel from './RequiredLabel.vue'
import RetryBtn from '@/components/RetryBtn.vue'
import { useThrottleStore } from '@/stores/throttle'
import ThrottleAlert from '@/components/ThrottleAlert.vue'
import SYSTEM from '@/config/system'

import type { DepartmentFormData, DepartmentFormError } from '@/types/department'

// title
const title = 'department.title.create'

// emits
const emit = defineEmits(['save', 'cancel'])

// department stores
const { departmentsFetch, departmentCreate } = useDepartmentStore()

// toast
const toast = useToastStore()

// validation rules
const { departmentValidation } = useDepartmentValidation()

// department data
const { departments } = storeToRefs(useDepartmentStore())

// throttle
const { throttle, isDisabled } = storeToRefs(useThrottleStore())
const { initThrottle, startThrottle } = useThrottleStore()

// loading state
const loading = ref<boolean>(false)

// error state
const isError = ref<boolean>(false)

// department form data
const departmentFormData = reactive<DepartmentFormData>({
	name: '',
	code: '',
	parent_id: null,
	description: '',
})

// error messages
const errorMessage = reactive<DepartmentFormError>({
	name: '',
	code: '',
	parent_id: '',
	description: '',
	getDepartmentList: '',
})

// get department list
const getDepartmentList = async () => {
	if (isDisabled.value('departmentFetch')) return

	try {
		loading.value = true
		await departmentsFetch()
		errorMessage.getDepartmentList = ''
		isError.value = false
	} catch (error: any) {
		errorMessage.getDepartmentList = 'common.error.fetchDataFailed'
		isError.value = true

		if (error.status === SYSTEM.SERVER_ERROR.TOO_MANY_REQUESTS) {
			throttle.value['departmentFetch'] = Number(error.response.headers['retry-after'])
			startThrottle('departmentFetch')
		}
	} finally {
		loading.value = false
	}
}

// handle create department
const handleCreate = async () => {
	try {
		const response = await departmentCreate(departmentFormData)

		toast.show(response.data.messageCode, 'success')

		emit('save')
	} catch (error: any) {
		if (error.status === SYSTEM.SERVER_ERROR.UNPROCESSABLE_ENTITY) {
			mapLaravelError(errorMessage, error)
			return
		}
		toast.show(error.response?.data?.messageCode, 'error')
	}
}

// handle cancel
const handleCancel = () => {
	emit('cancel')
}

onMounted(() => {
	initThrottle('departmentFetch')

	if (isDisabled.value('departmentFetch')) {
		isError.value = true
	}
})
</script>

<template>
	<Form :title @submit-form="handleCreate">
		<!-- Error Alert -->
		<error-alert :messages="errorMessage" :ignore="['getDepartmentList']"></error-alert>

		<!-- Department Name -->
		<v-row dense>
			<v-col cols="12">
				<Input
					name="name"
					:rules="departmentValidation.name"
					v-model="departmentFormData.name"
					:maxlength="CONFIG.maxLengthName"
					counter
				>
					<template #label>
						<required-label
							:label="$t('department.input.departmentName')"
						></required-label>
					</template>
				</Input>
			</v-col>
		</v-row>

		<!-- Department Code & Parent -->
		<v-row dense>
			<v-col cols="12" md="6">
				<Input
					:label="$t('department.input.departmentCode')"
					name="code"
					v-model="departmentFormData.code"
					:maxlength="CONFIG.maxLengthCode"
					counter
				>
					<template #append-inner>
						<annotation-tooltip
							text="department.tooltip.codeAutoGenerate"
						></annotation-tooltip>
					</template>
				</Input>
			</v-col>

			<!-- Department Parent -->
			<v-col cols="12" md="6">
				<list-filter
					:label="$t('department.input.departmentParent')"
					v-model="departmentFormData.parent_id"
					:error-messages="
						isDisabled('departmentFetch') ? '' : errorMessage.getDepartmentList
					"
					:items="departments"
					searchable
					item-title="name"
					item-value="id"
					:loading
					@click="getDepartmentList"
					list-filter
				>
					<template #append v-if="isError">
						<retry-btn
							@click.stop="getDepartmentList"
							only-icon
							:disabled="isDisabled('departmentFetch')"
						></retry-btn>
					</template>
				</list-filter>

				<throttle-alert
					:show="isDisabled('departmentFetch')"
					:time="throttle['departmentFetch'] || 0"
				></throttle-alert>
			</v-col>
		</v-row>

		<!-- Department Description -->
		<v-row dense>
			<v-col cols="12">
				<Textarea
					:label="$t('department.input.departmentDesc')"
					name="description"
					v-model="departmentFormData.description"
				></Textarea>
			</v-col>
		</v-row>

		<!-- Actions -->
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
</template>

