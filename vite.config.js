import { defineConfig } from 'vite'; 
import laravel from 'laravel-vite-plugin'
import { fileURLToPath } from 'node:url'
import vue from '@vitejs/plugin-vue'
import vueJsx from '@vitejs/plugin-vue-jsx'
import AutoImport from 'unplugin-auto-import/vite'
import Components from 'unplugin-vue-components/vite'
import vuetify from 'vite-plugin-vuetify'
import svgLoader from 'vite-svg-loader'

export default defineConfig({
  plugins: [
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      },
    }),
    vueJsx(),
    laravel({
      input: ['resources/js/main.js'],
      refresh: true,
    }),
    vuetify({
      styles: {
        configFile: 'resources/styles/variables/_vuetify.scss',
      },
    }),
    Components({
      dirs: ['resources/js/@core/components', 'resources/js/components'],
      dts: true,
      resolvers: [
        componentName => {
          if (componentName === 'VueApexCharts')
            return { name: 'default', from: 'vue3-apexcharts', as: 'VueApexCharts' }
        },
      ],
    }),
    AutoImport({
      imports: ['vue', 'vue-router', '@vueuse/core', '@vueuse/math', 'pinia'],
      vueTemplate: true,
      eslintrc: {
        enabled: true,
        filepath: './.eslintrc-auto-import.json',
      },
    }),
    svgLoader(),
  ],
  resolve: {
    alias: {
      '@core-scss': fileURLToPath(new URL('./resources/styles/@core', import.meta.url)),
      '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
      '@core': fileURLToPath(new URL('./resources/js/@core', import.meta.url)),
      '@layouts': fileURLToPath(new URL('./resources/js/@layouts', import.meta.url)),
      '@images': fileURLToPath(new URL('./resources/images/', import.meta.url)),
      '@styles': fileURLToPath(new URL('./resources/styles/', import.meta.url)),
      '@configured-variables': fileURLToPath(new URL('./resources/styles/variables/_template.scss', import.meta.url)),
    },
  },
  build: {
    chunkSizeWarningLimit: 5000,
  },
  optimizeDeps: {
    exclude: ['vuetify'],
    entries: [
      './resources/js/**/*.vue',
    ],
  },
  // Configuration to run Vite on the network (IP Wi-Fi)
  // server: {
  //   host: '192.168.0.105',  // Your Wi-Fi IP
  //   port: 5173,             // You can use any port, default for Vite is 5173
  //   hmr: {
  //     host: '192.168.0.105', // Ensure Hot Module Replacement also works on the network
  //   },
  // },
})
