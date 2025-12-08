import './bootstrap'
import '../css/app.css'
import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import { createPinia } from 'pinia'
import '@fortawesome/fontawesome-free/css/all.css'
import axios from 'axios'

const app = createApp(App)
app.use(router)
app.use(createPinia())

const token = localStorage.getItem('token')
if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
}

app.mount('#app')