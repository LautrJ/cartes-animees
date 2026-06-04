import api from './axios'

export const childrenService = {
    getAll: () => api.get('/children'),
    get: (id) => api.get(`/children/${id}`),
    create: (data) => api.post('/children', data),
    update: (id, data) => api.put(`/children/${id}`, data),
    delete: (id) => api.delete(`/children/${id}`),
    affiliateTherapist: (childId, code) =>
        api.post(`/children/${childId}/therapist`, { invitation_code: code }),
    removeTherapist: (childId, therapistId) =>
        api.delete(`children/${childId}/therapist/${therapistId}`),
}
