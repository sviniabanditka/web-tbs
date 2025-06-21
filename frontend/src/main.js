import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import ReverbPlugin from './plugins/reverb'
import './plugins/axios'
import router from './router'
import { useAuthStore } from './stores/auth'

const app = createApp(App)
app.use(createPinia())
app.use(router)
app.use(ReverbPlugin)
app.mount('#app')

const authStore = useAuthStore()
authStore.initialize()