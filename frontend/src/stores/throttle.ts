import { defineStore } from 'pinia'
import { computed, reactive, ref } from 'vue'

type Interval = ReturnType<typeof setInterval> | null

export const useThrottleStore = defineStore(
	'throttle',
	() => {
		let interval: Record<string, Interval> = {}

		const throttle = ref<Record<string, number | undefined>>({})

		const isDisabled = computed(() => (key: string): boolean => {
			return (throttle.value[key] || 0) > 0
		})

		const initThrottle = (key: string) => {
			if ((throttle.value[key] || 0) > 0) {
				startThrottle(key)
			}
		}

		const startThrottle = (key: string) => {
			stopThrottle(key)

			interval[key] = setInterval(() => {
				const current = throttle.value[key] || 0
				if (current > 0) {
					throttle.value[key] = current - 1
				} else {
					stopThrottle(key)
				}
			}, 1000)
		}

		const stopThrottle = (key: string) => {
			if (interval[key]) {
				clearInterval(interval[key] as ReturnType<typeof setInterval>)
				interval[key] = null
			}
		}

		return { throttle, isDisabled, startThrottle, initThrottle }
	},
	{
		persist: {
			storage: localStorage,
			key: 'throttle',
			pick: ['throttle'],
		},
	},
)

