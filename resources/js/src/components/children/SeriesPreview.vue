<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t, locale } = useI18n()

const props = defineProps({
    series: { type: Array, required: true },
    childId: { type: [Number, String], required: true },
})

const emit = defineEmits(['see-all', 'play'])

const bgColors = [
    'bg-sky-100',    'bg-violet-100', 'bg-emerald-100', 'bg-rose-100',
    'bg-amber-100',  'bg-indigo-100', 'bg-teal-100',    'bg-pink-100',
    'bg-orange-100', 'bg-cyan-100',   'bg-lime-100',    'bg-purple-100',
]
const textColors = [
    'text-sky-700',    'text-violet-700', 'text-emerald-700', 'text-rose-700',
    'text-amber-700',  'text-indigo-700', 'text-teal-700',    'text-pink-700',
    'text-orange-700', 'text-cyan-700',   'text-lime-700',    'text-purple-700',
]

const preview = computed(() => props.series.slice(0, 3))

function getName(serie) {
    if (typeof serie.name === 'string') return serie.name
    return serie.name[locale.value] ?? serie.name.fr ?? ''
}
</script>

<template>
    <div class="bg-white rounded-2xl shadow-sm p-5">

        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-gray-500 text-xs uppercase tracking-wide">
                {{ t('pages.child_detail.series.title') }}
            </h2>
            <button @click="emit('see-all')" class="text-sm text-primary font-medium hover:underline">
                {{ t('pages.child_detail.series.see_all') }} →
            </button>
        </div>

        <p v-if="series.length === 0" class="text-sm text-gray-400">
            {{ t('pages.child_detail.series.empty') }}
        </p>

        <div v-else class="flex gap-3 overflow-x-auto pb-2 snap-x snap-mandatory">
            <div
                v-for="serie in preview"
                :key="serie.id"
                class="relative shrink-0 w-[45%] md:w-[calc(33.33%-8px)] rounded-2xl overflow-hidden aspect-[4/3] snap-start cursor-pointer"
                :class="bgColors[serie.id % bgColors.length]"
                @click="emit('play', serie.id)"
            >
                <img
                    v-if="serie.thumbnail"
                    :src="serie.thumbnail"
                    :alt="serie.name"
                    class="w-full h-full object-cover"
                />
                <div
                    v-else
                    class="w-full h-full flex items-center justify-center font-dynapuff font-bold text-3xl"
                    :class="textColors[serie.id % textColors.length]"
                >
                    {{ serie.name[locale][0] }}
                </div>

                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent" />

                <div class="absolute bottom-0 left-0 right-0 p-3">
                    <p class="text-white font-semibold text-sm leading-tight mb-1">
                        {{ serie.name[locale] }}
                    </p>
                    <div class="flex items-center justify-between">
                        <p class="text-white/70 text-xs">
                            {{ serie.card_count }} {{ t('pages.child_detail.series.cards') }}
                        </p>
                        <span
                            class="text-xs font-semibold px-2 py-0.5 rounded-full"
                            :class="serie.status === 'completed' ? 'bg-emerald-400/80 text-white' : 'bg-[var(--color-primary)]/80 text-white'"
                        >
                            {{ serie.status === 'completed' ? '✓' : '→' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>
