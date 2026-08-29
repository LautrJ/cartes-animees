<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import DefaultLayout from '@/src/layouts/DefaultLayout.vue'
import { seriesService } from '@/src/services/series.service'

const { t, locale } = useI18n()
const route = useRoute()
const router = useRouter()

const childId = route.params.id
const seriesId = route.params.seriesId

const series = ref(null)
const loading = ref(true)

const isPlaying = ref(false)
const currentIndex = ref(0)
const phase = ref('drawn')
const isAnimating = ref(false)

const gifCanvas = ref(null)
const videoEl = ref(null)

let currentAudio = null
let currentTimer = null

const currentCard = computed(() => series.value?.cards[currentIndex.value])
const currentPath = computed(() =>
    phase.value === 'drawn'
        ? currentCard.value?.drawn_animation_path
        : currentCard.value?.real_animation_path
)
const currentIsVideo = computed(() => isVideo(currentPath.value))
const hasPrev = computed(() => currentIndex.value > 0)
const hasNext = computed(() => currentIndex.value < (series.value?.cards.length ?? 0) - 1)

function isVideo(path) {
    return /\.(mp4|webm|mov)$/i.test(path ?? '')
}

function loadGifFirstFrame() {
    if (currentIsVideo.value) return
    const img = new Image()
    img.onload = () => {
        if (!gifCanvas.value) return
        const canvas = gifCanvas.value
        const cw = canvas.offsetWidth
        const ch = canvas.offsetHeight
        if (!cw || !ch) return

        canvas.width = cw
        canvas.height = ch

        const ctx = canvas.getContext('2d')
        const imgAspect = img.naturalWidth / img.naturalHeight
        const containerAspect = cw / ch

        let sx = 0, sy = 0, sw = img.naturalWidth, sh = img.naturalHeight
        if (imgAspect > containerAspect) {
            sw = Math.round(img.naturalHeight * containerAspect)
            sx = Math.round((img.naturalWidth - sw) / 2)
        } else {
            sh = Math.round(img.naturalWidth / containerAspect)
            sy = Math.round((img.naturalHeight - sh) / 2)
        }

        ctx.drawImage(img, sx, sy, sw, sh, 0, 0, cw, ch)
    }
    img.src = currentPath.value
}

function clearMedia() {
    if (currentAudio) { currentAudio.pause(); currentAudio = null }
    if (currentTimer) { clearTimeout(currentTimer); currentTimer = null }
    if (videoEl.value) { videoEl.value.pause(); videoEl.value.currentTime = 0 }
    isAnimating.value = false
}

function enterCard(index) {
    clearMedia()
    currentIndex.value = index
    phase.value = 'drawn'
    nextTick(() => loadGifFirstFrame())
}

function handleTap() {
    if (isAnimating.value) return

    const card = currentCard.value
    isAnimating.value = true

    currentAudio = new Audio(card.sound_path)
    currentAudio.play().catch(() => {})

    if (currentIsVideo.value && videoEl.value) {
        videoEl.value.currentTime = 0
        videoEl.value.play()
    }

    currentTimer = setTimeout(() => {
        if (currentAudio) {
            currentAudio.pause();
            currentAudio = null
        }

        if (currentIsVideo.value && videoEl.value) {
            videoEl.value.pause()
            videoEl.value.currentTime = 0
        }
        isAnimating.value = false

        if (phase.value === 'drawn') {
            phase.value = 'real'
        } else {
            phase.value = 'drawn'
        }
        nextTick(() => loadGifFirstFrame())
    }, card.duration * 1000)
}

function prevCard() { if (hasPrev.value) enterCard(currentIndex.value - 1) }
function nextCard() { if (hasNext.value) enterCard(currentIndex.value + 1) }

function startPlayer() {
    isPlaying.value = true
    nextTick(() => enterCard(0))
}

function stopPlayer() {
    clearMedia()
    isPlaying.value = false
}

onMounted(async () => {
    try {
        const { data } = await seriesService.getDetail(childId, seriesId)
        series.value = data
    } catch {
        router.push({ name: 'home' })
    } finally {
        loading.value = false
    }
})

onUnmounted(clearMedia)
</script>

<template>
    <DefaultLayout>
        <template #header-left>
            <button
                class="w-10 h-10 flex items-center justify-center rounded-2xl hover:bg-black/10 transition-colors text-theme"
                @click="router.push({ name: 'home' })"
            >
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m18.75 4.5-7.5 7.5 7.5 7.5m-6-15L5.25 12l7.5 7.5" />
                </svg>
            </button>
        </template>
        <div v-if="loading" class="p-6">
            <p>{{ t('common.loading') }}</p>
        </div>

        <div v-else-if="!isPlaying" class="p-6 space-y-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="font-dynapuff font-bold text-3xl text-theme">
                        {{ series.name[locale] }}
                    </h1>
                    <p v-if="series.description[locale]" class="text-gray-500 mt-2 text-sm">
                        {{ series.description[locale] }}
                    </p>
                </div>
                <button
                    class="btn-theme shrink-0 px-6 py-3 rounded-2xl text-white font-dynapuff font-bold text-lg"
                    @click="startPlayer()"
                >
                    {{ t('pages.series.play')}}
                </button>
            </div>

            <div class="flex gap-4 overflow-x-auto pb-2">
                <div
                    v-for="card in series.cards"
                    :key="card.id"
                    class="shrink-0 w-36 aspect-[4/3] rounded-2xl overflow-hidden bg-gray-100 shadow-sm"
                >
                    <video
                        v-if="isVideo(card.drawn_animation_path)"
                        :src="card.drawn_animation_path"
                        preload="metadata"
                        class="w-full h-full object-cover"
                    />
                    <img
                        v-else
                        :src="card.drawn_animation_path"
                        :alt="card.name[locale]"
                        class="w-full h-full object-cover"
                    />
                </div>
            </div>

        </div>

        <!-- Player -->
        <div v-else class="flex items-center h-[calc(100vh-6rem)]">

            <div class="w-16 shrink-0 flex items-center justify-center">
                <button
                    v-if="hasPrev"
                    class="w-12 h-12 flex items-center justify-center rounded-full hover:bg-black/10 transition-colors text-theme"
                    @click="prevCard"
                >
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 flex items-center justify-center h-full cursor-pointer select-none"
                 @click="handleTap"
            >
                <div class="relative aspect-[4/3] overflow-hidden rounded-2xl"
                     :style="{ width: 'min(70%, calc((70vh - 4rem) * 4 / 3))' }">
                    <video
                        v-if="currentIsVideo"
                        ref="videoEl"
                        :src="currentPath"
                        preload="metadata"
                        playsinline
                        class="absolute inset-0 w-full h-full object-cover"
                    />

                    <template v-else>
                        <canvas
                            ref="gifCanvas"
                            v-show="!isAnimating"
                            class="absolute inset-0 w-full h-full"
                        />
                        <img
                            v-if="isAnimating"
                            :src="currentPath"
                            class="absolute inset-0 w-full h-full object-cover"
                        />
                    </template>
                </div>
            </div>

            <div class="w-16 shrink-0 flex items-center justify-center">
                <button
                    v-if="hasNext"
                    class="w-12 h-12 flex items-center justify-center rounded-full hover:bg-black/10 transition-colors text-theme"
                    @click="nextCard"
                >
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </button>
            </div>

        </div>

    </DefaultLayout>
</template>
