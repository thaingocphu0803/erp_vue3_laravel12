<script lang="ts" setup>
import Input from '@/components/form/Input.vue'
import Textarea from '@/components/form/Textarea.vue'
import roleValidation from '@/composables/validation/useRoleValidation'
import defaultConfig from '@/config/default'

const roleName = defineModel('roleName')
const roleDescription = defineModel('roleDescription')

</script>

<template>
	<v-row dense class="ga-3">
		<v-col cols="12">
			<Input
				name="name"
				:rules="roleValidation.name"
				v-model="roleName"
				:maxlength="defaultConfig.maxLengthName"
				counter
			>
				<template #label>
					{{ $t('role.input.roleName') }}
					<span class="text-error">*</span>
				</template>
			</Input>
		</v-col>

		<v-col cols="12">
			<Textarea
				:label="$t('role.input.roleDescription')"
				name="description"
				v-model="roleDescription"
			></Textarea>
		</v-col>
	</v-row>
</template>
