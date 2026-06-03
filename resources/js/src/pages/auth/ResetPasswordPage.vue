<script setup>
import { reactive, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import {useRoute, useRouter} from 'vue-router'
import { useAuthStore } from '@/src/stores/auth'
import AuthLayout from '@/src/layouts/AuthLayout.vue'

const { t } = useI18n()
const router = useRouter()
const authStore = useAuthStore()

const route = useRoute()

const form = reactive({
    email: route.query.email ?? '',
    token: route.query.token ?? '',
    password: '',
    password_confirmation: '',
})

const loading = ref(false)
const error = ref(null)

async function handleSubmit() {
    loading.value = true
    error.value = null
    try {
        await authStore.resetPassword(form)
        router.push({ name: 'login' })
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
                {{ t('auth.reset_password.title') }}
            </h2>
        </div>

        <div v-if="error" class="bg-red-50 border border-red-200 text-red-600 rounded-2xl p-3 text-sm text-center mb-4">
            {{ error }}
        </div>

        <form class="flex flex-col gap-4" @submit.prevent="handleSubmit">

            <div class="flex flex-col gap-1.5">
                <label class="text-sm font-semibold text-gray-600">
                    {{ t('auth.reset_password.email') }}
                </label>
                <input
                    v-model="form.email"
                    type="email"
                    required
                    class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-colors"
                />
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="text-sm font-semibold text-gray-600">
                    {{ t('auth.reset_password.password') }}
                </label>
                <input
                    v-model="form.password"
                    type="password"
                    required
                    class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-colors"
                />
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="text-sm font-semibold text-gray-600">
                    {{ t('auth.reset_password.password_confirm') }}
                </label>
                <input
                    v-model="form.password_confirmation"
                    type="password"
                    required
                    class="w-full border border-gray-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-colors"
                />
            </div>

            <button
                type="submit"
                :disabled="loading"
                class="w-full bg-sky-400 hover:bg-sky-500 disabled:opacity-50 text-white font-black py-4 rounded-2xl text-base transition-colors mt-2"
            >
                {{ loading ? t('common.loading') : t('auth.reset_password.submit') }}
            </button>
        </form>
    </AuthLayout>
</template>
