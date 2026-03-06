import { computed } from 'vue'
import { t } from '@/plugins/vueI18n'

interface TableHeader {
	title: string
	key: string
	align: 'start' | 'center' | 'end'
}


export const useTableModule = () => {

	const departmentHeaders = computed<TableHeader[]>(() => [
		{ title: t('common.table.department.name'), key: 'name', align: 'start' },
		{ title: t('common.table.department.code'), key: 'code', align: 'center' },
		{
			title: t('common.table.department.numberEmployees'),
			key: 'numberEmployees',
			align: 'center',
		},
		{ title: t('common.filter.status'), key: 'status', align: 'center' },
	])

	return { departmentHeaders }
}
