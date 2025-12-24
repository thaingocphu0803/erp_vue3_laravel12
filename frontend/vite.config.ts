import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'
import path from 'node:path'

// https://vite.dev/config/
export default defineConfig({
  plugins: [vue(), vueDevTools()],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url)),
    },
  },
  server: {
    host: 'reze.crm.local',
    port:5173,
    strictPort: true,
    proxy: {
      '/api': {
        target: 'http://reze.crm.local',
			changeOrigin: true,
      },
      '/api/token': {
        target: 'http://reze.crm.local',
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/api\/token/,'\/sanctum\/csrf-cookie')
      }
    },
  },
})
