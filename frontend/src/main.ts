import { createApp } from "vue";
import App from "./App.vue";
import { createVuestic, createIconsConfig } from 'vuestic-ui'
import './style.css'
import 'vuestic-ui/css'
import router from './router'
import { createPinia } from 'pinia'
import './plugins/axios' // Import axios plugin

const pinia = createPinia()

createApp(App)
  .use(pinia)
  .use(createVuestic({
    config: {
      colors: {
        variables: {
          // Tokyo Night inspired palette
          primary: '#7aa2f7', // Blue
          secondary: '#bb9af7', // Purple
          success: '#9ece6a', // Green
          info: '#7dcfff', // Light Blue
          warning: '#e0af68', // Orange
          danger: '#f7768e', // Red
          background: '#1a1b26', // Dark background
          background2: '#24283b', // Slightly lighter background
          background3: '#414868', // Even lighter background / borders
          onSurface: '#c0caf5', // Text color
          surface: '#1a1b26', // Card/surface background
        },
        presets: {
          light: {
            primary: '#42A5F5',
            secondary: '#AB47BC',
            success: '#66BB6A',
            info: '#26C6DA',
            warning: '#FFCA28',
            danger: '#EF5350',
            background: '#f5f5f5',
            background2: '#ffffff',
            background3: '#e0e0e0',
            onSurface: '#212121',
            surface: '#ffffff',
          },
          dark: {
            primary: '#7aa2f7', // Blue
            secondary: '#bb9af7', // Purple
            success: '#9ece6a', // Green
            info: '#7dcfff', // Light Blue
            warning: '#e0af68', // Orange
            danger: '#f7768e', // Red
            background: '#1a1b26', // Dark background
            background2: '#24283b', // Slightly lighter background
            background3: '#414868', // Even lighter background / borders
            onSurface: '#c0caf5', // Text color
            surface: '#1a1b26', // Card/surface background
          },
        },
      },
    },
  }))
  .use(router)
  .mount("#app");
