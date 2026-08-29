<script setup>
import { computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRouter } from 'vue-router'

const { t } = useI18n()
const router = useRouter()

const props = defineProps({
    subscription: { type: Object, default: null },
    childId: { type: [Number, String], required: true },
})

const emit = defineEmits(['cancel'])

const status = computed(() => props.subscription?.status ?? null)

const badge = computed(() => {
    const map = {
        active:     { label: t('pages.children.subscription.active'),     classes: 'bg-emerald-100 text-emerald-700' },
        free:       { label: t('pages.children.subscription.free'),       classes: 'bg-sky-100 text-sky-700' },
        past_due:   { label: t('pages.children.subscription.past_due'),   classes: 'bg-orange-100 text-orange-700' },
        canceled:   { label: t('pages.children.subscription.canceled'),   classes: 'bg-red-100 text-red-700' },
        incomplete: { label: t('pages.children.subscription.incomplete'), classes: 'bg-amber-100 text-amber-700' },
    }
    return map[status.value] ?? {
        label: t('pages.children.subscription.none'),
        classes: 'bg-gray-100 text-gray-500'
    }
})

const nextPayment = computed(() => {
    if (!props.subscription?.next_payment) return null
    return new Date(props.subscription.next_payment).toLocaleDateString('fr-FR')
})

const overridePrice = computed(() => {
    const p = props.subscription?.override_price
    if (p === null || p === undefined) return null
    return parseFloat(p)
})

const overridePriceLabel = computed(() => {
    if (overridePrice.value === null) return null
    if (overridePrice.value === 0) return t('pages.child_detail.subscription.price_free')
    return t('pages.child_detail.subscription.price_reduced', {
        price: overridePrice.value.toLocaleString('fr-FR', { minimumFractionDigits: 2 }),
    })
})

const confirmCancel = ref(false)

const dotColor = computed(() => {
    if (status.value === 'active' || status.value === 'free') return 'bg-emerald-400'
    if (status.value === 'past_due') return 'bg-orange-400'
    if (status.value === 'incomplete') return 'bg-amber-400'
    return 'bg-gray-300'
})
</script>

<template>
    <div class="bg-white rounded-2xl shadow-sm p-5">
        <h2 class="font-semibold text-gray-500 text-xs uppercase tracking-wide mb-4">
            {{ t('pages.child_detail.subscription.title') }}
        </h2>

        <div class="flex items-center gap-3 mb-4">
            <span class="w-2.5 h-2.5 rounded-full" :class="dotColor" />
            <span class="font-medium text-gray-800">{{ badge.label }}</span>
            <span class="ml-auto text-xs font-medium px-2 py-0.5 rounded-full" :class="badge.classes">
                {{ badge.label }}
            </span>
        </div>

        <p v-if="overridePriceLabel" class="text-sm font-medium mb-3"
           :class="overridePrice === 0 ? 'text-emerald-600' : 'text-sky-600'">
            {{ overridePriceLabel }}
        </p>

        <p v-if="nextPayment" class="text-sm text-gray-500 mb-4">
            {{ t('pages.child_detail.subscription.next_payment', { date: nextPayment }) }}
        </p>

        <p v-if="status === 'past_due'" class="text-sm text-orange-600 bg-orange-50 rounded-xl px-3 py-2 mb-4">
            {{ t('pages.child_detail.subscription.past_due_warning') }}
        </p>

        <button
            v-if="!status || status === 'canceled'"
            class="w-full btn-theme py-2 rounded-xl text-white text-sm font-medium"
            @click="router.push({ name: 'child-subscribe', params: { id: childId } })"
        >
            {{ t('pages.child_detail.subscription.subscribe') }}
        </button>

        <template v-else-if="status === 'incomplete'">
            <p class="text-sm text-amber-700 bg-amber-50 rounded-xl px-3 py-2 mb-4">
                {{ t('pages.child_detail.subscription.incomplete_warning') }}
            </p>
            <button
                class="w-full bg-amber-500 hover:bg-amber-600 transition py-2 rounded-xl text-white text-sm font-medium"
                @click="router.push({ name: 'child-subscribe', params: { id: childId } })"
            >
                {{ t('pages.child_detail.subscription.retry') }}
            </button>
        </template>

        <button
            v-else-if="status === 'past_due'"
            class="w-full bg-orange-500 hover:bg-orange-600 transition py-2 rounded-xl text-white text-sm font-medium"
            @click="router.push({ name: 'child-subscribe', params: { id: childId } })"
        >
            {{ t('pages.child_detail.subscription.regularize') }}
        </button>

        <template v-else-if="status === 'active'">
            <button
                v-if="!confirmCancel"
                class="w-full py-2 rounded-xl border border-red-200 text-red-500 text-sm font-medium hover:bg-red-50 transition"
                @click="confirmCancel = true"
            >
                {{ t('pages.child_detail.subscription.cancel') }}
            </button>
            <div v-else class="space-y-2">
                <p class="text-sm text-red-600 text-center">
                    {{ t('pages.child_detail.subscription.cancel_confirm') }}
                </p>
                <div class="flex gap-2">
                    <button
                        class="flex-1 py-2 rounded-xl border border-gray-200 text-gray-500 text-sm font-medium hover:bg-gray-50 transition"
                        @click="confirmCancel = false"
                    >
                        {{ t('common.cancel') }}
                    </button>
                    <button
                        class="flex-1 py-2 rounded-xl bg-red-500 hover:bg-red-600 text-white text-sm font-medium transition"
                        @click="emit('cancel'); confirmCancel = false"
                    >
                        {{ t('common.confirm') }}
                    </button>
                </div>
            </div>
        </template>
    </div>
</template>
