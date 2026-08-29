<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRoute, useRouter} from 'vue-router'
import { useAuthStore } from '@/src/stores/auth'
import DefaultLayout from "@/src/layouts/DefaultLayout.vue"
import HomeChildCard from "@/src/components/children/HomeChildCard.vue";
import SeriesCard from "@/src/components/children/SeriesCard.vue";
import {childrenService} from "@/src/services/children.service.js";
import {seriesService} from "@/src/services/series.service.js";
import {useUiStore} from "@/src/stores/ui.js";

const { t } = useI18n()
const router = useRouter()
const authStore = useAuthStore()
const uiStore = useUiStore()

const route = useRoute()

const children = ref([])
const childSeries = ref([])
const loading = ref(true)
const seriesLoading = ref(false)

const urgentChildren = computed(() => {
    return children.value.filter(c => c.subscription_status === 'past_due')
})

const sortedSeries = computed(() => [
    ...childSeries.value.filter(s => s.status !== 'completed'),
    ...childSeries.value.filter(s => s.status === 'completed'),
])

onMounted(async () => {
    try {
        const { data } = await childrenService.getAll()
        children.value = data
    } finally {
        loading.value = false
    }
})

watch(
    () => uiStore.activeChild,
    async (child) => {
        if (!child) return
        seriesLoading.value = true
        try {
            const { data } = await seriesService.getByChild(child.id)
            childSeries.value = data
        } finally {
            seriesLoading.value = false
        }
    },
    { immediate: true }
)
</script>

<template>
    <DefaultLayout>
        <!-- Mode parent -->
        <div v-if="!uiStore.isChildMode">
            <div class="text-center">
                <h2 class="font-dynapuff font-bold text-2xl text-theme">
                    {{ t('pages.home.greeting', { name: authStore.user?.first_name }) }}
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

        <!-- Mode enfant -->
        <div v-else class="p-6 space-y-6">
            <h1 class="font-dynapuff font-bold text-2xl text-theme text-center">
                {{ t('pages.home.greeting', { name: uiStore.activeChild.name }) }}
            </h1>

            <p v-if="seriesLoading">{{ t('common.loading') }}</p>

            <p v-else-if="sortedSeries.length === 0" class="text-gray-400 text-sm text-center">
                {{ t('pages.home.no_series') }}
            </p>

            <div v-else class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                <SeriesCard
                    v-for="serie in sortedSeries"
                    :key="serie.id"
                    :series="serie"
                    @click="router.push({ name: 'child-series-player', params: { id: uiStore.activeChild.id, seriesId: serie.id } })"
                />
            </div>
        </div>
    </DefaultLayout>
</template>
