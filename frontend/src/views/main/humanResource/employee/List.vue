<script setup lang="ts">
import { ref, computed } from 'vue';
import AppBreadcrumb from '@/components/layout/AppBreadcrumb.vue';

const search = ref('');
const 	itemsPerPage = ref(10);
const page = ref(1);

const filterDepartment = ref(null);
const filterPosition = ref(null);
const filterStatus = ref(null);

const departments = ['IT', 'Khách hàng cá nhân', 'Khách hàng doanh nghiệp', 'Marketing', 'Kế toán'];
const positions = ['Nhân viên', 'Trưởng phòng', 'Phó phòng', 'Giám đốc', 'Thực tập sinh'];
const statuses = ['Active', 'Inactive'];

const headers = [
	{ title: 'Nhân viên', key: 'name', align: 'start' },
	{ title: 'Phòng ban', key: 'department' },
	{ title: 'Chức vụ', key: 'position' },
	{ title: 'Trạng thái', key: 'status' }
];

// Tạo 150 nhân viên giả lập để có đủ 15 trang
const allEmployees = Array.from({ length: 150 }, (_, i) => ({
	id: i + 1,
	name: `Nguyễn Văn ${String.fromCharCode(65 + (i % 26))} ${i + 1}`,
	department: departments[i % 5],
	position: positions[i % 5],
	status: i % 4 === 0 ? 'Inactive' : 'Active'
}));

const employees = ref(allEmployees);

const filteredEmployees = computed(() => {
	return employees.value.filter(emp => {
		const matchName = emp.name.toLowerCase().includes(search.value.toLowerCase());
		const matchDept = !filterDepartment.value || emp.department === filterDepartment.value;
		const matchPos = !filterPosition.value || emp.position === filterPosition.value;
		const matchStatus = !filterStatus.value || emp.status === filterStatus.value;

		return matchName && matchDept && matchPos && matchStatus;
	});
});

const pageCount = computed(() => {
	return Math.ceil(filteredEmployees.value.length / itemsPerPage.value) || 1;
});
</script>

<template>
	<app-breadcrumb class="mb-2" />

	<div class="employee-list px-4 pb-4">

		<div class="d-flex justify-space-between align-center mb-4">
			<h2 class="text-h5 mb-0 font-weight-bold">List Employee</h2>
			<v-btn color="primary" class="text-none" height="40">
				<v-icon icon="mdi-plus" class="mr-sm-2"></v-icon>
				<span class="d-none d-sm-inline">Thêm nhân viên</span>
			</v-btn>
		</div>

		<v-card class="elevation-1 mb-4">
			<v-card-text>
				<v-row dense>
					<v-col cols="12" sm="6" md="3" lg="2">
						<v-text-field
							v-model="search"
							label="Tìm kiếm theo tên"
							prepend-inner-icon="mdi-magnify"
							variant="outlined"
							density="compact"
							hide-details
						></v-text-field>
					</v-col>

					<v-col cols="12" sm="6" md="3" lg="2">
						<v-select
							v-model="filterDepartment"
							:items="departments"
							label="Phòng ban"
							variant="outlined"
							density="compact"
							hide-details
							clearable
						></v-select>
					</v-col>

					<v-col cols="12" sm="6" md="3" lg="2">
						<v-select
							v-model="filterPosition"
							:items="positions"
							label="Chức vụ"
							variant="outlined"
							density="compact"
							hide-details
							clearable
						></v-select>
					</v-col>

					<v-col cols="12" sm="6" md="3" lg="2">
						<v-select
							v-model="filterStatus"
							:items="statuses"
							label="Trạng thái"
							variant="outlined"
							density="compact"
							hide-details
							clearable
						></v-select>
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
					<a href="#" class="text-primary font-weight-medium text-decoration-none text-hover-underline">
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
							<v-select
								class="d-none d-sm-block"
								v-model="itemsPerPage"
								:items="[5, 10, 15, 20, 50]"
								label="Số dòng/trang"
								variant="outlined"
								density="compact"
								hide-details
								max-width="200"
								min-width="200"
							></v-select>

						<v-pagination
							v-model="page"
							:length="pageCount"
							:total-visible="5"
							rounded="circle"
							density="comfortable"
						></v-pagination>
					</div>
				</template>
			</v-data-table>
		</v-card>
	</div>
</template>
