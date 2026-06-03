import api from './axios'

export const authService = {
    login: (email, password) => api.post('/auth/login', { email, password }),
    register: (data) => api.post('/auth/register', data),
    logout: () => api.post('/auth/logout'),
    me: () => api.get('/auth/me'),
    forgotPassword: (email) => api.post('/auth/password/forgot', { email }),
    resetPassword: (data) => api.post('/auth/password/reset', data),
}
