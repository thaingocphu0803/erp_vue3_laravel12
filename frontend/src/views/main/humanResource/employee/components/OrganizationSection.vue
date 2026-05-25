<script lang="ts" setup>
import ListFilter from '@/components/list/ListFilter.vue'
import RequiredLabel from '@/components/form/formModal/RequiredLabel.vue'
import useEmployeeValidation from '@/composables/validation/useEmployeeValidation'
import { useDepartmentStore } from '@/stores/department'
import { usePositionStore } from '@/stores/position'
import { storeToRefs } from 'pinia'
import { computed, onMounted, ref, watch } from 'vue'
import Checkbox from '@/components/form/CheckBox.vue'
import CONFIG from '@/config/constants'
import DepartmentForm from '@/components/form/formModal/DepartmentForm.vue'
import PositionForm from '@/components/form/formModal/PositionForm.vue'
import CreatePrependItem from '@/components/form/AddItemListBtn.vue'
import SYSTEM from '@/config/system'
import { useThrottleStore } from '@/stores/throttle'
import RetryBtn from '@/components/RetryBtn.vue'
import ThrottleAlert from '@/components/ThrottleAlert.vue'
import { formatLaravelRetryAfter } from '@/utils/errorHandler'
import { useFilterFetch } from '@/composables/useFilterFetch'

// interface ErrorMessage {
// 	getDepartmentList: string
// 	getPositionList: string
// }

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

const { employeeValidation } = useEmployeeValidation()
const { throttle, isDisabled } = storeToRefs(useThrottleStore())
const { initThrottle, startThrottle } = useThrottleStore()

const showIsLeader = computed(() => {
	const department = departments.value.find((department) => department.id === department_id.value)
	return department ? department.leader_id === null : false
})

const { 
	loading: loadingDepartments,
	isError: isDepartmentError,
	errorMessage: errorMessageGetDepartmentList,
	fetchFilterData: getDepartmentList,
	checkDisabledError: checkDisabledDepartmentError,
} = useFilterFetch(departmentsFetch, 'departmentFetch')
const { 
	loading: loadingPositions,
	isError: isPositionError, 
	errorMessage: errorMessageGetPositionList, 
	fetchFilterData: getPositionList 
} = useFilterFetch(() => positionsFetchByDepartmentId(department_id.value), 'positionFetch')

const showPositionDialog = ref<boolean>(false)
const showDepartmentDialog = ref<boolean>(false)

onMounted(() => {
	initThrottle('departmentFetch')
	initThrottle('positionFetch')

	checkDisabledDepartmentError()
})

watch(department_id, (newValue) => {
	if (!newValue) return

	position_id.value = null
	disablePosition.value = false
	positionReset()
	getPositionList(newValue)
})
</script>

<template>
	<!-- Organization Section -->
	<v-row dense>
		<!-- Department -->
		<v-col cols="12" sm="6" class="mb-3">
			<list-filter v-model="department_id" :items="departments" searchable item-title="name" item-value="id"
				:loading="loadingDepartments" :rules="isDepartmentError ? [] : employeeValidation.department"
				:error-messages="isDisabled('departmentFetch') ? '' : errorMessageGetDepartmentList"
				@click="getDepartmentList" :clearable="false">
				<template #prepend-item>
					<create-prepend-item title="department.title.create" @open-model="showDepartmentDialog = true">
					</create-prepend-item>
					<v-divider />
				</template>
				<template #label>
					<required-label :label="$t('employee.input.department')"></required-label>
				</template>
				<template #append v-if="isDepartmentError">
					<retry-btn @click.stop="getDepartmentList" only-icon
						:disabled="isDisabled('departmentFetch')"></retry-btn>
				</template>
			</list-filter>

			<!-- Throttle Alert -->
			<throttle-alert :show="isDisabled('departmentFetch')"
				:time="throttle['departmentFetch'] || 0"></throttle-alert>
		</v-col>

		<!-- Position -->
		<v-col cols="12" sm="6" class="mb-3">
			<list-filter v-model="position_id" :items="positionByDepartment" searchable item-title="name"
				item-value="id" :loading="loadingPositions" :disabled="disablePosition"
				:rules="isPositionError ? [] : employeeValidation.position"
				:error-messages="isDisabled('positionFetch') ? '' : errorMessageGetPositionList"
				@click="getPositionList(department_id)" :clearable="false">
				<template #prepend-item>
					<create-prepend-item title="position.title.create" @open-model="showPositionDialog = true">
					</create-prepend-item>
					<v-divider />
				</template>
				<template #label>
					<required-label :label="$t('employee.input.position')"></required-label>
				</template>
				<template #append v-if="isPositionError">
					<retry-btn @click.stop="getPositionList(department_id)" only-icon
						:disabled="isDisabled('positionFetch')"></retry-btn>
				</template>
			</list-filter>

			<!-- Throttle Alert -->
			<throttle-alert :show="isDisabled('positionFetch')" :time="throttle['positionFetch'] || 0"></throttle-alert>
		</v-col>

		<!-- Is Leader -->
		<v-col cols="12" class="mt-n4">
			<Checkbox :style="{ visibility: showIsLeader ? 'visible' : 'hidden' }" color="primary" v-model="is_leader"
				:label="$t('employee.input.isLeader')" name="is_leader" />
		</v-col>
	</v-row>

	<!-- Dialog Create Position -->
	<v-dialog v-model="showPositionDialog" :max-width="CONFIG.maxWidthForm" persistent>
		<PositionForm @save="showPositionDialog = false" @cancel="showPositionDialog = false" />
	</v-dialog>

	<!-- Dialog Create Department -->
	<v-dialog v-model="showDepartmentDialog" :max-width="CONFIG.maxWidthForm" persistent>
		<DepartmentForm @save="showDepartmentDialog = false" @cancel="showDepartmentDialog = false" />
	</v-dialog>
</template>
