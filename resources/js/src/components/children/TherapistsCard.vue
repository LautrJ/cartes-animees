<script setup>
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { childrenService } from '@/src/services/children.service'

const { t } = useI18n()

const props = defineProps({
    therapists: { type: Array, default: () => [] },
    childId:    { type: [Number, String], required: true },
})

const emit = defineEmits(['therapist-removed', 'therapist-added'])

const confirmingRemoval = ref(null)

async function confirmRemove(therapistId) {
    try {
        await childrenService.removeTherapist(props.childId, therapistId)
        emit('therapist-removed', therapistId)
    } catch {
        // silencieux pour l'instant
    } finally {
        confirmingRemoval.value = null
    }
}

const showAffiliateForm = ref(false)
const invitationCode    = ref('')
const affiliateError    = ref('')
const affiliating       = ref(false)

async function submitAffiliate() {
    if (!invitationCode.value.trim()) return
    affiliateError.value = ''
    affiliating.value = true

    try {
        await childrenService.affiliateTherapist(props.childId, invitationCode.value.trim())
        invitationCode.value = ''
        showAffiliateForm.value = false
        emit('therapist-added')
    } catch (e) {
        const status = e.response?.status
        if (status === 404) {
            affiliateError.value = t('pages.child_detail.therapists.error_invalid_code')
        } else if (status === 409) {
            affiliateError.value = t('pages.child_detail.therapists.error_already_linked')
        } else {
            affiliateError.value = t('common.error')
        }
    } finally {
        affiliating.value = false
    }
}
</script>

<template>
    <div class="bg-white rounded-2xl shadow-sm p-5">
        <h2 class="font-semibold text-gray-500 text-xs uppercase tracking-wide mb-4">
            {{ t('pages.child_detail.therapists.title') }}
        </h2>

        <div v-if="therapists.length > 0" class="space-y-3 mb-4">
            <div v-for="therapist in therapists" :key="therapist.id" class="flex items-center gap-3">
                <span class="w-2 h-2 rounded-full bg-emerald-400 shrink-0" />
                <span class="flex-1 font-medium text-gray-800">{{ therapist.name }}</span>

                <template v-if="confirmingRemoval === therapist.id">
                    <button
                        class="text-xs text-red-500 font-semibold"
                        @click="confirmRemove(therapist.id)"
                    >
                        {{ t('common.confirm') }}
                    </button>
                    <button
                        class="text-xs text-gray-400"
                        @click="confirmingRemoval = null"
                    >
                        {{ t('common.cancel') }}
                    </button>
                </template>
                <button
                    v-else
                    class="text-xs text-red-400 hover:text-red-600 font-medium"
                    @click="confirmingRemoval = therapist.id"
                >
                    {{ t('pages.child_detail.therapists.end') }}
                </button>
            </div>
        </div>

        <div v-else class="flex items-center gap-3 mb-4">
            <span class="w-2 h-2 rounded-full bg-gray-300 shrink-0" />
            <span class="text-gray-500">{{ t('pages.children.therapist.not_followed') }}</span>
        </div>

        <div v-if="showAffiliateForm" class="mb-3">
            <input
                v-model="invitationCode"
                type="text"
                :placeholder="t('pages.child_detail.therapists.code_placeholder')"
                class="w-full border border-theme rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]/40"
                @keyup.enter="submitAffiliate"
            />
            <p v-if="affiliateError" class="text-xs text-red-500 mt-1">{{ affiliateError }}</p>
            <div class="flex gap-2 mt-2">
                <button
                    class="flex-1 btn-theme py-2 rounded-xl text-white text-sm font-medium disabled:opacity-50"
                    :disabled="affiliating"
                    @click="submitAffiliate"
                >
                    {{ affiliating ? t('common.loading') : t('common.confirm') }}
                </button>
                <button
                    class="flex-1 py-2 rounded-xl border border-gray-200 text-sm text-gray-500"
                    @click="showAffiliateForm = false; affiliateError = ''"
                >
                    {{ t('common.cancel') }}
                </button>
            </div>
        </div>

        <button
            v-else
            class="w-full btn-theme py-2 rounded-xl text-white text-sm font-medium"
            @click="showAffiliateForm = true"
        >
            {{ t('pages.child_detail.therapists.affiliate') }}
        </button>
    </div>
</template>
