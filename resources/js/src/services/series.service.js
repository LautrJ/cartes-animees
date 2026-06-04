import api from './axios'

export const seriesService = {
    getByChild: (childId) => api.get(`/children/${childId}/series`)
}
