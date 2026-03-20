<script setup lang="ts">
import Form from '@/components/Form.vue'
import Input from '@/components/form/Input.vue'
import BaseBtn from '@/components/BaseBtn.vue'
import AnnotationTooltip from '../AnnotationTooltip.vue'
import ErrorAlert from '../ErrorAlert.vue'
import departmentValidation from '@/composables/validation/useDepartmentValidation'
import {reactive, ref } from 'vue'
import ListFilter from '@/components/list/ListFilter.vue'
import Textarea from '@/components/form/Textarea.vue'
import { useDepartmentStore } from '@/stores/department'
import { storeToRefs } from 'pinia'
import { mapLaravelError } from '@/utils/errorHandler'
import { useToastStore } from '@/stores/toast'
import defaultConfig from '@/config/default'


interface DepartmentForm {
	name: string
	code: string | null
	parent_id: number | null
	description: string
}

interface ValidateMessage {
	name: string,
	code: string,
	parent_id: string
}

const title = 'department.title.create'

const emit = defineEmits(['success', 'cancel'])

const departmentStore = useDepartmentStore()

const toast = useToastStore();

const { departments } = storeToRefs(departmentStore)

const loading = ref<boolean>(false)

const disabledSelect = ref<boolean>(false)

const departmentData = reactive<DepartmentForm>({
	name: '',
	code: '',
	parent_id: null,
	description: '',
})

const errorMessage = reactive<ValidateMessage>({
	name: '',
	code: '',
	parent_id: ''
})

const getParentErrorMessage = ref<string>('')

const getDepartmentList = async () => {
	try {
		loading.value = true
		await departmentStore.departmentsFetch();
	} catch (error:any) {
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

const handleCreate = async () => {
	try {
		const response = await departmentStore.departmentCreate(departmentData)

		toast.show(response.data.messageCode, 'success')

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

		<Form :title @submit-form="handleCreate">
			<error-alert :messages="errorMessage" :ignore="['name', 'code']"></error-alert>

			<v-row dense>
				<v-col cols="12">
					<Input
						name="name"
						:rules="departmentValidation.name"
						v-model="departmentData.name"
						:maxlength="defaultConfig.maxLengthName"
						counter
						:error-messages="errorMessage.name"
					>
					<template #label>
						{{ $t('department.input.departmentName') }}
						<span class="text-error">*</span>
					</template>
					</Input>
				</v-col>
			</v-row>

			<v-row dense>
				<v-col cols="12" md="6">
					<Input
						:label="$t('department.input.departmentCode')"
						name="code"
						v-model="departmentData.code"
						:maxlength="defaultConfig.maxLengthCode"
						counter
						:error-messages="errorMessage.code"
					>
						<template #append-inner>
							<annotation-tooltip text="department.tooltip.codeAutoGenerate"></annotation-tooltip>
						</template>
					</Input>
				</v-col>

				<v-col cols="12" md="6">
					<list-filter
						:label="getParentErrorMessage.length ? $t(getParentErrorMessage) : $t('department.input.departmentParent')"
						v-model="departmentData.parent_id"
						:items="departments"
						searchable
						item-title="name"
						item-value="id"
						:loading
						:disabled="disabledSelect"
						@click="getDepartmentList"
					/>
				</v-col>
			</v-row>

			<v-row dense>
				<v-col cols="12">
					<Textarea
						:label="$t('department.input.departmentDesc')"
						name="description"
						v-model="departmentData.description"
					></Textarea>
				</v-col>
			</v-row>

			<!-- Actions: Cancel (red) + Create (blue) -->
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
</template>
