import api from './axios'

export const childrenService = {
    getAll: () => api.get('/children'),
    get: (id) => api.get(`/children/${id}`),
    create: (data) => api.post('/children', data),
    update: (id, data) => api.put(`/children/${id}`, data),
    delete: (id) => api.delete(`/children/${id}`),
}
