<script lang="ts" setup>
import Input from '@/components/form/Input.vue'
import defaultConfig from '@/config/default'
import requiredLabel from '@/components/form/formModal/requiredLabel.vue'
import employeeValidation from '@/composables/validation/useEmployeeValidation'
import ListFilter from '@/components/list/ListFilter.vue'
import AnnotationTooltip from '@/components/form/AnnotationTooltip.vue'
import { useFilterModule } from '@/composables/useFilterModule'
import { useAdministrativeUnitStore } from '@/stores/administrativeUnit'
import { storeToRefs } from 'pinia'
import { computed, ref, watch } from 'vue'
import i18n from '@/plugins/vueI18n'
import { formatDate } from '@/utils/dateFormat'

interface ErrorMessage {
	province: string
	ward: string
}

const name = defineModel<string>('name')
const code = defineModel<string>('code')
const gender = defineModel<string | null>('gender', { default: null })
const birth_date = defineModel<string | null>('birth_date')
const phone_number = defineModel<string>('phone_number')
const address = defineModel<string>('address')
const ward_code = defineModel<string | null>('ward_code', { default: null })
const province_code = defineModel<string | null>('province_code', { default: null })

const { genders } = useFilterModule()

const formatBirthdate = computed({
	get() {
		return birth_date.value ? new Date(birth_date.value) : null
	},
	set(value: Date | null) {
		birth_date.value = formatDate(value)
	},
})

const provinceLoading = ref<boolean>(false)
const wardLoading = ref<boolean>(false)

const errorMessage = ref<ErrorMessage>({
	province: '',
	ward: '',
})

const disableWard = ref<boolean>(true)

const { provincesFetch, wardsFetchByProvinceCode, wardsReset } = useAdministrativeUnitStore()
const { provinces, wards } = storeToRefs(useAdministrativeUnitStore())

const getProvinces = async () => {
	try {
		provinceLoading.value = true
		await provincesFetch()
		errorMessage.value.province = ''
	} catch (error: any) {
		if (error.status === 500) {
			errorMessage.value.province = error.response?.data?.messageCode
		}
	} finally {
		provinceLoading.value = false
	}
}

const getWards = async (provinceCode: string | null) => {
	if (!provinceCode) {
		disableWard.value = true
		return
	}

	try {
		wardLoading.value = true
		await wardsFetchByProvinceCode(provinceCode)
		errorMessage.value.ward = ''
	} catch (error: any) {
		if (error.status === 500) {
			errorMessage.value.ward = error.response?.data?.messageCode
		}

		if (error.status === 422) {
			errorMessage.value.ward = error.response?.data?.message
		}
	} finally {
		wardLoading.value = false
	}
}

watch(province_code, async (newVal) => {
	if (!newVal) return

	ward_code.value = null
	disableWard.value = false
	wardsReset()
	await getWards(newVal)
})
</script>

<template>
	<v-row dense>
		<v-col cols="12" sm="6" class="mb-3">
			<Input
				v-model="name"
				:maxlength="defaultConfig.maxLengthName"
				counter
				:rules="employeeValidation.name"
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
					<annotation-tooltip
						text="employee.tooltip.codeAutoGenerate"
					></annotation-tooltip>
				</template>
			</Input>
		</v-col>

		<v-col cols="12" sm="6" class="mb-3">
			<list-filter
				:hide-details="false"
				v-model="gender"
				:items="genders"
				searchable
				item-title="name"
				item-value="id"
				:rules="employeeValidation.gender"
				:clearable="false"
			>
				<template #label>
					<required-label :label="$t('employee.input.gender')"></required-label>
				</template>
			</list-filter>
		</v-col>
		<v-col cols="12" sm="6" class="mb-3">
			<v-date-input
				v-model="formatBirthdate"
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
				v-model="phone_number"
				placeholder="0987654321"
				prefix="+84"
				:maxlength="defaultConfig.sizePhone"
				counter
				:rules="employeeValidation.phone"
			>
				<template #label>
					<required-label :label="$t('employee.input.phone')"></required-label>
				</template>
			</Input>
		</v-col>

		<v-col cols="12" sm="6"></v-col>

		<!-- Address Split (1 row) -->
		<v-col cols="12" sm="4" class="mb-3">
			<list-filter
				:hide-details="false"
				v-model="province_code"
				:items="provinces"
				searchable
				:item-title="i18n.global.locale.value === 'vi' ? 'full_name' : 'full_name_en'"
				item-value="code"
				:rules="employeeValidation.province"
				:error-messages="errorMessage.province"
				:loading="provinceLoading"
				:clearable="false"
				@click="getProvinces()"
			>
				<template #label>
					<required-label :label="$t('employee.input.province')"></required-label>
				</template>
			</list-filter>
		</v-col>
		<v-col cols="12" sm="4" class="mb-3">
			<list-filter
				:hide-details="false"
				v-model="ward_code"
				:items="wards"
				searchable
				:item-title="i18n.global.locale.value === 'vi' ? 'full_name' : 'full_name_en'"
				item-value="code"
				:rules="employeeValidation.ward"
				:error-messages="errorMessage.ward"
				:disabled="disableWard"
				:clearable="false"
				:loading="wardLoading"
				@click="getWards(province_code)"
			>
				<template #label>
					<required-label :label="$t('employee.input.ward')"></required-label>
				</template>
			</list-filter>
		</v-col>
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
	</v-row>
</template>
