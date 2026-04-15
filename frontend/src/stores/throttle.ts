import { defineStore } from 'pinia'
import { computed, ref } from 'vue'

type Interval = ReturnType<typeof setInterval> | null

export const useThrottleStore = defineStore(
	'throttle',
	() => {
		let interval: Interval = null

		const throttle = ref<number>(0)

		const isDisabled = computed<boolean>(() => throttle.value > 0)

		const initThrottle = () => {
			if (throttle.value > 0) {
				startThrottle()
			}
		}

		const startThrottle = () => {
			stopThrottle()

			interval = setInterval(() => {
				if (throttle.value > 0) {
					throttle.value--
				} else {
					stopThrottle()
				}
			}, 1000)
		}

		const stopThrottle = () => {
			if (interval) {
				clearInterval(interval)
				interval = null
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
