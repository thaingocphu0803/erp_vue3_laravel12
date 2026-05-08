import { departmentService } from '@/services/departmentService'
import { defineStore } from 'pinia'
import { ref } from 'vue'
import type { Department, DepartmentItem } from '@/types/department'
import type { commonStatus } from '@/types/common'

export const useDepartmentStore = defineStore('deparment', () => {
	const isFetched = ref<boolean>(false)

	const isFetchPaginate = ref<boolean>(false)

	const departments = ref<Department[]>([])

	const departmentIndex = ref<DepartmentItem[]>([])

	const departmentsPaginate = async (payload: object) => {
		const response = await departmentService.getPaginate(payload)
		departmentIndex.value = response.data.data
		isFetchPaginate.value = true
		return response
	}

	const departmentsFetch = async () => {
		if (isFetched.value) return

		const response = await departmentService.getList()
		departments.value = response.data.data
		isFetched.value = true
	}

	const departmentCreate = async (payload: object) => {
		const response = await departmentService.create(payload)
		isFetched.value = false
		isFetchPaginate.value = false
		return response
	}

	const departmentDelete = async (id: number) => {
		const response = await departmentService.delete(id)
		isFetched.value = false
		isFetchPaginate.value = false
		return response
	}

	const departmentBulkDelete = async (ids: number[]) => {
		const payload = { ids }
		const response = await departmentService.bulkDelete(payload)
		isFetched.value = false
		isFetchPaginate.value = false
		return response
	}

	const departmentBulkUpdateStatus = async (ids: number[], status: commonStatus) => {
		const payload = { ids, status }
		const response = await departmentService.bulkUpdateStatus(payload)
		isFetched.value = false
		isFetchPaginate.value = false
		return response
	}

	return {
		departments,
		departmentIndex,
		departmentsPaginate,
		departmentsFetch,
		departmentCreate,
		departmentDelete,
		departmentBulkDelete,
		departmentBulkUpdateStatus,
	}
})
