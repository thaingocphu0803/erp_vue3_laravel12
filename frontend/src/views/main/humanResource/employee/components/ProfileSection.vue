<script lang="ts" setup>
import Input from '@/components/form/Input.vue'
import CONFIG from '@/config/constants'
import RequiredLabel from '@/components/form/formModal/RequiredLabel.vue'
import useEmployeeValidation from '@/composables/validation/useEmployeeValidation'
import ListFilter from '@/components/list/ListFilter.vue'
import AnnotationTooltip from '@/components/form/AnnotationTooltip.vue'
import { useFilterModule } from '@/composables/useFilterModule'
import { useAdministrativeUnitStore } from '@/stores/administrativeUnit'
import { storeToRefs } from 'pinia'
import { computed, onMounted, ref, watch } from 'vue'
import i18n from '@/plugins/vueI18n'
import { formatDate } from '@/utils/dateFormat'
import type { commonGender, commonLocale } from '@/types/common'
import { useThrottleStore } from '@/stores/throttle'
import ThrottleAlert from '@/components/ThrottleAlert.vue'
import RetryBtn from '@/components/RetryBtn.vue'
import SYSTEM from '@/config/system'
import { formatLaravelRetryAfter } from '@/utils/errorHandler'

interface ErrorMessage {
	province: string
	ward: string
}

const name = defineModel<string>('name')
const code = defineModel<string>('code')
const gender = defineModel<commonGender | null>('gender', { default: null })
const birth_date = defineModel<string | null>('birth_date')
const phone_number = defineModel<string>('phone_number')
const address = defineModel<string>('address')
const ward_code = defineModel<string | null>('ward_code', { default: null })
const province_code = defineModel<string | null>('province_code', { default: null })
const locale = defineModel<commonLocale>('locale', { default: 'en' })

const { employeeValidation } = useEmployeeValidation()
const { genders, locales } = useFilterModule()

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

const isProvinceError = ref<boolean>(false)
const isWardError = ref<boolean>(false)

const errorMessage = ref<ErrorMessage>({
	province: '',
	ward: '',
})

const disableWard = ref<boolean>(true)

const { provincesFetch, wardsFetchByProvinceCode, wardsReset } = useAdministrativeUnitStore()
const { provinces, wards } = storeToRefs(useAdministrativeUnitStore())

const { throttle, isDisabled } = storeToRefs(useThrottleStore())
const { initThrottle, startThrottle } = useThrottleStore()

const getProvinces = async () => {
	if (isDisabled.value('provinceFetch')) return

	try {
		provinceLoading.value = true
		await provincesFetch()
		errorMessage.value.province = ''
		isProvinceError.value = false
	} catch (error: any) {
		errorMessage.value.province = 'common.error.fetchDataFailed'
		isProvinceError.value = true

		if (error.status === SYSTEM.SERVER_ERROR.TOO_MANY_REQUESTS) {
			throttle.value['provinceFetch'] = formatLaravelRetryAfter(error)
			startThrottle('provinceFetch')
		}
	} finally {
		provinceLoading.value = false
	}
}

