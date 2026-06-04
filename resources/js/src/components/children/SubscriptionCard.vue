<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRouter } from 'vue-router'

const { t } = useI18n()
const router = useRouter()

const props = defineProps({
    subscription: { type: Object, default: null },
    childId: { type: [Number, String], required: true },
})

const badge = computed(() => {
    const map = {
        active:   { label: t('pages.children.subscription.active'),   classes: 'bg-emerald-100 text-emerald-700' },
        free:     { label: t('pages.children.subscription.free'),     classes: 'bg-sky-100 text-sky-700' },
        past_due: { label: t('pages.children.subscription.past_due'), classes: 'bg-orange-100 text-orange-700' },
        canceled: { label: t('pages.children.subscription.canceled'), classes: 'bg-red-100 text-red-700' },
    }
    return map[props.subscription?.status] ?? {
        label: t('pages.children.subscription.none'),
        classes: 'bg-gray-100 text-gray-500'
    }
})

const nextPayment = computed(() => {
    if (!props.subscription?.next_payment) return null
    return new Date(props.subscription.next_payment).toLocaleDateString('fr-FR')
})
</script>

<template>
    <div class="bg-white rounded-2xl shadow-sm p-5">
        <h2 class="font-semibold text-gray-500 text-xs uppercase tracking-wide mb-4">
            {{ t('pages.child_detail.subscription.title') }}
        </h2>

        <div class="flex items-center gap-3 mb-4">
            <span class="w-2.5 h-2.5 rounded-full"
                  :class="subscription?.status === 'active' || subscription?.status === 'free'
                    ? 'bg-emerald-400' : 'bg-gray-300'"
            />
            <span class="font-medium text-gray-800">{{ badge.label }}</span>
            <span class="ml-auto text-xs font-medium px-2 py-0.5 rounded-full" :class="badge.classes">
                {{ badge.label }}
            </span>
        </div>

        <p v-if="nextPayment" class="text-sm text-gray-500 mb-4">
            {{ t('pages.child_detail.subscription.next_payment', { date: nextPayment }) }}
        </p>

        <button
            class="w-full btn-theme py-2 rounded-xl text-white text-sm font-medium"
            @click="router.push({ name: 'child-subscribe', params: { id: childId } })"
        >
            {{ t('pages.child_detail.subscription.manage') }}
        </button>
    </div>
</template>
