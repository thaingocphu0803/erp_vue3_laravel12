<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AppBreadcrumb from '@/components/layout/AppBreadcrumb.vue'
import ListHeader from '@/components/list/ListHeader.vue'
import ListSearch from '@/components/list/ListSearch.vue'
import ListFilter from '@/components/list/ListFilter.vue'
import defaultConfig from '@/config/default'
import {t} from '@/plugins/vueI18n'

const search = ref('')
const itemsPerPage = ref(10)
const page = ref(1)

const filterDepartment = ref(null)
const filterPosition = ref(null)
const filterStatus = ref(null)

const departments = ['IT', 'Khách hàng cá nhân', 'Khách hàng doanh nghiệp', 'Marketing', 'Kế toán']
const positions = ['Nhân viên', 'Trưởng phòng', 'Phó phòng', 'Giám đốc', 'Thực tập sinh']
const statuses = ['Active', 'Inactive']

const headers:any = computed(()=> [
	{ title: t('common.table.department.name'), key: 'name', align: 'start' },
	{ title: t('common.table.department.code'), key: 'code', align: 'center' },
	{ title: t('common.table.department.numberEmployees'), key: 'numberEmployees',  align: 'center' },
	{ title: t('common.filter.status'), key: 'status',  align: 'center' },
])

</script>

<template>
	<app-breadcrumb class="mb-2" />

	<v-container fluid class="employee-list">
		<list-header title="common.header.listEmployee">
			<template v-slot:prepend>
				<v-btn color="primary" class="text-none" :to="{name: 'hr.department.create'}">
					<v-icon icon="mdi-plus"></v-icon>
					<span class="d-none d-sm-inline ml-sm-2">{{ $t('common.button.addDepartment') }}</span>
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
							searchable
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
			<v-data-table-server
				v-model:page="page"
				:headers="headers"
				:items="[]"
				:items-per-page="itemsPerPage"
				item-value="id"
				:items-length="itemsPerPage"
			>
				<template v-slot:item.name="{ item }">
					<a
						href="#"
						class="text-primary font-weight-medium text-decoration-none text-hover-underline"
					>
						{{ item }}
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
							:length="1"
							:total-visible="5"
							rounded="shape"
							density="comfortable"
						></v-pagination>
					</div>
				</template>
			</v-data-table-server>
		</v-card>
	</v-container>
</template>
