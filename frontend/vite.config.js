import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'
import path from 'path'

// https://vite.dev/config/
export default defineConfig({
  plugins: [
    vue(),
    vueDevTools(),
  ],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
      'vue-router': path.resolve(__dirname, 'node_modules/vue-router')
    },
  },
  server: {
    host: '0.0.0.0',
    port: 5173,
    watch: {
      usePolling: true
    }
  }
})
