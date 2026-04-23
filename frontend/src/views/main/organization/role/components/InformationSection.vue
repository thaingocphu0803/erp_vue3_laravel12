<script lang="ts" setup>
import Input from '@/components/form/Input.vue'
import Textarea from '@/components/form/Textarea.vue'
import useRoleValidation from '@/composables/validation/useRoleValidation'
import CONFIG from '@/config/constants'
import RequiredLabel from '@/components/form/formModal/RequiredLabel.vue'

const { roleValidation } = useRoleValidation()
const roleName = defineModel('roleName')
const roleDescription = defineModel('roleDescription')
</script>

<template>
	<!-- Role Information Section -->
	<v-row dense class="ga-3">
		<!-- Role Name -->
		<v-col cols="12">
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

