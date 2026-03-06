<script lang="ts" setup>
import { useFilterModule } from '@/composables/useFilterModule'

const { statusMap } = useFilterModule()

interface Props {
	val: 'A' | 'X'
}

const props = defineProps<Props>()
</script>

<template>
	<v-chip
		:color="statusMap[val]?.color"
		size="small"
		variant="flat"
		class="text-uppercase font-weight-bold"
	>
		{{ statusMap[val]?.name  }}
	</v-chip>
</template>
