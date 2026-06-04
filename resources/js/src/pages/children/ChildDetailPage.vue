<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import DefaultLayout from '@/src/layouts/DefaultLayout.vue'
import SubscriptionCard from '@/src/components/children/SubscriptionCard.vue'
import TherapistsCard from '@/src/components/children/TherapistsCard.vue'
import SeriesPreview from '@/src/components/children/SeriesPreview.vue'
import ChildEditModal from '@/src/components/children/ChildEditModal.vue'
import { childrenService } from '@/src/services/children.service'
import { seriesService } from '@/src/services/series.service'

const { t } = useI18n()
const route = useRoute()
const router = useRouter()

const child = ref(null)
const series = ref([])
const loading = ref(true)
const showEditModal = ref(false)

const avatarColors = [
    'bg-sky-400', 'bg-violet-400', 'bg-emerald-400',
    'bg-rose-400', 'bg-amber-400', 'bg-indigo-400'
]

async function loadChild() {
    loading.value = true
    const id = route.params.id
    try {
        const [childRes, seriesRes] = await Promise.all([
            childrenService.get(id),
            seriesService.getByChild(id),
        ])
        child.value = childRes.data
        series.value = seriesRes.data
    } catch {
        router.push({ name: 'children' })
    } finally {
        loading.value = false
    }
}

onMounted(loadChild)

function onTherapistRemoved(therapistId) {
    child.value.therapists = child.value.therapists.filter(t => t.id !== therapistId)
}

function onChildUpdated(updated) {
    child.value = { ...child.value, ...updated }
    showEditModal.value = false
}

const avatarColor = (id) => avatarColors[id % avatarColors.length]

const initials = (c) =>
    `${c.first_name[0]}${c.last_name[0]}`.toUpperCase()

const formattedBirthdate = (birthdate) =>
    new Date(birthdate).toLocaleDateString('fr-FR')
</script>

<template>
    <DefaultLayout>
        <div v-if="loading" class="p-6">
            <p>{{ t('common.loading') }}</p>
        </div>

        <div v-else class="p-6 space-y-6">

            <div class="bg-white rounded-2xl shadow-sm p-6 flex items-center gap-5">
                <div
                    class="w-20 h-20 rounded-full flex items-center justify-center text-white font-bold text-2xl shrink-0"
                    :class="avatarColor(child.id)"
                >
                    {{ initials(child) }}
                </div>
                <div class="flex-1">
                    <h1 class="font-dynapuff font-bold text-2xl text-gray-800">
                        {{ child.first_name }} {{ child.last_name }}
                    </h1>
                    <p class="text-gray-500 mt-1">
                        {{ t('pages.child_detail.born_on', { date: formattedBirthdate(child.birthdate) }) }}
                    </p>
                </div>
                <button
                    class="btn-theme px-4 py-2 rounded-xl text-white text-sm font-medium"
                    @click="showEditModal = true"
                >
                    {{ t('common.edit') }}
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <SubscriptionCard
                    :subscription="child.subscription"
                    :child-id="child.id"
                />
                <TherapistsCard
                    :therapists="child.therapists"
                    :child-id="child.id"
                    @therapist-removed="onTherapistRemoved"
                    @therapist-added="loadChild"
                />
            </div>

            <SeriesPreview
                :series="series"
                :child-id="child.id"
            />

            <div v-if="child.notes" class="bg-white rounded-2xl shadow-sm p-6">
                <h2 class="font-semibold text-gray-600 text-sm uppercase tracking-wide mb-3">
                    {{ t('pages.child_detail.therapist_notes') }}
                </h2>
                <p class="text-gray-700 whitespace-pre-wrap">{{ child.notes }}</p>
            </div>

        </div>

        <ChildEditModal
            v-if="showEditModal"
            :child="child"
            @close="showEditModal = false"
            @updated="onChildUpdated"
        />
    </DefaultLayout>
</template>
