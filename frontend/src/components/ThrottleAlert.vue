<script setup lang="ts">
import { storeToRefs } from 'pinia'
import { useThrottleStore } from '@/stores/throttle'

interface Props {
    show: boolean
    time: number
}

const props = defineProps<Props>()

const { throttle, isDisabled } = storeToRefs(useThrottleStore())
</script>

<template>
    <div v-if="props.show" class="text-red-lighten-2 mt-5">
        {{ $t('common.throttle.tooManyRequests', { time: props.time }) }}
    </div>
</template>