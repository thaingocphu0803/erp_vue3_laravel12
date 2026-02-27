import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'

// https://vite.dev/config/
export default defineConfig({
	plugins: [vue(), vueDevTools()],
	resolve: {
		alias: {
			'@': fileURLToPath(new URL('./src', import.meta.url)),
		},
	},
	server: {
		host: 'reze.erp.local',
		port: 5173,
		open: 'login',
		strictPort: true,
		proxy: {
			'/api/token': {
				target: 'http://reze.erp.local',
				changeOrigin: true,
				rewrite: (path) => path.replace(/^\/api\/token/, '\/sanctum\/csrf-cookie'),
			},
			'/api': {
				target: 'http://reze.erp.local',
				changeOrigin: true,
			},
		},
	},
})