const getWards = async (provinceCode: string | null) => {
	if (isDisabled.value('wardFetch')) return

	if (!provinceCode) {
		disableWard.value = true
		return
	}

	try {
		wardLoading.value = true
		await wardsFetchByProvinceCode(provinceCode)
		errorMessage.value.ward = ''
		isWardError.value = false
	} catch (error: any) {
		errorMessage.value.ward = 'common.error.fetchDataFailed'
		isWardError.value = true

		if (error.status === SYSTEM.SERVER_ERROR.TOO_MANY_REQUESTS) {
			throttle.value['wardFetch'] = formatLaravelRetryAfter(error)
			startThrottle('wardFetch')
		}

		if (error.status === SYSTEM.SERVER_ERROR.UNPROCESSABLE_ENTITY) {
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

onMounted(() => {
	initThrottle('provinceFetch')
	initThrottle('wardFetch')

	if (isDisabled.value('provinceFetch')) {
		isProvinceError.value = true
	}
})
</script>

<template>
	<!-- Profile Section -->
	<v-row dense>
		<!-- Full Name -->
		<v-col cols="12" sm="6" class="mb-3">
			<Input v-model="name" :maxlength="CONFIG.maxLengthName" counter :rules="employeeValidation.name">
				<template #label>
					<required-label :label="$t('employee.input.fullName')"></required-label>
				</template>
			</Input>
		</v-col>

		<!-- Employee Code -->
		<v-col cols="12" sm="6" class="mb-3">
			<Input v-model="code" :label="$t('employee.input.employeeCode')" :maxlength="CONFIG.maxLengthCode" counter>
				<template #append-inner>
					<annotation-tooltip text="employee.tooltip.codeAutoGenerate"></annotation-tooltip>
				</template>
			</Input>
		</v-col>

		<!-- Gender -->
		<v-col cols="12" sm="6" class="mb-3">
			<list-filter v-model="gender" :items="genders" item-title="name" item-value="id"
				:rules="employeeValidation.gender" :clearable="false">
				<template #label>
					<required-label :label="$t('employee.input.gender')"></required-label>
				</template>
			</list-filter>
		</v-col>

		<!-- Birth Date -->
		<v-col cols="12" sm="6" class="mb-3">
			<v-date-input v-model="formatBirthdate" density="compact" variant="outlined" input-format="yyyy/mm/dd"
				prepend-inner-icon="mdi-calendar" prepend-icon="" :rules="employeeValidation.birthDate"
				:max="CONFIG.currentDate" @keydown.prevent>
				<template #label>
					<required-label :label="$t('employee.input.birthDate')"></required-label>
				</template>
				<template v-slot:message="{ message }">{{ $t(message) }}</template>
			</v-date-input>
		</v-col>

		<!-- Phone -->
		<v-col cols="12" sm="6" class="mb-3">
			<Input v-model="phone_number" placeholder="0987654321" prefix="+84" :maxlength="CONFIG.sizePhone" counter
				:rules="employeeValidation.phone">
				<template #label>
					<required-label :label="$t('employee.input.phone')"></required-label>
				</template>
			</Input>
		</v-col>

		<v-col cols="12" sm="6">
			<list-filter v-model="locale" :items="locales" item-title="name" item-value="id" :clearable="false"
				:label="$t('employee.input.locale')">
			</list-filter>
		</v-col>

		<!-- Address Split (1 row) -->
		<v-col cols="12" sm="4" class="mb-3">
			<list-filter v-model="province_code" :items="provinces" searchable
				:item-title="i18n.global.locale.value === 'vi' ? 'full_name' : 'full_name_en'" item-value="code"
				:rules="isProvinceError ? [] : employeeValidation.province"
				:error-messages="isDisabled('provinceFetch') ? '' : errorMessage.province" :loading="provinceLoading"
				:clearable="false" @click="getProvinces()">
				<template #label>
					<required-label :label="$t('employee.input.province')"></required-label>
				</template>

				<template #append v-if="isProvinceError">
					<retry-btn @click.stop="getProvinces()" only-icon
						:disabled="isDisabled('provinceFetch')"></retry-btn>
				</template>
			</list-filter>

			<!-- Throttle Alert -->
			<throttle-alert :show="isDisabled('provinceFetch')" :time="throttle['provinceFetch'] || 0"></throttle-alert>
		</v-col>

		<!-- Ward -->
		<v-col cols="12" sm="4" class="mb-3">
			<list-filter v-model="ward_code" :items="wards" searchable
				:item-title="i18n.global.locale.value === 'vi' ? 'full_name' : 'full_name_en'" item-value="code"
				:rules="isWardError ? [] : employeeValidation.ward"
				:error-messages="isDisabled('wardFetch') ? '' : errorMessage.ward" :disabled="disableWard"
				:clearable="false" :loading="wardLoading" @click="getWards(province_code)">
				<template #label>
					<required-label :label="$t('employee.input.ward')"></required-label>
				</template>

				<template #append v-if="isWardError">
					<retry-btn @click.stop="getWards(province_code)" only-icon
						:disabled="isDisabled('wardFetch')"></retry-btn>
				</template>
			</list-filter>

			<!-- Throttle Alert -->
			<throttle-alert :show="isDisabled('wardFetch')" :time="throttle['wardFetch'] || 0"></throttle-alert>
		</v-col>

		<!-- Address -->
		<v-col cols="12" sm="4" class="mb-3">
			<Input v-model="address" :placeholder="$t('employee.placeholder.addressExample')"
				:maxlength="CONFIG.maxLengthAddress" counter :rules="employeeValidation.address">
				<template #label>
					<required-label :label="$t('employee.input.address')"></required-label>
				</template>
			</Input>
		</v-col>
	</v-row>
</template>
