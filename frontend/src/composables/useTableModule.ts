import { computed } from 'vue'
import { t } from '@/plugins/vueI18n'

interface TableHeader {
	title: string
	key: string
	align: 'start' | 'center' | 'end'
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

	return { departmentHeaders, positionHeaders }
}
