import { computed } from 'vue'
import { t } from '@/plugins/vueI18n'
import type { suportedScopes } from '@/types/common'

interface TableHeader {
	title: string
	key: string
	align: 'start' | 'center' | 'end'
	headerProps: object
	cellProps?: object
	sortable?: boolean
}

interface permissionScopeHeaders {
	title: string
	key: suportedScopes
}

export const useTableModule = () => {
	// department table header
	const departmentHeaders = computed<TableHeader[]>(() => [
		{
			title: t('common.table.department.name'),
			key: 'name',
			align: 'start',
			headerProps: { class: 'font-weight-bold' },
		},
		{
			title: t('common.table.department.code'),
			key: 'code',
			align: 'center',
			headerProps: { class: 'font-weight-bold' },
		},
		{
			title: t('common.table.commonColumn.staffCount'),
			key: 'employees_count',
			align: 'center',
			headerProps: { class: 'font-weight-bold' },
		},
		{
			title: t('common.filter.status'),
			key: 'status',
			align: 'center',
			headerProps: { class: 'font-weight-bold' },
		},
		{
			title: t('common.table.department.leader'),
			key: 'leader',
			align: 'center',
			headerProps: { class: 'font-weight-bold' },
			sortable: false,
		},
		{
			title: t('common.table.department.parent'),
			key: 'parent',
			align: 'center',
			headerProps: { class: 'font-weight-bold' },
			sortable: false,
		},
		{
			title: t('common.table.commonColumn.createdBy'),
			key: 'created_by',
			align: 'start',
			headerProps: { class: 'font-weight-bold' },
			sortable: false,
		},
	])

	// position table header
	const positionHeaders = computed<TableHeader[]>(() => [
		{
			title: t('common.table.position.name'),
			key: 'name',
			align: 'start',
			headerProps: { class: 'font-weight-bold' },
		},
		{
			title: t('common.table.position.code'),
			key: 'code',
			align: 'center',
			headerProps: { class: 'font-weight-bold' },
		},
		{
			title: t('common.table.commonColumn.staffCount'),
			key: 'employees_count',
			align: 'center',
			headerProps: { class: 'font-weight-bold' },
		},
		{
			title: t('common.filter.status'),
			key: 'status',
			align: 'center',
			headerProps: { class: 'font-weight-bold' },
		},
		{
			title: t('common.table.position.parent'),
			key: 'parent_name',
			align: 'center',
			headerProps: { class: 'font-weight-bold' },
			sortable: false,
		},
		{
			title: t('common.table.position.departmentApply'),
			key: 'department_name',
			align: 'center',
			headerProps: { class: 'font-weight-bold' },
			sortable: false,
		},
		{
			title: t('common.table.commonColumn.createdBy'),
			key: 'created_by',
			align: 'center',
			headerProps: { class: 'font-weight-bold' },
			sortable: false,
		},
	])

	// role table header
	const roleHeaders = computed<TableHeader[]>(() => [
		{
			title: t('common.table.role.name'),
			key: 'name',
			align: 'start',
			headerProps: { class: 'font-weight-bold' },
		},
		{
			title: t('common.table.role.code'),
			key: 'code',
			align: 'start',
			headerProps: { class: 'font-weight-bold' },
		},
		{
			title: t('common.table.commonColumn.staffCount'),
			key: 'users_count',
			align: 'center',
			headerProps: { class: 'font-weight-bold' },
		},
		{
			title: t('common.filter.status'),
			key: 'status',
			align: 'center',
			headerProps: { class: 'font-weight-bold' },
		},
		{
			title: t('common.table.commonColumn.createdBy'),
			key: 'created_by',
			align: 'start',
			headerProps: { class: 'font-weight-bold' },
			sortable: false,
		},
	])

	const employeeHeaders = computed<TableHeader[]>(() => [
		{
			title: t('common.table.employee.name'),
			key: 'name',
			align: 'start',
			headerProps: { class: 'font-weight-bold' },
		},
		{
			title: t('common.table.employee.code'),
			key: 'code',
			align: 'start',
			headerProps: { class: 'font-weight-bold' },
		},
		{
			title: t('common.table.employee.email'),
			key: 'email',
			align: 'start',
			headerProps: { class: 'font-weight-bold' },
		},
		{
			title: t('common.filter.position'),
			key: 'position_name',
			align: 'start',
			headerProps: { class: 'font-weight-bold' },
			sortable: false,
		},
		{
			title: t('common.filter.status'),
			key: 'status',
			align: 'center',
			headerProps: { class: 'font-weight-bold' },
		},
		{
			title: t('common.filter.department'),
			key: 'department_name',
			align: 'start',
			headerProps: { class: 'font-weight-bold' },
			sortable: false,
		},
		{
			title: t('common.table.commonColumn.createdBy'),
			key: 'created_by',
			align: 'start',
			headerProps: { class: 'font-weight-bold' },
			sortable: false,
		},
	])

	//permission scope table header
	const permissionScopeHeaders = computed<permissionScopeHeaders[]>(() => [
		{ title: t('common.permission.scope.all'), key: 'ALL' },
		{ title: t('common.permission.scope.dept'), key: 'DEPT' },
		{ title: t('common.permission.scope.own'), key: 'OWN' },
		{ title: t('common.permission.scope.none'), key: 'NONE' },
	])

	return {
		departmentHeaders,
		positionHeaders,
		roleHeaders,
		permissionScopeHeaders,
		employeeHeaders,
	}
}

