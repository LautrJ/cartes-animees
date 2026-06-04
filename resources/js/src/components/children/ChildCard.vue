<script setup>
import { computed } from 'vue'
import { RouterLink } from 'vue-router'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
    child: {
        type: Object,
        required: true
    }
})

const initials = computed(() =>
    `${props.child.first_name[0]}${props.child.last_name[0]}`.toUpperCase()
)

const avatarColors = [
    'bg-sky-400', 'bg-violet-400', 'bg-emerald-400',
    'bg-rose-400', 'bg-amber-400', 'bg-indigo-400'
]
const avatarColor = computed(() => avatarColors[props.child.id % avatarColors.length])

const subscriptionBadge = computed(() => {
    const map = {
        active:   { label: t('pages.children.subscription.active'),   classes: 'bg-emerald-100 text-emerald-700' },
        free:     { label: t('pages.children.subscription.free'),     classes: 'bg-sky-100 text-sky-700' },
        past_due: { label: t('pages.children.subscription.past_due'), classes: 'bg-orange-100 text-orange-700' },
        canceled: { label: t('pages.children.subscription.canceled'), classes: 'bg-red-100 text-red-700' },
    }
    return map[props.child.subscription_status] ?? { label: t('pages.children.subscription.none'), classes: 'bg-gray-100 text-gray-500' }
})
</script>

<template>
    <RouterLink :to="{ name: 'child-detail', params: { id: child.id } }">
        <div class="flex items-center gap-4 bg-white rounded-2xl shadow-sm p-4 hover:shadow-md transition-shadow cursor-pointer">

            <div
                class="w-16 h-16 rounded-full flex items-center justify-center text-white font-bold text-xl shrink-0"
                :class="avatarColor"
            >
                {{ initials }}
            </div>

            <div class="flex flex-col gap-1.5">
                <p class="font-semibold text-lg text-gray-800">
                    {{ child.first_name }} {{ child.last_name }}
                </p>

                <div class="flex flex-wrap gap-2">
                    <span
                        class="text-xs font-medium px-2 py-0.5 rounded-full"
                        :class="child.has_therapist
                            ? 'bg-teal-100 text-teal-700'
                            : 'bg-gray-100 text-gray-500'"
                    >
                        {{ child.has_therapist ? t('pages.children.therapist.followed') : t('pages.children.therapist.not_followed') }}
                    </span>

                    <span
                        class="text-xs font-medium px-2 py-0.5 rounded-full"
                        :class="subscriptionBadge.classes"
                    >
                        {{ subscriptionBadge.label }}
                    </span>
                </div>
            </div>

            <svg class="ml-auto text-gray-300 shrink-0" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </div>
    </RouterLink>
</template>
