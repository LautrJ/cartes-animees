import api from './axios'

export const seriesService = {
    getByChild: (childId) => api.get(`/children/${childId}/series`),
    getDetail: (childId, seriesId) => api.get(`/children/${childId}/series/${seriesId}`),
}
