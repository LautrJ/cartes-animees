<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { RouterLink } from 'vue-router'

const { t } = useI18n()

const props = defineProps({
    series: { type: Array, required: true },
    childId: { type: [Number, String], required: true },
})

const preview = computed(() => props.series.slice(0, 3))

function statusLabel(status) {
    return status === 'completed'
        ? t('pages.children.subscription.active')
        : '→'
}

function statusClasses(status) {
    return status === 'completed'
        ? 'bg-emerald-400/80 text-white'
        : 'bg-[var(--color-primary)]/80 text-white'
}
</script>

<template>
    <div class="bg-white rounded-2xl shadow-sm p-5">

        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-gray-500 text-xs uppercase tracking-wide">
                {{ t('pages.child_detail.series.title') }}
            </h2>
            <RouterLink
                v-if="series.length > 0"
                :to="{ name: 'child-series', params: { id: childId } }"
                class="text-sm text-primary font-medium hover:underline"
            >
                {{ t('pages.child_detail.series.see_all') }} →
            </RouterLink>
        </div>

        <p v-if="series.length === 0" class="text-sm text-gray-400">
            {{ t('pages.child_detail.series.empty') }}
        </p>

        <div v-else class="flex gap-3 overflow-x-auto pb-2 snap-x snap-mandatory">
            <RouterLink
                v-for="serie in preview"
                :key="serie.id"
                :to="{ name: 'child-series-player', params: { id: childId, seriesId: serie.id } }"
                class="relative shrink-0 w-[45%] md:w-[calc(33.33%-8px)] rounded-2xl overflow-hidden aspect-[4/3] bg-primary-light snap-start block"
            >
                <img
                    v-if="serie.thumbnail"
                    :src="serie.thumbnail"
                    :alt="serie.name.fr"
                    class="w-full h-full object-cover"
                />
                <div
                    v-else
                    class="w-full h-full flex items-center justify-center text-[var(--color-primary)] font-dynapuff font-bold text-3xl"
                >
                    {{ serie.name[0] }}
                </div>

                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent" />

                <div class="absolute bottom-0 left-0 right-0 p-3">
                    <p class="text-white font-semibold text-sm leading-tight mb-1">
                        {{ serie.name.fr }}
                    </p>
                    <div class="flex items-center justify-between">
                        <p class="text-white/70 text-xs">
                            {{ serie.cards_count }} {{ t('pages.child_detail.series.cards') }}
                        </p>
                        <span
                            class="text-xs font-semibold px-2 py-0.5 rounded-full"
                            :class="statusClasses(serie.status)"
                        >
                            {{ serie.status === 'completed' ? '✓' : '→' }}
                        </span>
                    </div>
                </div>
            </RouterLink>
        </div>

    </div>
</template>
