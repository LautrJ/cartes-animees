<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t, locale } = useI18n()

const props = defineProps({
    series: { type: Object, required: true },
})

const emit = defineEmits(['click'])

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

const idx = computed(() => props.series.id % bgColors.length)
const isCompleted = computed(() => props.series.status === 'completed')

function getName(serie) {
    if (typeof serie.name === 'string') return serie.name
    return serie.name[locale.value] ?? serie.name.fr ?? ''
}
</script>

<template>
    <div
        class="relative h-44 rounded-2xl p-4 flex flex-col cursor-pointer shadow-sm transition-opacity"
        :class="[bgColors[idx], isCompleted ? 'opacity-50 grayscale' : '']"
        @click="emit('click')"
    >

        <div class="flex-1 flex items-center justify-center relative z-10">
            <h3 class="font-dynapuff font-bold text-xl leading-tight text-center" :class="textColors[idx]">
                {{ series.name[locale] }}
            </h3>
        </div>

        <div class="relative z-10 flex items-center justify-between">
            <span class="text-sm font-medium" :class="textColors[idx]">
                {{ series.card_count }} {{ t('pages.child_detail.series.cards') }}
            </span>
            <svg v-if="isCompleted" class="w-6 h-6" :class="textColors[idx]"
                 fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" />
            </svg>
        </div>
    </div>
</template>
