import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useUiStore = defineStore('ui', () => {
    const stored = sessionStorage.getItem('activeChild')
    const activeChild = ref(stored ? JSON.parse(stored) : null)

    const isChildMode = computed(() => activeChild.value !== null)

    function switchToChild(child) {
        activeChild.value = { id: child.id, name: child.first_name }
        sessionStorage.setItem('activeChild', JSON.stringify(activeChild.value))
    }

    function switchToParent() {
        activeChild.value = null
        sessionStorage.removeItem('activeChild')
    }

    return { activeChild, isChildMode, switchToChild, switchToParent }
})
