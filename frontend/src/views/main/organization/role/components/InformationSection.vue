<script lang="ts" setup>
import Input from '@/components/form/Input.vue'
import Textarea from '@/components/form/Textarea.vue'
import useRoleValidation from '@/composables/validation/useRoleValidation'
import CONFIG from '@/config/constants'
import RequiredLabel from '@/components/form/formModal/RequiredLabel.vue'
import AnnotationTooltip from '@/components/form/AnnotationTooltip.vue'

const { roleValidation } = useRoleValidation()
const roleName = defineModel('roleName')
const roleCode = defineModel('roleCode')
const roleDescription = defineModel('roleDescription')
</script>

<template>
	<!-- Role Information Section -->
	<v-row dense>
		<!-- Role Name -->
		<v-col cols="12" md="6" class="mb-3">
			<Input
				name="name"
				:rules="roleValidation.name"
				v-model="roleName"
				:maxlength="CONFIG.maxLengthName"
				counter
			>
				<template #label>
					<required-label :label="$t('role.input.roleName')"></required-label>
				</template>
			</Input>
		</v-col>

		<!-- Role Code -->
		<v-col cols="12" md="6">
			<Input
				:label="$t('role.input.roleCode')"
				name="code"
				v-model="roleCode"
				:maxlength="CONFIG.maxLengthCode"
				counter
			>
				<template #append-inner>
					<annotation-tooltip text="role.tooltip.codeAutoGenerate"></annotation-tooltip>
				</template>
			</Input>
		</v-col>

		<!-- Role Description -->
		<v-col cols="12">
			<Textarea
				:label="$t('role.input.roleDescription')"
				name="description"
				v-model="roleDescription"
			></Textarea>
		</v-col>
	</v-row>
</template>

