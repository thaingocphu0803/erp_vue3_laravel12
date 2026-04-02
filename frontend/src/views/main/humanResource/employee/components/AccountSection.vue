<script lang="ts" setup>
import employeeValidation from '@/composables/validation/useEmployeeValidation';
import ListFilter from '@/components/list/ListFilter.vue';
import Input from '@/components/form/Input.vue';
import RequiredLabel from '@/components/form/formModal/requiredLabel.vue';

const email = defineModel('email')
const role = defineModel('role')

</script>

<template>
	<h4 class="text-h6 font-weight-bold mb-4 text-primary">{{ $t('employee.section.account') }}</h4>
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
						:hide-details="false"
						v-model="role"
						:items="[]"
						searchable
						item-title="name"
						item-value="id"
						:rules="employeeValidation.role"
					>
						<template #prepend-item>
							<!-- <create-prepend-item title="role.title.create" @open-model="showRoleDialog = true"/> -->
							<v-divider />
						</template>
						<template #label>
							<required-label :label="$t('employee.input.role')"></required-label>
						</template>
					</list-filter>
		</v-col>
		<v-col cols="12">
			<v-alert
				type="info"
				variant="tonal"
				density="compact"
				class="text-caption"
			>
				{{ $t('employee.tooltip.autoSendAuthEmail') }}
			</v-alert>
		</v-col>
	</v-row>

</template>