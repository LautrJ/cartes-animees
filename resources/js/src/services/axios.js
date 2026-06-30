import axios from 'axios'
import { useAuthStore } from '@/src/stores/auth'

const api = axios.create({
    baseURL: '/api',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
})

api.interceptors.request.use((config) => {
    const authStore = useAuthStore()
    if (authStore.token) {
        config.headers.Authorization = `Bearer ${authStore.token}`
    }
    return config
})

api.interceptors.response.use(
    (response) => {
        localStorage.setItem('last_activity', Date.now().toString())
        return response
    },
    (error) => {
        if (error.response?.status === 401) {
            const authStore = useAuthStore()
            authStore.clearAuth()
            window.location.href = '/login'
        }
        return Promise.reject(error)
    }
)

export default api
