import api from './axios.js'

export const profileService = {
    get: () => api.get('/profile'),
    update: (data) => api.put('/profile', data),
    updatePassword: (data) => api.patch('/profile/password', data),
}
