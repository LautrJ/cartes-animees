<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const { t, locale } = useI18n()

const props = defineProps({
    child : { type: Object, required: true },
})

const avatarColors = [
    'bg-sky-400', 'bg-violet-400', 'bg-emerald-400',
    'bg-rose-400', 'bg-amber-400', 'bg-indigo-400',
]
const avatarColor = computed(() => avatarColors[props.child.id % avatarColors.length])

const borderColors = [
    'border-sky-400', 'border-violet-400', 'border-emerald-400',
    'border-rose-400', 'border-amber-400', 'border-indigo-400',
]
const borderColor = computed(() => borderColors[props.child.id % borderColors.length])

const cardColors = [
    'bg-sky-100', 'bg-violet-100', 'bg-emerald-100',
    'bg-rose-100', 'bg-amber-100', 'bg-indigo-100',
]
const cardColor = computed(() => cardColors[props.child.id % cardColors.length])

const initials = computed(() =>
    `${props.child.first_name[0]}${props.child.last_name[0]}`.toUpperCase()
)

const subscriptionBadge = computed(() => {
    const status = props.child.subscription_status
    const override = props.child.subscription_override_price

    if (status === 'active' && override !== null && override !== undefined && parseFloat(override) === 0) {
        return { label: t('pages.children.subscription.free'), classes: 'text-sky-600' }
    }

    const map = {
        active:   { label: t('pages.children.subscription.active'),   classes: 'text-emerald-600' },
        free:     { label: t('pages.children.subscription.free'),     classes: 'text-sky-600' },
        past_due: { label: t('pages.children.subscription.past_due'), classes: 'text-orange-500' },
        canceled: { label: t('pages.children.subscription.canceled'), classes: 'text-red-500' },
    }
    return map[status] ?? { label: t('pages.children.subscription.none'), classes: 'text-gray-400' }
})

function seriesName(name) {
    if (typeof name === 'string') return name
    return name[locale.value] ?? name.fr ?? ''
}

function relativeTime(dateStr) {
    if (!dateStr) return null
    const days = Math.floor((Date.now() - new Date(dateStr).getTime()) / 86400000)
    if (days === 0) return t('common.today')
    if (days === 1) return t('common.yesterday')
    return t('common.days_ago', { days })
}
</script>

<template>
    <div class="rounded-2xl shadow-sm p-4 w-56 shrink-0 flex flex-col gap-3 cursor-pointer hover:shadow-md transition-shadow border-2" :class="[cardColor, borderColor]">

        <div class="flex items-center gap-2">
            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm shrink-0"
                :class="avatarColor">
                {{ initials }}
            </div>
            <span class="font-semibold text-gray-800 truncate">{{ child.first_name }}</span>
        </div>

        <p class="text-xs font-medium" :class="subscriptionBadge.classes">
            {{ subscriptionBadge.label }}
        </p>

        <p v-if="child.last_series" class="text-xs text-gray-400 truncate">
            {{ seriesName(child.last_series.name) }} · {{ relativeTime(child.last_series.played_at) }}
        </p>

        <p v-else class="text-xs text-gray-300 italic">
            {{ t('pages.home.no_series_played') }}
        </p>

    </div>
</template>
