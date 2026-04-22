<script lang="ts" setup>
import employeeValidation from '@/composables/validation/useEmployeeValidation'
import ListFilter from '@/components/list/ListFilter.vue'
import Input from '@/components/form/Input.vue'
import RequiredLabel from '@/components/form/formModal/requiredLabel.vue'
import defaultConfig from '@/config/default'
import CreatePrependItem from '@/components/form/AddItemListBtn.vue'
import RoleForm from '@/components/form/formModal/RoleForm.vue'
import { computed, onMounted, ref, watch } from 'vue'
import { useRoleStore } from '@/stores/role'
import { useThrottleStore } from '@/stores/throttle'
import { storeToRefs } from 'pinia'
import ThrottleAlert from '@/components/ThrottleAlert.vue'
import RetryBtn from '@/components/RetryBtn.vue'
import SYSTEM from '@/config/system'

const email = defineModel<string>('email')
const role_ids = defineModel<number[]>('role_ids', { default: [] })

const { rolesFetch } = useRoleStore()
const { roles } = storeToRefs(useRoleStore())

const { initThrottle, startThrottle } = useThrottleStore()
const { throttle, isDisabled } = storeToRefs(useThrottleStore())

const showRoleDialog = ref<boolean>(false)
const loadingRole = ref<boolean>(false)

const isError = computed<boolean>(
	() => isDisabled.value('roleFetch') || !!getRolesErrorMessage.value,
)

const getRolesErrorMessage = ref<string>('')

const getRoleList = async () => {
	if (isDisabled.value('roleFetch')) return

	try {
		loadingRole.value = true
		await rolesFetch()
		getRolesErrorMessage.value = ''
	} catch (error: any) {
		getRolesErrorMessage.value = 'common.error.fetchDataFailed'

		if (error.status === SYSTEM.SERVER_ERROR.TOO_MANY_REQUESTS) {
			throttle.value['roleFetch'] = Number(error.response.headers['retry-after'])
			startThrottle('roleFetch')
		}
	} finally {
		loadingRole.value = false
	}
}

onMounted(() => {
	initThrottle('roleFetch')
})
</script>

<template>
	<v-row dense>
		<v-col cols="12" sm="6" class="mb-3">
			<Input
				v-model="email"
				placeholder="example@company.com"
				:rules="employeeValidation.email"
			>
				<template #label>
					<required-label :label="$t('employee.input.email')"></required-label>
				</template>
			</Input>
		</v-col>

		<v-col cols="12" sm="6" class="mb-3">
			<list-filter
				v-model="role_ids"
				:items="roles"
				searchable
				item-title="name"
				item-value="id"
				:rules="isError ? [] : employeeValidation.role"
				:error-messages="isDisabled('roleFetch') ? '' : getRolesErrorMessage"
				:loading="loadingRole"
				:clearable="false"
				multiple
				@click="getRoleList"
			>
				<template #prepend-item>
					<create-prepend-item
						title="role.title.create"
						@open-model="showRoleDialog = true"
					/>
					<v-divider />
				</template>

				<template #label>
					<required-label :label="$t('employee.input.role')"></required-label>
				</template>

				<template #append v-if="isError">
					<retry-btn
						@click.stop="getRoleList"
						only-icon
						:disabled="isDisabled('roleFetch')"
					/>
				</template>
			</list-filter>
			<throttle-alert :show="isDisabled('roleFetch')" :time="throttle['roleFetch'] || 0" />
		</v-col>
		<v-col cols="12">
			<v-alert type="info" variant="tonal" density="compact" class="text-caption">
				{{ $t('employee.tooltip.autoSendAuthEmail') }}
			</v-alert>
		</v-col>
	</v-row>

	<!-- Dialog Create Role -->
	<v-dialog v-model="showRoleDialog" :max-width="defaultConfig.maxWidthForm" persistent>
		<RoleForm @save="showRoleDialog = false" @cancel="showRoleDialog = false" />
	</v-dialog>
</template>
