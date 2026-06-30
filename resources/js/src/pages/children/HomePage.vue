<script setup>
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRoute, useRouter} from 'vue-router'
import { useAuthStore } from '@/src/stores/auth'
import DefaultLayout from "@/src/layouts/DefaultLayout.vue"
import HomeChildCard from "@/src/components/children/HomeChildCard.vue";
import {childrenService} from "@/src/services/children.service.js";
import {useUiStore} from "@/src/stores/ui.js";

const { t } = useI18n()
const router = useRouter()
const authStore = useAuthStore()
const uiStore = useUiStore()

const route = useRoute()

const children = ref([])
const loading = ref(true)

const urgentChildren = computed(() => {
    return children.value.filter(c => c.subscription_status === 'past_due')
})

onMounted(async () => {
    try {
        const { data } = await childrenService.getAll()
        children.value = data
    } finally {
        loading.value = false
    }
})
</script>

<template>
    <DefaultLayout>
        <div v-if="!uiStore.isChildMode">
            <div class="text-center">
                <h2 class="font-dynapuff font-bold text-2xl text-theme">
                    Bonjour {{ authStore.user?.first_name }} !
                </h2>
            </div>

            <div class="p-6">
                <p v-if="loading">{{ t('common.loading') }}</p>

                <p v-else-if="children.length === 0">
                    {{ t('pages.children.empty') }}
                </p>

                <div v-else class="flex gap-4 overflow-x-auto py-4">
                    <RouterLink
                        v-for="child in children"
                        :key="child.id"
                        :to="{ name: 'child-detail', params: {id: child.id} }"
                    >
                        <HomeChildCard :child="child"/>
                    </RouterLink>
                </div>
            </div>
        </div>

    </DefaultLayout>
</template>
