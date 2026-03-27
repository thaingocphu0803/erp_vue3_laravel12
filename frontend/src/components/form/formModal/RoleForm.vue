<script setup lang="ts">
import Form from '@/components/Form.vue'
import ErrorAlert from '../ErrorAlert.vue'
import { computed, reactive, ref, watch } from 'vue'
import { mapLaravelError } from '@/utils/errorHandler'
import { useRoleStore } from '@/stores/role'
import PermissionForm from '@/views/main/organization/role/components/PermissionForm.vue'
import InformationForm from '@/views/main/organization/role/components/InformationForm.vue'
import BaseBtn from '@/components/BaseBtn.vue'
import roleValidation from '@/composables/validation/useRoleValidation'
import type { RolePermission } from '@/stores/permission'
import { useToastStore } from '@/stores/toast'


interface RoleForm {
	name: string
	description: string
	permissions: RolePermission
}

interface ValidateMessage {
	name: string
	permissions: string
}

const emit = defineEmits(['success', 'cancel'])

const { roleCreate } = useRoleStore()
const toast = useToastStore()

const roleData = reactive<RoleForm>({
	name: '',
	description: '',
	permissions: {},
})

const errorMessage = reactive<ValidateMessage>({
	name: '',
	permissions: '',
})

const selectedScopes = ref<RolePermission>({})

const step = ref<number>(1)

const isDisabled = computed(() => {
	if (step.value === 1) {
		const name = roleData.name
		if (!name || name.trim() === '') return true

		for (const rule of roleValidation.name) {
			if (typeof rule(name) === 'string') return true
		}
	}

	if (step.value === 2) {
		if (Object.keys(roleData.permissions).length === 0) return true
	}

	return false
})

const stepItems = [
	{ id: 1, title: 'role.step.info' },
	{ id: 2, title: 'role.step.permission' },
]

const handleSubmit = async () => {
	try {
		const response = await roleCreate(roleData)
		console.log(response)
		// emit('success')
	} catch (error: any) {
		if (error.status === 422) {
			mapLaravelError(errorMessage, error)
			return
		}

		// toast.show(error.response?.data?.messageCode, 'error')
	}
}

const handleNextStep = async () => {
	if (hasNext(step.value)) {
		step.value++
	} else {
		await handleSubmit()
	}
}

const handlePrevStep = () => {
	if (hasNext(step.value)) {
		emit('cancel')
	} else {
		step.value--
	}
}

const hasNext = (currentStep: number) => {
	if (currentStep < Object.keys(stepItems).length) return true

	return false
}

const updatePermision = (permissionsRecord: RolePermission) => {
	const newPermissions = Object.fromEntries(
		Object.entries(permissionsRecord).filter(([_, scope]) => scope !== 'NONE'),
	)

	roleData.permissions = newPermissions
}

watch(selectedScopes, (newScopes) => {
	updatePermision(newScopes)
}, { deep: true })

watch(() => roleData.permissions, (newPermissions) => {
	if (Object.keys(newPermissions).length === 0) {
		errorMessage.permissions = 'role.validate.permissions.atLeastOne'
	}else{
		errorMessage.permissions = ''
	}
}, { deep: true })

</script>

<template>
	<Form title="role.title.create" @submit-form="handleSubmit">

		<v-stepper v-model="step" class="elevation-0">
			<v-stepper-header class="elevation-0">
				<template v-for="item in stepItems" :key="item.id">
					<v-stepper-item
						:value="item.id"
						:complete="item.id < step"
						color="primary"
						:title="$t(item.title)"
					>
					</v-stepper-item>
					<v-divider v-if="hasNext(item.id)"></v-divider>
				</template>
			</v-stepper-header>

			<v-stepper-window>
				<!-- errror alert -->
				<error-alert :messages="errorMessage"></error-alert>

				<v-stepper-window-item v-for="step in stepItems" :key="step.id" :value="step.id">
					<!-- content for step 1: Add information -->
					<information-form v-if="step.id === 1"
						v-model:role-name="roleData.name"
						v-model:role-description="roleData.description"
						class="mt-2"
					></information-form>

					<!-- content for step 2: select role -->
					<permission-form v-else v-model="selectedScopes"></permission-form>
				</v-stepper-window-item>
			</v-stepper-window>

			<v-stepper-actions>
				<template #prev>
					<base-btn
						@click.prevent="handlePrevStep"
						:title="hasNext(step) ? 'common.btn.cancel' : 'common.btn.back'"
						:color="hasNext(step) ? 'red-darken-1': 'grey-darken-1'"
						:disabled="false"
					></base-btn>
				</template>

				<template #next>
					<base-btn
						@click.prevent="handleNextStep"
						:title="hasNext(step) ? 'common.btn.next' : 'common.btn.create'"
						:disabled="isDisabled"
						color="primary"
					></base-btn>
				</template>
			</v-stepper-actions>
		</v-stepper>
	</Form>
</template>
