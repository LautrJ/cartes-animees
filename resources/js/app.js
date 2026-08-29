import './bootstrap'
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createI18n } from 'vue-i18n'

import App from './src/App.vue'
import router from './src/router/index'
import fr from './src/locales/fr.json'
import en from './src/locales/en.json'
import { useAuthStore } from './src/stores/auth'

const i18n = createI18n({
    legacy: false,
    locale: 'fr',
    fallbackLocale: 'en',
    messages: { fr, en },
})

const pinia = createPinia()
const app = createApp(App)

app.use(pinia)
app.use(router)
app.use(i18n)

const authStore = useAuthStore()
authStore.init().finally(() => {
    app.mount('#app')
})
