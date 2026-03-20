import { t } from "@/plugins/vueI18n"
import type { commonStatus } from "@/types/common"
import { computed } from "vue"

interface Status {
	name: string,
	id: commonStatus,
	color: 'success' | 'error'
}

export const useFilterModule = () => {
		const statuses = computed((): Status[] => [
			{ name: t('common.status.active'), id: 'A', color: 'success'},
			{ name: t('common.status.inActive'), id: 'X', color: 'error' }
		])

	const statusMap = computed((): Record<string, Status> =>
        Object.fromEntries(statuses.value.map(s => [s.id, s]))
    )

	return {statuses, statusMap}
}
