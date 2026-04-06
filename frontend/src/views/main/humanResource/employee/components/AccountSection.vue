<script lang="ts" setup>
import employeeValidation from '@/composables/validation/useEmployeeValidation'
import ListFilter from '@/components/list/ListFilter.vue'
import Input from '@/components/form/Input.vue'
import RequiredLabel from '@/components/form/formModal/requiredLabel.vue'
import defaultConfig from '@/config/default'
import CreatePrependItem from '@/components/form/AddItemListBtn.vue'
import RoleForm from '@/components/form/formModal/RoleForm.vue'
import { ref } from 'vue'

import { useRoleStore } from '@/stores/role'
import { storeToRefs } from 'pinia'

const email = defineModel('email')
const role = defineModel('role')

const { rolesFetch } = useRoleStore()
const { roles } = storeToRefs(useRoleStore())

const showRoleDialog = ref<boolean>(false)
const loadingRole = ref<boolean>(false)
const getRolesErrorMessage = ref<string>('')

const getRoleList = async () => {
	try {
		loadingRole.value = true
		await rolesFetch()
		getRolesErrorMessage.value = ''
	} catch (error: any) {
		if (error.status === 400 || error.status === 500) {
			getRolesErrorMessage.value = error.response?.data?.messageCode
		}
	} finally {
		loadingRole.value = false
	}
}
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
			<list-filter :hide-details="false" v-model="role" :items="roles" searchable item-title="name"
				item-value="id" :rules="employeeValidation.role" :error-messages="getRolesErrorMessage"
				:loading="loadingRole" :clearable="false" @click="getRoleList">
				<template #prepend-item>
					<create-prepend-item title="role.title.create" @open-model="showRoleDialog = true" />
					<v-divider />
				</template>
				<template #label>
					<required-label :label="$t('employee.input.role')"></required-label>
				</template>
			</list-filter>
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

