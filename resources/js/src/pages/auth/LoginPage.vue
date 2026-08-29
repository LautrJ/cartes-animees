<script setup>
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/src/stores/auth'
import AuthLayout from '@/src/layouts/AuthLayout.vue'

const { t } = useI18n()
const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')

const loading = ref(false)
const error = ref(null)

async function handleSubmit() {
    loading.value = true
    error.value = null
    try {
        await authStore.login(email.value, password.value)
        router.push({ name: 'home' })
    } catch (e) {
        error.value = e.response?.data?.message ?? t('common.error')
    } finally {
        loading.value = false
    }
}
</script>

<template>
    <AuthLayout>
        <div class="text-center mb-8">
            <h2 class="text-2xl font-dynapuff font-semibold text-sky-700">
                {{ t('auth.login.title') }}
            </h2>
            <p class="text-sm text-gray-400 mt-1">
                {{ t('auth.login.subtitle') }}
            </p>
        </div>

        <div v-if="error" class="bg-red-50 border border-red-200 text-red-600 rounded-2xl p-3 text-sm text-center mb-4">
            {{ error }}
        </div>

        <form class="flex flex-col gap-4" @submit.prevent="handleSubmit">

            <div class="flex flex-col gap-1.5">
                <label class="text-sm font-semibold text-gray-600">
                    {{ t('auth.login.email') }}
                </label>
                <input
                    v-model="email"
                    type="email"
                    required
                    class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-colors"
                />
            </div>

            <div class="flex flex-col gap-1.5">
                <div class="flex items-center justify-between">
                    <label class="text-sm font-semibold text-gray-600">
                        {{ t('auth.login.password') }}
                    </label>
                    <RouterLink :to="{ name: 'forgot-password' }" class="text-xs text-sky-500 hover:underline">
                        {{ t('auth.login.forgot') }}
                    </RouterLink>
                </div>
                <input
                    v-model="password"
                    type="password"
                    required
                    class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-colors"
                />
            </div>

            <button
                type="submit"
                :disabled="loading"
                class="w-full bg-sky-400 hover:bg-sky-500 disabled:opacity-50 text-white font-dynapuff font-semibold py-4 rounded-2xl text-base transition-colors mt-2"
            >
                {{ loading ? t('common.loading') : t('auth.login.submit') }}
            </button>
        </form>

        <p class="text-center text-sm text-gray-400 mt-6">
            {{ t('auth.login.no_account') }}
            <RouterLink :to="{ name: 'register' }" class="text-sky-600 font-bold hover:underline">
                {{ t('auth.login.register_link') }}
            </RouterLink>
        </p>
    </AuthLayout>
</template>
