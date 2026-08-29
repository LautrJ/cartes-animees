<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import DefaultLayout from "@/src/layouts/DefaultLayout.vue";
import {profileService} from "@/src/services/profile.service.js";

const { t } = useI18n()

const profile = ref(null)
const loading = ref(true)
const editingSection = ref(null)

// Informations personnelles
const infoForm = reactive({ first_name: '', last_name: '', phone: '', email: '' })
const infoLoading = ref(false)
const infoError = ref('')

function startEditInfo() {
    infoForm.first_name = profile.value.first_name
    infoForm.last_name = profile.value.last_name
    infoForm.phone = profile.value.phone
    infoForm.email = profile.value.email
    infoError.value = ''
    editingSection.value = 'info'
}

async function saveInfo() {
    infoLoading.value = true
    infoError.value = ''
    try {
        const { data } = await profileService.update(infoForm)
        profile.value = data
        editingSection.value = null
    } catch {
        infoError.value = t('common.error')
    } finally {
        infoLoading.value = false
    }
}

// Mot de passe
const passwordForm = reactive({ current_password: '', password: '', password_confirmation: '' })
const passwordLoading = ref(false)
const passwordError = ref('')
const passwordSuccess = ref(false)

function startEditPassword() {
    passwordForm.current_password = ''
    passwordForm.password = ''
    passwordForm.password_confirmation = ''
    passwordError.value = ''
    passwordSuccess.value = false
    editingSection.value = 'password'
}

async function savePassword() {
    passwordLoading.value = true
    passwordError.value = ''
    passwordSuccess.value = false
    try {
        await profileService.updatePassword(passwordForm)
        passwordSuccess.value = true
        editingSection.value = null
    } catch (e) {
        passwordError.value = e.response?.status === 422
            ? t('pages.profile.wrong_password')
            : t('common.error')
    } finally {
        passwordLoading.value = false
    }
}

// Avatar et autres
const avatarInitials = computed(() => {
    if (!profile.value) return ''
    return `${profile.value.first_name[0]}${profile.value.last_name[0]}`.toUpperCase()
})

const registeredAt = computed(() => {
    if (!profile.value) return ''
    return new Date(profile.value.created_at).toLocaleDateString('fr-FR')
})

onMounted(async() => {
    try {
        const { data } = await profileService.get()
        profile.value = data
    } finally {
        loading.value = false
    }
})
</script>

