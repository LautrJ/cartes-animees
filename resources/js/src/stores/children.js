import { defineStore } from 'pinia'
import { ref } from 'vue'
import { childrenService } from '@/src/services/children.service'

export const useChildrenStore = defineStore('children', () => {
    const children = ref([])
    const loading = ref(false)

    async function fetchChildren() {
        loading.value = true
        try {
            const { data } = await childrenService.getAll()
            children.value = data
        } finally {
            loading.value = false
        }
    }

    return { children, loading, fetchChildren }
})
