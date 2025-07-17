import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'

// https://vite.dev/config/
export default defineConfig({
  plugins: [vue()],
  server: {
    port: 5174,
    host: true,
    allowedHosts: 'all', // ⚠️ allow all external hosts
  },
  build: {
    sourcemap: true,
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'src'),
      '@pages': path.resolve(__dirname, 'src/pages'),
      '@components': path.resolve(__dirname, 'src/components'),
      '@chat': path.resolve(__dirname, 'src/components/chatComponents'),
      '@follow': path.resolve(__dirname, 'src/components/followComponents'),
      '@layouts': path.resolve(__dirname, 'src/layouts'),
      '@services': path.resolve(__dirname, 'src/services'),
      '@router': path.resolve(__dirname, 'src/router'),
      '@stores': path.resolve(__dirname, 'src/stores'),
      '@assets': path.resolve(__dirname, 'src/assets'),
      '@utils': path.resolve(__dirname, 'src/utils'),
      '@auth': path.resolve(__dirname, 'src/stores/auth')
    }
  }
})
