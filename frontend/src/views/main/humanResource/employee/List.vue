<script setup lang="ts">
import { ref, computed } from 'vue'
import AppBreadcrumb from '@/components/layout/AppBreadcrumb.vue'
import ListHeader from '@/components/list/ListHeader.vue'
import ListSearch from '@/components/list/ListSearch.vue'
import ListFilter from '@/components/list/ListFilter.vue'
import defaultConfig from '@/config/default'

const search = ref('')
const itemsPerPage = ref(10)
const page = ref(1)

const filterDepartment = ref(null)
const filterPosition = ref(null)
const filterStatus = ref(null)

const departments = ['IT', 'Khách hàng cá nhân', 'Khách hàng doanh nghiệp', 'Marketing', 'Kế toán']
const positions = ['Nhân viên', 'Trưởng phòng', 'Phó phòng', 'Giám đốc', 'Thực tập sinh']
const statuses = ['Active', 'Inactive']

const headers = [
	{ title: 'Tên', key: 'name', align: 'start' },
	{ title: 'Mã Nhân viên', key: 'code', align: 'start' },
	{ title: 'Phòng ban', key: 'department' },
	{ title: 'Chức vụ', key: 'position' },
	{ title: 'Trạng thái', key: 'status' },
]

// Tạo 150 nhân viên giả lập để có đủ 15 trang
const allEmployees = Array.from({ length: 150 }, (_, i) => ({
	id: i + 1,
	name: `Nguyễn Văn ${String.fromCharCode(65 + (i % 26))} ${i + 1}`,
	code: `${String.fromCharCode(65 + (i % 26))} ${i + 1}`,
	department: departments[i % 5],
	position: positions[i % 5],
	status: i % 4 === 0 ? 'Inactive' : 'Active',
}))

const employees = ref(allEmployees)

const filteredEmployees = computed(() => {
	return employees.value.filter((emp) => {
		const matchName = emp.name.toLowerCase().includes(search.value.toLowerCase())
		const matchDept = !filterDepartment.value || emp.department === filterDepartment.value
		const matchPos = !filterPosition.value || emp.position === filterPosition.value
		const matchStatus = !filterStatus.value || emp.status === filterStatus.value

		return matchName && matchDept && matchPos && matchStatus
	})
})

const pageCount = computed(() => {
	return Math.ceil(filteredEmployees.value.length / itemsPerPage.value) || 1
})
</script>

<template>
	<app-breadcrumb class="mb-2" />

	<v-container :fluid="true" class="employee-list">
		<list-header title="common.header.listEmployee">
			<template v-slot:prepend>
				<v-btn color="primary" class="text-none">
					<v-icon icon="mdi-plus"></v-icon>
					<span class="d-none d-sm-inline ml-sm-2">{{ $t('common.button.addEmployee') }}</span>
				</v-btn>
			</template>
		</list-header>

		<v-card class="elevation-1 mb-4">
			<v-card-text>
				<v-row dense>
					<v-col cols="12" sm="6" md="3" lg="3">
						<list-search
							v-model="search"
							:label="$t('common.filter.employeeNameOrCode')">
						</list-search>
					</v-col>

					<v-col cols="12" sm="6" md="3" lg="2">
						<list-filter
							v-model="filterDepartment"
							:items="departments"
							:label="$t('common.filter.department')"
						></list-filter>
					</v-col>

					<v-col cols="12" sm="6" md="3" lg="2">
						<list-filter
							v-model="filterPosition"
							:items="positions"
							:label="$t('common.filter.position')"
						></list-filter>
					</v-col>

					<v-col cols="12" sm="6" md="3" lg="2">
						<list-filter
							v-model="filterStatus"
							:items="statuses"
							:label="$t('common.filter.status')"
						></list-filter>
					</v-col>
				</v-row>
			</v-card-text>
		</v-card>

		<v-card class="elevation-1">
			<v-data-table
				v-model:page="page"
				:headers="headers"
				:items="filteredEmployees"
				:items-per-page="itemsPerPage"
				item-value="id"
			>
				<template v-slot:item.name="{ item }">
					<a
						href="#"
						class="text-primary font-weight-medium text-decoration-none text-hover-underline"
					>
						{{ item.name }}
					</a>
				</template>

				<template v-slot:item.status="{ value }">
					<v-chip
						:color="value === 'Active' ? 'success' : 'error'"
						size="small"
						variant="flat"
						class="text-uppercase font-weight-bold"
					>
						{{ value }}
					</v-chip>
				</template>

				<template v-slot:bottom>
					<v-divider></v-divider>
					<div class="d-flex justify-center justify-sm-space-between align-center pa-4">
						<list-filter
							class="d-none d-sm-block"
							v-model="itemsPerPage"
							:items="defaultConfig.perPage"
							:label="$t('common.filter.itemPerPage')"
							max-width="200"
							min-width="200"
						></list-filter>

						<v-pagination
							v-model="page"
							:length="pageCount"
							:total-visible="5"
							rounded="shape"
							density="comfortable"
						></v-pagination>
					</div>
				</template>
			</v-data-table>
		</v-card>
	</v-container>
</template>
