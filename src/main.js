import { createApp } from 'vue'
import App from './App.vue'
import { createPinia } from 'pinia'
import router from './router'
import './axios' // <-- Add this line
import piniaPersistedstate from 'pinia-plugin-persistedstate'

const app = createApp(App)
const pinia = createPinia()
pinia.use(piniaPersistedstate) // <-- Add persisted state plugin to persist Pinia state
app.use(pinia)
app.use(router)
app.mount('#app')
