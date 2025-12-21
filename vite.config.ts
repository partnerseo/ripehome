import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import path from 'path'

export default defineConfig({
  plugins: [react()],
  base: '/',
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
    },
  },
  publicDir: 'public',
  build: {
    outDir: 'dist',
    assetsDir: 'assets',
    // ÜRÜNLER klasörlerini build'e dahil etme
    rollupOptions: {
      external: [],
      output: {
        manualChunks: undefined,
      }
    },
    // Sadece gerekli dosyaları kopyala
    copyPublicDir: true,
  },
})
