import api from './axios.js'

export const subscriptionService = {
    currentPrice: () => api.get('/subscription/price'),
    create: (childId, data) => api.post(`/children/${childId}/subscription`, data),
    cancel: (childId) => api.delete(`/children/${childId}/subscription`),
}
