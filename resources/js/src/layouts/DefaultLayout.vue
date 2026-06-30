<script setup>
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/src/stores/auth'
import { useUiStore } from '@/src/stores/ui'
import { useChildrenStore } from '@/src/stores/children'

const { t } = useI18n()
const router = useRouter()
const authStore = useAuthStore()
const uiStore = useUiStore()
const childrenStore = useChildrenStore()

const menuOpen = ref(false)
const avatarOpen = ref(false)

const theme = computed(() => uiStore.isChildMode ? 'child' : 'parent')

const initials = computed(() => {
    if (uiStore.isChildMode) return uiStore.activeChild.name.charAt(0).toUpperCase()
    return authStore.user?.first_name?.charAt(0).toUpperCase() ?? '?'
})

const displayName = computed(() => {
    if (uiStore.isChildMode) return uiStore.activeChild.name
    return authStore.user?.first_name ?? ''
})

function switchToChild(child) {
    uiStore.switchToChild(child)
    avatarOpen.value = false
    router.push({ name: 'home' })
}

function switchToParent() {
    uiStore.switchToParent()
    avatarOpen.value = false
    router.push({ name: 'home' })
}

async function logout() {
    avatarOpen.value = false
    await authStore.logout()
    router.push({ name: 'login' })
}

async function leaveImpersonation() {
    await authStore.leaveImpersonation()
}

onMounted(() => {
    childrenStore.fetchChildren()
})
</script>

<template>
    <div :data-theme="theme" class="min-h-screen bg-theme">

        <div
            v-if="authStore.isImpersonating"
            class="bg-amber-500 text-white px-4 py-2.5 flex items-center justify-between text-sm font-semibold sticky top-0 z-50"
        >
            <span>
                👁️ {{ t('impersonation.banner', { name: authStore.user?.first_name ?? '...' }) }}
            </span>
            <button
                class="underline hover:no-underline transition-all"
                @click="leaveImpersonation"
            >
                {{ t('impersonation.leave') }}
            </button>
        </div>

        <Transition name="fade">
            <div
                v-if="menuOpen"
                class="fixed inset-0 bg-black/30 z-40"
                @click="menuOpen = false"
            />
        </Transition>

        <Transition name="slide">
            <aside
                v-if="menuOpen && !uiStore.isChildMode"
                class="fixed inset-y-0 left-0 w-64 bg-theme-card shadow-2xl z-50 flex flex-col"
            >
                <div class="p-6 border-b border-theme">
                    <span class="font-dynapuff font-bold text-xl text-primary">
                        {{ t('app_name') }}
                    </span>
                </div>

                <nav class="flex flex-col gap-1 p-4 flex-1">
                    <RouterLink
                        :to="{ name: 'children' }"
                        class="flex items-center gap-3 px-4 py-3 rounded-2xl text-theme hover:bg-theme-navbar transition-colors font-semibold"
                        @click="menuOpen = false"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{ t('pages.children.title') }}
                    </RouterLink>

                    <RouterLink
                        :to="{ name: 'profile' }"
                        class="flex items-center gap-3 px-4 py-3 rounded-2xl text-theme hover:bg-theme-navbar transition-colors font-semibold"
                        @click="menuOpen = false"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        {{ t('pages.profile.title') }}
                    </RouterLink>
                </nav>

                <div class="p-4 border-t border-theme">
                    <button
                        class="flex items-center gap-3 px-4 py-3 rounded-2xl text-red-500 hover:bg-red-50 transition-colors w-full font-semibold"
                        @click="logout"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        {{ t('default_layout.disconnect') }}
                    </button>
                </div>
            </aside>
        </Transition>

        <header class="bg-theme-navbar h-16 flex items-center justify-between px-4 sticky top-0 z-30 shadow-sm">

            <div class="flex items-center gap-1">
                <button
                    v-if="!uiStore.isChildMode"
                    class="w-10 h-10 flex items-center justify-center rounded-2xl hover:bg-black/10 transition-colors text-theme"
                    @click="menuOpen = !menuOpen"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <button
                    v-if="!uiStore.isChildMode"
                    class="w-10 h-10 flex items-center justify-center rounded-2xl hover:bg-black/10 transition-colors text-theme"
                    @click="router.push({ name: 'home' })"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />                    </svg>
                </button>
            </div>

            <span class="font-dynapuff font-bold text-xl text-theme">
                {{ t('app_name') }}
            </span>

            <div class="flex items-center gap-2">

                <button
                    v-if="!uiStore.isChildMode"
                    class="w-10 h-10 flex items-center justify-center rounded-2xl hover:bg-black/10 transition-colors text-theme"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </button>

                <div class="relative">
                    <button
                        class="w-10 h-10 rounded-full bg-theme-card border-2 border-theme flex items-center justify-center font-dynapuff font-bold text-primary text-sm"
                        @click="avatarOpen = !avatarOpen"
                    >
                        {{ initials }}
                    </button>

                    <div
                        v-if="avatarOpen"
                        class="fixed inset-0 z-40"
                        @click="avatarOpen = false"
                    />

                    <div
                        v-if="avatarOpen"
                        class="absolute right-0 top-full mt-2 w-56 bg-theme-card rounded-2xl shadow-xl border border-theme z-50 overflow-hidden"
                    >
                        <div class="px-4 py-3 border-b border-theme">
                            <p class="text-xs text-theme-muted font-semibold uppercase tracking-wide">
                                {{ uiStore.isChildMode ? t('default_layout.children_space') : t('default_layout.parent_space') }}
                            </p>
                            <p class="font-dynapuff font-bold text-theme text-base mt-0.5">
                                {{ displayName }}
                            </p>
                        </div>

                        <div v-if="uiStore.isChildMode" class="p-2">
                            <button
                                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-theme-navbar transition-colors text-theme font-semibold text-sm"
                                @click="switchToParent"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                                </svg>
                                {{ t('default_layout.switch_to_parent') }}
                            </button>
                        </div>

                        <div v-else class="p-2">
                            <p class="px-3 py-1.5 text-xs text-theme-muted font-semibold uppercase tracking-wide">
                                {{ t('default_layout.switch_profile') }}
                            </p>

                            <button
                                v-for="child in childrenStore.children"
                                :key="child.id"
                                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-theme-navbar transition-colors text-theme font-semibold text-sm"
                                @click="switchToChild(child)"
                            >
                                <span class="w-7 h-7 rounded-full bg-primary-light flex items-center justify-center font-dynapuff font-bold text-primary text-xs">
                                    {{ child.first_name.charAt(0).toUpperCase() }}
                                </span>
                                {{ child.first_name }}
                            </button>

                            <div v-if="childrenStore.children.length === 0" class="px-3 py-2.5 text-sm text-theme-muted">
                                {{ t('default_layout.no_children') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="p-4">
            <slot />
        </main>

    </div>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.slide-enter-active, .slide-leave-active { transition: transform 0.25s ease; }
.slide-enter-from, .slide-leave-to { transform: translateX(-100%); }
</style>
