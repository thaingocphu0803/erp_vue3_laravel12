import api from '@/services/api'
import { defineStore } from 'pinia'
import { ref } from 'vue'

interface Unit {
	code: string
	full_name: string
	full_name_en: string
}

export const useAdministrativeUnitStore = defineStore('administrativeUnit', () => {
	const provinces = ref<Unit[]>([])
	const wards = ref<Unit[]>([])

	const isProvincesFetched = ref<boolean>(false)
	const isWardsFetched = ref<boolean>(false)

	const provincesFetch = async () => {
		if (isProvincesFetched.value) return

		const response = await api.get('/administrative-units/provinces')
		provinces.value = response.data.data
		isProvincesFetched.value = true

		return response
	}

	const wardsFetchByProvinceCode = async (provinceCode: string) => {
		if (isWardsFetched.value) return

		const response = await api.get(`/administrative-units/provinces/${provinceCode}/wards`)
		wards.value = response.data.data
		isWardsFetched.value = true

		return response
	}

	const wardsReset = () => {
		wards.value = []
		isWardsFetched.value = false
	}

	return {
		provinces,
		wards,
		provincesFetch,
		wardsFetchByProvinceCode,
		wardsReset,
	}
})

