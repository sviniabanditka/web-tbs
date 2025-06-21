import './assets/main.css'

import { createApp } from 'vue'
import App from './App.vue'
import ReverbPlugin from './plugins/reverb'

const app = createApp(App)
app.use(ReverbPlugin)
app.mount('#app')
