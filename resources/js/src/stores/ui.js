import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useUiStore = defineStore('ui', () => {
    const activeChild = ref(null)

    const isChildMode = computed(() => activeChild.value !== null)

    function switchToChild(child) {
        activeChild.value = { id: child.id, name: child.first_name }
    }

    function switchToParent() {
        activeChild.value = null
    }

    return { activeChild, isChildMode, switchToChild, switchToParent }
})
