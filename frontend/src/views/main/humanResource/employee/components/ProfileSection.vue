<script lang="ts" setup>
import Input from '@/components/form/Input.vue'
import defaultConfig from '@/config/default'
import requiredLabel from '@/components/form/formModal/requiredLabel.vue'
import employeeValidation from '@/composables/validation/useEmployeeValidation'
import ListFilter from '@/components/list/ListFilter.vue'
import AnnotationTooltip from '@/components/form/AnnotationTooltip.vue'

const fullName = defineModel('fullName')
const code = defineModel('code')
const gender = defineModel('gender')
const birthDate = defineModel('birthDate')
const phone = defineModel('phone')
const address = defineModel('address')
const ward = defineModel('ward')
const province = defineModel('province')

</script>

<template>
	<h4 class="text-h6 font-weight-bold mb-4 text-primary">{{ $t('employee.section.profile') }}</h4>
	<v-row dense>
		<v-col cols="12" sm="6" class="mb-3">
			<Input
				v-model="fullName"
				:maxlength="defaultConfig.maxLengthName"
				counter
				:rules="employeeValidation.fullName"
			>
				<template #label>
					<required-label :label="$t('employee.input.fullName')"></required-label>
				</template>
			</Input>
		</v-col>

		<v-col cols="12" sm="6" class="mb-3">
			<Input
				v-model="code"
				:label="$t('employee.input.employeeCode')"
				:maxlength="defaultConfig.maxLengthCode"
				counter
			>
				<template #append-inner>
					<annotation-tooltip text="employee.tooltip.codeAutoGenerate"></annotation-tooltip>
				</template>
			</Input>
		</v-col>

		<v-col cols="12" sm="6" class="mb-3">
			<list-filter
				:hide-details="false"
				v-model="gender"
				:items="[]"
				searchable
				item-title="name"
				item-value="id"
				:rules="employeeValidation.gender"
			>
				<template #label>
					<required-label :label="$t('employee.input.gender')"></required-label>
				</template>
			</list-filter>
		</v-col>
		<v-col cols="12" sm="6" class="mb-3">
			<v-date-input	
				v-model="birthDate"
				density="compact"
				variant="outlined"
				input-format="yyyy/mm/dd"
				prepend-inner-icon="mdi-calendar"
				prepend-icon=""
				:rules="employeeValidation.birthDate"
				:max="defaultConfig.currentDate"
				@keydown.prevent
			>
				<template #label>
					<required-label :label="$t('employee.input.birthDate')"></required-label>
				</template>
				<template v-slot:message="{ message }">{{ $t(message) }}</template>
			</v-date-input>
		</v-col>

		<!-- Phone -->
		<v-col cols="12" sm="6" class="mb-3">
			<Input
				v-model="phone"
				placeholder="0987654321"
				prefix="+84"
				:maxlength="defaultConfig.minLengthPhone"
				counter
				:rules="employeeValidation.phone"
			>
				<template #label>
					<required-label :label="$t('employee.input.phone')"></required-label>
				</template>
			</Input>
		</v-col>

		<v-col cols="12" sm="6"></v-col>

		<v-col cols="12" sm="4" class="mb-3">
			<Input
				v-model="address"
				:placeholder="$t('employee.placeholder.addressExample')"
				:maxlength="defaultConfig.maxLengthAddress"
				counter
				:rules="employeeValidation.address"
			>
				<template #label>
					<required-label :label="$t('employee.input.address')"></required-label>
				</template>
			</Input>
		</v-col>

		<!-- Address Split (1 row) -->
		<v-col cols="12" sm="4" class="mb-3">
			<list-filter	
				:hide-details="false"
				v-model="ward"
				:items="[]"
				searchable
				item-title="name"
				item-value="id"
				:rules="employeeValidation.ward"
			>
				<template #label>
					<required-label :label="$t('employee.input.ward')"></required-label>
				</template>
			</list-filter>
		</v-col>
		<v-col cols="12" sm="4" class="mb-3">
			<list-filter
				:hide-details="false"
				v-model="province"
				:items="[]"
				searchable
				item-title="name"
				item-value="id"
				:rules="employeeValidation.province"
			>
				<template #label>
					<required-label :label="$t('employee.input.province')"></required-label>
				</template>
			</list-filter>
		</v-col>
	</v-row>	
</template>