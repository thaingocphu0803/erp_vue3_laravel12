<script setup lang="ts">
import Form from '@/components/Form.vue'
import Input from '@/components/form/Input.vue'
import BaseBtn from '@/components/BaseBtn.vue'
import AnnotationTooltip from '../AnnotationTooltip.vue'
import ErrorAlert from '../ErrorAlert.vue'
import departmentValidation from '@/composables/validation/useDepartmentValidation'
import { onMounted, reactive, ref, computed } from 'vue'
import ListFilter from '@/components/list/ListFilter.vue'
import Textarea from '@/components/form/Textarea.vue'
import { useDepartmentStore } from '@/stores/department'
import { storeToRefs } from 'pinia'
import { mapLaravelError } from '@/utils/errorHandler'
import { useToastStore } from '@/stores/toast'
import defaultConfig from '@/config/default'
import RequiredLabel from './requiredLabel.vue'
import RetryBtn from '@/components/RetryBtn.vue'
import { useThrottleStore } from '@/stores/throttle'
import ThrottleAlert from '@/components/ThrottleAlert.vue'
import SYSTEM from '@/config/system'

interface DepartmentForm {
	name: string
	code: string | null
	parent_id: number | null
	description: string
}

interface ErrorMessage {
	name: string
	code: string
	parent_id: string
	description: string
	getDepartmentList: string
}

const title = 'department.title.create'

const emit = defineEmits(['save', 'cancel'])

const { departmentsFetch, departmentCreate } = useDepartmentStore()

const toast = useToastStore()

const { departments } = storeToRefs(useDepartmentStore())

const { isDisabled, throttle } = storeToRefs(useThrottleStore())

const { initThrottle, startThrottle } = useThrottleStore()

const loading = ref<boolean>(false)

const isError = ref<boolean>(false)

const departmentData = reactive<DepartmentForm>({
	name: '',
	code: '',
	parent_id: null,
	description: '',
})

const errorMessage = reactive<ErrorMessage>({
	name: '',
	code: '',
	parent_id: '',
	description: '',
	getDepartmentList: '',
})

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

const handleCreate = async () => {
	try {
		const response = await departmentCreate(departmentData)

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
		<error-alert :messages="errorMessage" :ignore="['getDepartmentList']"></error-alert>

		<v-row dense>
			<v-col cols="12">
				<Input name="name" :rules="departmentValidation.name" v-model="departmentData.name"
					:maxlength="defaultConfig.maxLengthName" counter>
					<template #label>
						<required-label :label="$t('department.input.departmentName')"></required-label>
					</template>
				</Input>
			</v-col>
		</v-row>

		<v-row dense>
			<v-col cols="12" md="6">
				<Input :label="$t('department.input.departmentCode')" name="code" v-model="departmentData.code"
					:maxlength="defaultConfig.maxLengthCode" counter>
					<template #append-inner>
						<annotation-tooltip text="department.tooltip.codeAutoGenerate"></annotation-tooltip>
					</template>
				</Input>
			</v-col>

			<v-col cols="12" md="6">
				<list-filter :label="$t('department.input.departmentParent')" v-model="departmentData.parent_id"
					:error-messages="isDisabled('departmentFetch') ? '' : errorMessage.getDepartmentList
						" :items="departments" searchable item-title="name" item-value="id" :loading @click="getDepartmentList"
					list-filter>
					<template #append v-if="isError">
						<retry-btn @click.stop="getDepartmentList" only-icon
							:disabled="isDisabled('departmentFetch')"></retry-btn>
					</template>
				</list-filter>

				<throttle-alert :show="isDisabled('departmentFetch')"
					:time="throttle['departmentFetch'] || 0"></throttle-alert>
			</v-col>
		</v-row>

		<v-row dense>
			<v-col cols="12">
				<Textarea :label="$t('department.input.departmentDesc')" name="description"
					v-model="departmentData.description"></Textarea>
			</v-col>
		</v-row>

		<!-- Actions: Cancel (red) + Create (blue) -->
		<v-row dense justify="space-between" class="mt-2">
			<v-col cols="auto">
				<BaseBtn title="common.btn.cancel" color="red-darken-1" @click.prevent="handleCancel" />
			</v-col>
			<v-col cols="auto">
				<BaseBtn title="common.btn.create" color="primary" type="submit" />
			</v-col>
		</v-row>
	</Form>
</template>
