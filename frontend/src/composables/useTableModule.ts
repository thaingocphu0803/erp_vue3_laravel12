import { computed } from 'vue'
import { t } from '@/plugins/vueI18n'
import type { suportedScopes } from '@/stores/permission'

interface TableHeader {
	title: string
	key: string
	align: 'start' | 'center' | 'end'
	headerProps: object
}

interface permissionScopeHeaders {
	title: string
	key: suportedScopes
}


export const useTableModule = () => {

	const departmentHeaders = computed<TableHeader[]>(() => [
		{ title: t('common.table.department.name'), key: 'name', align: 'start', headerProps: { class: 'font-weight-bold' } },
		{ title: t('common.table.department.code'), key: 'code', align: 'center', headerProps: { class: 'font-weight-bold' } },
		{title: t('common.table.department.numberEmployees'), key: 'numberEmployees', align: 'center', headerProps: { class: 'font-weight-bold' }},
		{ title: t('common.filter.status'), key: 'status', align: 'center',headerProps: { class: 'font-weight-bold' } },
	])

	const positionHeaders = computed<TableHeader[]>(() => [
		{ title: t('common.table.position.name'), key: 'name', align: 'start', headerProps: { class: 'font-weight-bold' } },
		{ title: t('common.table.department.name'), key: 'departmentName', align: 'center', headerProps: { class: 'font-weight-bold' } },
		{title: t('common.table.department.numberEmployees'), key: 'numberEmployees', align: 'center', headerProps: { class: 'font-weight-bold' }},
		{ title: t('common.filter.status'), key: 'status', align: 'center',headerProps: { class: 'font-weight-bold' } },
	])

	const permissionScopeHeaders = computed<permissionScopeHeaders[]>(() => [
		{ title: t('common.permission.scope.all'), key: 'ALL' },
		{ title: t('common.permission.scope.dept'), key: 'DEPT' },
		{ title: t('common.permission.scope.own'), key: 'OWN' },
		{ title: t('common.permission.scope.none'), key: 'NONE' },
	])

	return { departmentHeaders, positionHeaders, permissionScopeHeaders }
}
