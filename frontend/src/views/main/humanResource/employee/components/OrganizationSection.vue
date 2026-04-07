<script lang="ts" setup>
import ListFilter from '@/components/list/ListFilter.vue'
import requiredLabel from '@/components/form/formModal/requiredLabel.vue'
import employeeValidation from '@/composables/validation/useEmployeeValidation'
import { useDepartmentStore } from '@/stores/department'
import { usePositionStore } from '@/stores/position'
import { storeToRefs } from 'pinia'
import { ref, watch } from 'vue'
import Checkbox from '@/components/form/CheckBox.vue'
import defaultConfig from '@/config/default'
import DepartmentForm from '@/components/form/formModal/DepartmentForm.vue'
import PositionForm from '@/components/form/formModal/PositionForm.vue'
import CreatePrependItem from '@/components/form/AddItemListBtn.vue'

interface ErrorMessage {
	getDepartmentList: string
	getPositionList: string
}

const department_id = defineModel<number | null>('department_id', {
	default: null,
})

const position_id = defineModel<number | null>('position_id', {
	default: null,
})

const is_leader = defineModel<boolean>('is_leader', {
	default: false,
})
const disablePosition = ref<boolean>(true)

const { departmentsFetch } = useDepartmentStore()
const { positionsFetchByDepartmentId, positionReset } = usePositionStore()

const { departments } = storeToRefs(useDepartmentStore())
const { positionByDepartment } = storeToRefs(usePositionStore())

const loadingDepartments = ref<boolean>(false)
const loadingPositions = ref<boolean>(false)

const showPositionDialog = ref<boolean>(false)
const showDepartmentDialog = ref<boolean>(false)

const errorMessage = ref<ErrorMessage>({
	getDepartmentList: '',
	getPositionList: '',
})

const getDepartmentList = async () => {
	try {
		loadingDepartments.value = true
		await departmentsFetch()
		errorMessage.value.getDepartmentList = ''
	} catch (error: any) {
		if (error.status === 400 || error.status === 500) {
			errorMessage.value.getDepartmentList = error.response?.data?.messageCode
		}
	} finally {
		loadingDepartments.value = false
	}
}

const getPositionList = async (departmentId: number | null) => {
	if (!departmentId) {
		disablePosition.value = true
		return
	}

	try {
		loadingPositions.value = true
		await positionsFetchByDepartmentId(departmentId)
		errorMessage.value.getPositionList = ''
	} catch (error: any) {
		console.log(error)
		if (error.status === 500) {
			errorMessage.value.getPositionList = error.response?.data?.messageCode
		}

		if (error.status === 422) {
			errorMessage.value.getPositionList = error.response?.data?.message
		}
	} finally {
		loadingPositions.value = false
	}
}

watch(department_id, (newValue) => {
	if (!newValue) return

	position_id.value = null
	disablePosition.value = false
	positionReset()
	getPositionList(newValue)
})
</script>

<template>
	<v-row dense>
		<v-col cols="12" sm="6" class="mb-3">
			<list-filter
				:hide-details="false"
				v-model="department_id"
				:items="departments"
				searchable
				item-title="name"
				item-value="id"
				:loading="loadingDepartments"
				:rules="employeeValidation.department"
				:error-messages="errorMessage.getDepartmentList"
				@click="getDepartmentList"
				:clearable="false"
			>
				<template #prepend-item>
					<create-prepend-item
						title="department.title.create"
						@open-model="showDepartmentDialog = true"
					>
					</create-prepend-item>
					<v-divider />
				</template>
				<template #label>
					<required-label :label="$t('employee.input.department')"></required-label>
				</template>
			</list-filter>
		</v-col>
		<v-col cols="12" sm="6" class="mb-3">
			<list-filter
				:hide-details="false"
				v-model="position_id"
				:items="positionByDepartment"
				searchable
				item-title="name"
				item-value="id"
				:loading="loadingPositions"
				:disabled="disablePosition"
				:rules="employeeValidation.position"
				:error-messages="errorMessage.getPositionList"
				@click="getPositionList(department_id)"
				:clearable="false"
			>
				<template #prepend-item>
					<create-prepend-item
						title="position.title.create"
						@open-model="showPositionDialog = true"
					>
					</create-prepend-item>
					<v-divider />
				</template>
				<template #label>
					<required-label :label="$t('employee.input.position')"></required-label>
				</template>
			</list-filter>
		</v-col>

		<v-col cols="12" class="mt-n4">
			<Checkbox
				:style="{ visibility: department_id ? 'visible' : 'hidden' }"
				color="primary"
				v-model="is_leader"
				:label="$t('employee.input.isLeader')"
				name="is_leader"
			/>
		</v-col>
	</v-row>

	<!-- Dialog Create Position -->
	<v-dialog v-model="showPositionDialog" :max-width="defaultConfig.maxWidthForm" persistent>
		<PositionForm @save="showPositionDialog = false" @cancel="showPositionDialog = false" />
	</v-dialog>

	<!-- Dialog Create Department -->
	<v-dialog v-model="showDepartmentDialog" :max-width="defaultConfig.maxWidthForm" persistent>
		<DepartmentForm
			@save="showDepartmentDialog = false"
			@cancel="showDepartmentDialog = false"
		/>
	</v-dialog>
</template>
