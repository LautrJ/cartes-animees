<script setup>
import {reactive, ref} from 'vue'
import { useI18n } from 'vue-i18n'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/src/stores/auth'
import AuthLayout from '@/src/layouts/AuthLayout.vue'

const { t } = useI18n()
const router = useRouter()
const authStore = useAuthStore()

const email = ref('')

const loading = ref(false)
const success = ref(false)

async function handleSubmit() {
    loading.value = true
    success.value = false
    try {
        await authStore.forgotPassword(email.value)
        success.value = true
    } catch (e) {
        success.value = true
    } finally {
        loading.value = false
    }
}
</script>

<template>
    <AuthLayout>
        <div class="text-center mb-8">
            <h2 class="text-2xl font-dynapuff font-semibold text-sky-700">
                {{ t('auth.forgot_password.title') }}
            </h2>
            <p class="text-sm text-gray-400 mt-1">
                {{ t('auth.forgot_password.subtitle') }}
            </p>
        </div>

        <div v-if="success" class="bg-green-50 border border-green-200 text-green-600 rounded-2xl p-3 text-sm text-center mb-4">
            {{ t('auth.forgot_password.success') }}

            <p class="text-center text-sm text-gray-400 mt-6">
                <RouterLink :to="{ name: 'login' }" class="text-sky-600 font-bold hover:underline">
                    {{ t('auth.forgot_password.back') }}
                </RouterLink>
            </p>
        </div>

        <form v-if="!success" class="flex flex-col gap-2" @submit.prevent="handleSubmit">

            <div class="flex flex-col gap-1.5">
                <label class="text-sm font-semibold text-gray-600">
                    {{ t('auth.forgot_password.email') }}
                </label>
                <input
                    v-model="email"
                    type="email"
                    required
                    class="w-full border border-gray-200 rounded-2xl px-4 py-2 text-sm focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-colors"
                />
            </div>

            <button
                type="submit"
                :disabled="loading"
                class="w-full bg-sky-400 hover:bg-sky-500 disabled:opacity-50 text-white font-dynapuff font-semibold py-4 rounded-2xl text-base transition-colors mt-2"
            >
                {{ loading ? t('common.loading') : t('auth.forgot_password.submit') }}
            </button>
        </form>
    </AuthLayout>
</template>