<template>
    <DefaultLayout>
        <div v-if="loading" class="p-6">
            <p>{{ t('common.loading') }}</p>
        </div>

        <div v-else class="p-6 space-y-6 max-w-2xl mx-auto">

            <div class="bg-white rounded-2xl shadow-sm p-6 flex items-center gap-5">
                <div class="w-20 h-20 rounded-full btn-theme flex items-center justify-center font-dynapuff font-bold text-2xl text-white shrink-0">
                    {{ avatarInitials }}
                </div>
                <div>
                    <h1 class="font-dynapuff font-bold text-2xl text-theme">
                        {{ profile.first_name }} {{ profile.last_name }}
                    </h1>
                    <p class="text-theme-muted text-sm mt-1">
                        {{ t('pages.profile.member_since', { date: registeredAt }) }}
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6">
                <div class="flex item-center justify-between mb-5">
                    <h2 class="font-semibold text-gray-500 text-xs uppercase tracking-wide">
                        {{ t('pages.profile.personal_info') }}
                    </h2>
                    <button
                        v-if="editingSection !== 'info'"
                        class="text-sm text-primary font-medium"
                        @click="startEditInfo"
                    >
                        {{ t('common.edit') }}
                    </button>
                </div>

                <div v-if="editingSection !== 'info'" class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ t('pages.profile.first_name') }}</span>
                        <span class="font-medium text-gray-800">{{ profile.first_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ t('pages.profile.last_name') }}</span>
                        <span class="font-medium text-gray-800">{{ profile.last_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ t('pages.profile.email') }}</span>
                        <span class="font-medium text-gray-400">{{ profile.email }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ t('pages.profile.phone') }}</span>
                        <span class="font-medium text-gray-800">{{ profile.phone ?? 'Ø' }}</span>
                    </div>
                </div>

                <form v-else class="space-y-4" @submit.prevent="saveInfo">
                    <div>
                        <label class="block text-sm font-medium text-theme-muted mb-1">
                            {{ t('pages.profile.first_name') }}
                        </label>
                        <input v-model="infoForm.first_name" type="text" required
                               class="w-full border border-theme rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]/40"/>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-theme-muted mb-1">
                            {{ t('pages.profile.last_name') }}
                        </label>
                        <input v-model="infoForm.last_name" type="text" required
                               class="w-full border border-theme rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]/40"/>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-theme-muted mb-1">
                            {{ t('pages.profile.email') }}
                        </label>
                        <input v-model="infoForm.email" type="email"
                               class="w-full border border-theme rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]/40"/>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-theme-muted mb-1">
                            {{ t('pages.profile.phone') }}
                        </label>
                        <input v-model="infoForm.phone" type="tel"
                               class="w-full border border-theme rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]/40"/>
                    </div>
                    <p v-if="infoError" class="text-sm text-red-500">{{ infoError }}</p>
                    <div class="flex gap-3">
                        <button type="button" class="flex-1 py-2 rounded-xl border border-theme text-sm text-theme-muted"
                                @click="editingSection = null"
                        >
                            {{ t('common.cancel') }}
                        </button>
                        <button type="submit" class="flex-1 btn-theme py-2 rounded-xl text-sm font-medium" :disabled="infoLoading">
                            {{ infoLoading ? t('common.loading') : t('common.save') }}
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="font-semibold text-gray-500 text-xs uppercase tracking-wide">
                        {{ t('pages.profile.password') }}
                    </h2>
                    <button
                        v-if="editingSection !== 'password'"
                        class="text-sm text-primary font-medium"
                        @click="startEditPassword"
                    >
                        {{ t('common.edit') }}
                    </button>
                </div>

                <div v-if="editingSection !== 'password'">
                    <p v-if="passwordSuccess" class="text-sm text-emerald-600 mb-2">
                        {{ t('pages.profile.password_updated') }}
                    </p>
                    <p class="text-gray-400 text-lg tracking-widest">••••••••</p>
                </div>

                <form v-else class="space-y-4" @submit.prevent="savePassword">
                    <div>
                        <label class="block text-sm font-medium text-theme-muted mb-1">
                            {{ t('pages.profile.current_password') }}
                        </label>
                        <input v-model="passwordForm.current_password" type="password" required
                               class="w-full border border-theme rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring[var(--color-primary)]/40"/>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-theme-muted mb-1">
                            {{ t('pages.profile.new_password') }}
                        </label>
                        <input v-model="passwordForm.password" type="password" required
                               class="w-full border border-theme rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring[var(--color-primary)]/40"/>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-theme-muted mb-1">
                            {{ t('pages.profile.password_confirm') }}
                        </label>
                        <input v-model="passwordForm.password_confirmation" type="password" required
                               class="w-full border border-theme rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring[var(--color-primary)]/40"/>
                    </div>
                    <p v-if="passwordError" class="text-sm text-red-500">{{ passwordError }}</p>
                    <div class="flex gap-3">
                        <button type="button" class="flex-1 py-2 rounded-xl border border-theme text-sm text-theme-muted"
                                @click="editingSection = null"
                        >
                            {{ t('common.cancel') }}
                        </button>
                        <button type="submit" class="flex-1 btn-theme py-2 rounded-xl text-sm font-medium" :disabled="passwordLoading">
                            {{ passwordLoading ? t('common.loading') : t('common.save') }}
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </DefaultLayout>
</template>
