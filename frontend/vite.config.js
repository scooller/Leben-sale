import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

// https://vite.dev/config/
export default defineConfig({
  envPrefix: ['VITE_'],
  plugins: [
    react({
      babel: {
        plugins: [['babel-plugin-react-compiler']],
      },
    }),
  ],
  build: {
    rollupOptions: {
      output: {
        manualChunks: {
          // Separar librerías pesadas en chunks propios
          'vendor-web-awesome': ['@web.awesome.me/webawesome-pro'],
          'vendor-gsap': ['gsap'],
          'vendor-fancybox': ['@fancyapps/ui'],
          'vendor-react': ['react', 'react-dom'],
          // Separar axios en su propio chunk
          'vendor-axios': ['axios'],
        },
      },
    },
    // Aumentar el límite para CSS (el theme es legítimamente grande)
    chunkSizeWarningLimit: 750,
  },
})
