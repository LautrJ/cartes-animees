<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { loadStripe } from '@stripe/stripe-js'
import DefaultLayout from '@/src/layouts/DefaultLayout.vue'
import { childrenService } from '@/src/services/children.service'
import { subscriptionService } from '@/src/services/subscription.service'

const { t, locale } = useI18n()
const route = useRoute()
const router = useRouter()

const childId = route.params.id
const child = ref(null)
const price = ref(null)
const loading = ref(true)
const submitting = ref(false)
const stripeError = ref('')

let stripe = null
let cardElement = null

onMounted(async () => {
    try {
        const [childRes, priceRes] = await Promise.all([
            childrenService.get(childId),
            subscriptionService.currentPrice(),
        ])
        child.value = childRes.data
        price.value = priceRes.data.price
    } finally {
        loading.value = false
    }

    stripe = await loadStripe(import.meta.env.VITE_STRIPE_KEY, { locale: locale.value })
    const elements = stripe.elements()
    cardElement = elements.create('card', {
        style: {
            base: { fontSize: '16px', color: '#374151' },
        },
    })
    cardElement.mount('#card-element')
})

async function subscribe() {
    submitting.value = true
    stripeError.value = ''

    const { paymentMethod, error } = await stripe.createPaymentMethod({
        type: 'card',
        card: cardElement,
    })

    if (error) {
        stripeError.value = error.message
        submitting.value = false
        return
    }

    try {
        const { data } = await subscriptionService.create(childId, {
            payment_method_id: paymentMethod.id,
        })
        console.log(data)

        if (data.client_secret) {
            const { error: confirmError } = await stripe.confirmCardPayment(data.client_secret, {
                payment_method: paymentMethod.id,
            })
            if (confirmError) {
                stripeError.value = confirmError.message
                submitting.value = false
                return
            }
        }

        router.push({ name: 'child-detail', params: { id: childId } })
    } catch (err) {
        stripeError.value = err.response?.data?.message ?? t('common.error')
        submitting.value = false
    }
}
</script>

<template>
    <DefaultLayout>
        <div v-if="loading" class="p-6">
            <p>{{ t('common.loading') }}</p>
        </div>

        <div v-else class="max-w-lg mx-auto p-6 space-y-4">

            <h1 class="font-dynapuff font-bold text-2xl text-theme">
                {{ t('pages.subscribe.title', { name: child.first_name }) }}
            </h1>

            <div v-if="!child.has_therapist"
                 class="bg-orange-50 border border-orange-200 rounded-2xl p-4 text-sm text-orange-700">
                {{ t('pages.subscribe.no_therapist_warning') }}
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6 space-y-5">

                <div class="flex items-center justify-between">
                    <span class="text-gray-600 text-sm">{{ t('pages.subscribe.monthly_price') }}</span>
                    <span class="font-dynapuff font-bold text-xl text-theme">
                        {{ price }} €<span class="text-sm font-normal text-gray-400">{{ t('pages.subscribe.per_month') }}</span>
                    </span>
                </div>

                <hr class="border-theme" />

                <div>
                    <label class="block text-sm font-medium text-theme-muted mb-2">
                        {{ t('pages.subscribe.card') }}
                    </label>
                    <div id="card-element"
                         class="border border-theme rounded-xl px-3 py-3 focus-within:ring-2 focus-within:ring-[var(--color-primary)]/40">
                    </div>
                </div>

                <p v-if="stripeError" class="text-sm text-red-500">{{ stripeError }}</p>

                <div class="flex gap-3">
                    <button type="button"
                            class="flex-1 py-2 rounded-xl border border-theme text-sm text-theme-muted"
                            @click="router.back()">
                        {{ t('common.cancel') }}
                    </button>
                    <button
                        class="flex-1 btn-theme py-2 rounded-xl text-sm font-medium"
                        :disabled="submitting"
                        @click="subscribe">
                        {{ submitting ? t('common.loading') : t('pages.subscribe.cta') }}
                    </button>
                </div>

            </div>
        </div>
    </DefaultLayout>
</template>
