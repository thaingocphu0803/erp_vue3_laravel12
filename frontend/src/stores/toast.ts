import { defineStore } from 'pinia'
import { ref } from 'vue'

export type ToastType = 'success' | 'error' | 'info' | 'warning'

export const useToastStore = defineStore('toast', () => {
	const visible = ref<boolean>(false)
	const message = ref<string>('')
	const type = ref<ToastType>('success')
	const duration = ref<number>(3000)

	const show = (msg: string, t: ToastType = 'success', ms: number = 3000) => {
		message.value = msg
		type.value = t
		duration.value = ms
		visible.value = true
	}

	const hide = () => {
		visible.value = false
	}

	return { visible, message, type, duration, show, hide }
})
