<script lang="ts" setup>
import useEmployeeValidation from '@/composables/validation/useEmployeeValidation'
import ListFilter from '@/components/list/ListFilter.vue'
import Input from '@/components/form/Input.vue'
import RequiredLabel from '@/components/form/formModal/RequiredLabel.vue'
import CONFIG from '@/config/constants'
import CreatePrependItem from '@/components/form/AddItemListBtn.vue'
import RoleForm from '@/components/form/formModal/RoleForm.vue'
import { onMounted, ref } from 'vue'
import { useRoleStore } from '@/stores/role'
import { useThrottleStore } from '@/stores/throttle'
import { storeToRefs } from 'pinia'
import ThrottleAlert from '@/components/ThrottleAlert.vue'
import RetryBtn from '@/components/RetryBtn.vue'
import SYSTEM from '@/config/system'
import { formatLaravelRetryAfter } from '@/utils/errorHandler'
import { useFilterFetch } from '@/composables/useFilterFetch'

const email = defineModel<string>('email')
const role_ids = defineModel<number[]>('role_ids', { default: [] })

const { rolesFetch } = useRoleStore()
const { roles } = storeToRefs(useRoleStore())

const { employeeValidation } = useEmployeeValidation()
const { initThrottle, startThrottle } = useThrottleStore()
const { throttle, isDisabled } = storeToRefs(useThrottleStore())

const showRoleDialog = ref<boolean>(false)

const {
	loading: loadingRole,
	isError,
	errorMessage: getRolesErrorMessage,
	fetchFilterData: getRoleList,
	checkDisabledError,
} = useFilterFetch(rolesFetch, 'roleFetch')

onMounted(() => {
	initThrottle('roleFetch')

	checkDisabledError()
})
</script>

<template>
	<!-- Account Section -->
	<v-row dense>
		<!-- Email -->
		<v-col cols="12" sm="6" class="mb-3">
			<Input v-model="email" placeholder="example@company.com" :rules="employeeValidation.email">
				<template #label>
					<required-label :label="$t('employee.input.email')"></required-label>
				</template>
			</Input>
		</v-col>

		<!-- Role -->
		<v-col cols="12" sm="6" class="mb-3">
			<list-filter v-model="role_ids" :items="roles" searchable item-title="name" item-value="id"
				:rules="isError ? [] : employeeValidation.role"
				:error-messages="isDisabled('roleFetch') ? '' : getRolesErrorMessage" :loading="loadingRole"
				:clearable="false" multiple @click.stop="getRoleList">
				<template #prepend-item>
					<create-prepend-item title="role.title.create" @open-model="showRoleDialog = true" />
					<v-divider />
				</template>

				<template #label>
					<required-label :label="$t('employee.input.role')"></required-label>
				</template>

				<template #append v-if="isError">
					<retry-btn @click.stop="getRoleList" only-icon :disabled="isDisabled('roleFetch')" />
				</template>
			</list-filter>

			<!-- Throttle Alert -->
			<throttle-alert :show="isDisabled('roleFetch')" :time="throttle['roleFetch'] || 0" />
		</v-col>

		<!-- Note: Auto Send Auth Email -->
		<v-col cols="12">
			<v-alert type="info" variant="tonal" density="compact" class="text-caption">
				{{ $t('employee.tooltip.autoSendAuthEmail') }}
			</v-alert>
		</v-col>
	</v-row>

	<!-- Dialog Create Role -->
	<v-dialog v-model="showRoleDialog" :max-width="CONFIG.maxWidthForm" persistent>
		<RoleForm @save="showRoleDialog = false" @cancel="showRoleDialog = false" />
	</v-dialog>
</template>
