<script lang="ts" setup>
import ListFilter from '@/components/list/ListFilter.vue'
import requiredLabel from '@/components/form/formModal/requiredLabel.vue'
import employeeValidation from '@/composables/validation/useEmployeeValidation'

const department = defineModel('department')
const position = defineModel('position')

</script>

<template>
	<h4 class="text-h6 font-weight-bold mb-4 text-primary">{{ $t('employee.section.organization') }}</h4>
	<v-row dense>
		<v-col cols="12" sm="6" class="mb-3">
			<v-row dense>
				<v-col class="flex-grow-1">
					<list-filter
						:hide-details="false"
						v-model="department"
						:items="[]"
						searchable
						item-title="name"
						item-value="id"
						:rules="employeeValidation.department"
					>
						<template #prepend-item>
							<!-- <create-prepend-item title="department.title.create" @open-model="showDepartmentDialog = true"/> -->
							<v-divider />
						</template>
						<template #label>
							<required-label :label="$t('employee.input.department')"></required-label>
						</template>
					</list-filter>
				</v-col>
			</v-row>
		</v-col>
		<v-col cols="12" sm="6">
			<v-row dense>
				<v-col class="flex-grow-1">
					<list-filter
						:hide-details="false"
						v-model="position"
						:items="[]"
						searchable
						item-title="name"
						item-value="id"
						:rules="employeeValidation.position"
					>	
						<template #prepend-item>
							<!-- <create-prepend-item title="position.title.create" @open-model="showPositionDialog = true"/> -->
							<v-divider />
						</template>
						<template #label>
							<required-label :label="$t('employee.input.position')"></required-label>
						</template>
					</list-filter>
				</v-col>
			</v-row>
		</v-col>
	</v-row>
</template>