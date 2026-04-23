import api from './api'

/**
 * Service handling Administrative Unit API calls (Provinces, Wards)
 */
export const administrativeUnitService = {
	/**
	 * Fetch list of all provinces
	 */
	getProvinces: () => api.get('/administrative-units/provinces'),

	/**
	 * Fetch list of wards for a specific province
	 * @param provinceCode - Code of the province
	 */
	getWards: (provinceCode: string) =>
		api.get(`/administrative-units/provinces/${provinceCode}/wards`),
}

