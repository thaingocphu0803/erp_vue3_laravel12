<script setup lang="ts">
import Form from '@/components/Form.vue'
import ErrorAlert from '../ErrorAlert.vue'
import { reactive, watch } from 'vue'
import { mapLaravelError } from '@/utils/errorHandler'
import { useRoleStore } from '@/stores/role'
import PermissionSection from '@/views/main/organization/role/components/PermissionSection.vue'
import InformationSection from '@/views/main/organization/role/components/InformationSection.vue'
import BaseBtn from '@/components/BaseBtn.vue'
import type { RolePermission } from '@/stores/permission'
import { useToastStore } from '@/stores/toast'

interface RoleForm {
	name: string
	description: string
	permissions: RolePermission
}

interface ErrorMessage {
	name: string
	permissions: string
	description: string
}

const emit = defineEmits(['save', 'cancel'])

const { roleCreate } = useRoleStore()
const toast = useToastStore()

const roleData = reactive<RoleForm>({
	name: '',
	description: '',
	permissions: {},
})

const errorMessage = reactive<ErrorMessage>({
	name: '',
	permissions: '',
	description: '',
})

const sections = [
	{ id: 1, title: 'role.step.info' },
	{ id: 2, title: 'role.step.permission' },
]

const handleSubmit = async () => {
	try {
		const response = await roleCreate(roleData)
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

const updatePermision = (permissionsRecord: RolePermission) => {
	const newPermissions = Object.fromEntries(
		Object.entries(permissionsRecord).filter(([_, scope]) => scope !== 'NONE'),
	)

	roleData.permissions = { ...newPermissions }
}

watch(
	() => roleData.permissions,
	(newPermissions) => {
		if (Object.keys(newPermissions).length === 0) {
			errorMessage.permissions = 'role.validate.permissions.atLeastOne'
		} else {
			errorMessage.permissions = ''
		}
	},
	{ deep: true },
)

const handleCancel = () => {
	emit('cancel')
}
</script>

<template>
	<Form title="role.title.create" @submit-form="handleSubmit">
		<error-alert :messages="errorMessage"></error-alert>

		<template v-for="section in sections" :key="section.id">
			<h4 class="text-h6 font-weight-bold mb-4 text-primary">{{ $t(section.title) }}</h4>
			<information-section
				v-if="section.id === 1"
				v-model:role-name="roleData.name"
				v-model:role-description="roleData.description"
				class="mt-2"
			/>

			<!-- content for step 2: select role -->
			<permission-section v-else @update:selected-permissions="updatePermision" />

			<v-divider v-if="section.id !== sections.length"></v-divider>
		</template>

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
				<BaseBtn title="common.btn.create" color="primary" type="submit" />
			</v-col>
		</v-row>
	</Form>
</template>

