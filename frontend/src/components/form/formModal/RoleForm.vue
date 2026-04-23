<script setup lang="ts">
import Form from '@/components/Form.vue'
import ErrorAlert from '../ErrorAlert.vue'
import { onMounted, reactive, watch } from 'vue'
import { formatLaravelRetryAfter, mapLaravelError } from '@/utils/errorHandler'
import { useRoleStore } from '@/stores/role'
import PermissionSection from '@/views/main/organization/role/components/PermissionSection.vue'
import InformationSection from '@/views/main/organization/role/components/InformationSection.vue'
import BaseBtn from '@/components/BaseBtn.vue'
import { useToastStore } from '@/stores/toast'
import SYSTEM from '@/config/system'
import type { RoleFormError, RoleFormData, RolePermission } from '@/types/role'
import ThrottleAlert from '@/components/ThrottleAlert.vue'
import { useThrottleStore } from '@/stores/throttle'
import { storeToRefs } from 'pinia'

// title
const title = 'role.title.create'

// emits
const emit = defineEmits(['save', 'cancel'])

// role stores
const { roleCreate } = useRoleStore()

// toast
const toast = useToastStore()

// throttle
const { initThrottle, startThrottle } = useThrottleStore()
const { throttle, isDisabled } = storeToRefs(useThrottleStore())

// role form data
const roleFormData = reactive<RoleFormData>({
	name: '',
	description: '',
	permissions: {},
})

// error message
const errorMessage = reactive<RoleFormError>({
	name: '',
	permissions: '',
	description: '',
})

// sections
const sections = [
	{ id: 1, title: 'role.step.info' },
	{ id: 2, title: 'role.step.permission' },
]

// handle submit
const handleSubmit = async () => {
	try {
		const response = await roleCreate(roleFormData)
		toast.show(response.data.messageCode, 'success')
		emit('save')
	} catch (error: any) {
		if (error.status === SYSTEM.SERVER_ERROR.UNPROCESSABLE_ENTITY) {
			mapLaravelError(errorMessage, error)
		}

		if (error.status === SYSTEM.SERVER_ERROR.INTERNAL_SERVER_ERROR) {
			const messageCode = 'role.alert.error.create'
			toast.show(messageCode, 'error')
		}

		if (error.status === SYSTEM.SERVER_ERROR.TOO_MANY_REQUESTS) {
			throttle.value['roleCreate'] = formatLaravelRetryAfter(error)
			startThrottle('roleCreate')
		}
	}
}

// handle update permission
const updatePermision = (permissionsRecord: RolePermission) => {
	const newPermissions = Object.fromEntries(
		Object.entries(permissionsRecord).filter(([_, scope]) => scope !== 'NONE'),
	)

	roleFormData.permissions = { ...newPermissions }
}

onMounted(() => {
	initThrottle('roleCreate')
})

// watch permission
watch(
	() => roleFormData.permissions,
	(newPermissions) => {
		if (Object.keys(newPermissions).length === 0) {
			errorMessage.permissions = 'role.validate.permissions.atLeastOne'
		} else {
			errorMessage.permissions = ''
		}
	},
	{ deep: true },
)

// handle cancel
const handleCancel = () => {
	emit('cancel')
}
</script>

<template>
	<Form :title @submit-form="handleSubmit">
		<!-- Error Message -->
		<error-alert :messages="errorMessage"></error-alert>

		<!-- Content -->
		<template v-for="section in sections" :key="section.id">
			<h4 class="text-h6 font-weight-bold mb-4 text-primary">{{ $t(section.title) }}</h4>

			<!-- Step 1: Information -->
			<information-section v-if="section.id === 1" v-model:role-name="roleFormData.name"
				v-model:role-description="roleFormData.description" class="mt-2" />

			<!-- Step 2: Permission -->
			<permission-section v-else @update:selected-permissions="updatePermision" />

			<v-divider v-if="section.id !== sections.length"></v-divider>
		</template>

		<v-row dense justify="center">
			<!-- Throttle Alert -->
			<throttle-alert :time="throttle['roleCreate'] || 0" :show="isDisabled('roleCreate')" />
		</v-row>

		<!-- Actions -->
		<v-row dense justify="space-between" class="mt-2">
			<!-- cancel button -->
			<v-col cols="auto">
				<BaseBtn title="common.btn.cancel" color="red-darken-1" @click.prevent="handleCancel" />
			</v-col>

			<!-- create button -->
			<v-col cols="auto">
				<BaseBtn title="common.btn.create" color="primary" type="submit" :disabled="isDisabled('roleCreate')" />
			</v-col>
		</v-row>
	</Form>
</template>
