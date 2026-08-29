<script setup>
import {reactive, ref} from 'vue'
import { useI18n } from 'vue-i18n'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/src/stores/auth'
import AuthLayout from '@/src/layouts/AuthLayout.vue'

const { t } = useI18n()
const router = useRouter()
const authStore = useAuthStore()

const form = reactive({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
})

const loading = ref(false)
const error = ref(null)

async function handleSubmit() {
    loading.value = true
    error.value = null
    try {
        await authStore.register(form)
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
                {{ t('auth.register.title') }}
            </h2>
            <p class="text-sm text-gray-400 mt-1">
                {{ t('auth.register.subtitle') }}
            </p>
        </div>

        <div v-if="error" class="bg-red-50 border border-red-200 text-red-600 rounded-2xl p-3 text-sm text-center mb-4">
            {{ error }}
        </div>

        <form class="flex flex-col gap-2" @submit.prevent="handleSubmit">

            <div class="flex flex-row gap-2">

                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-gray-600">
                        {{ t('auth.register.first_name') }}
                    </label>
                    <input
                        v-model="form.first_name"
                        type="text"
                        required
                        class="w-full border border-gray-200 rounded-2xl px-4 py-2 text-sm focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-colors"
                    />
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-gray-600">
                        {{ t('auth.register.last_name') }}
                    </label>
                    <input
                        v-model="form.last_name"
                        type="text"
                        required
                        class="w-full border border-gray-200 rounded-2xl px-4 py-2 text-sm focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-colors"
                    />
                </div>

            </div>

            <div class="flex flex-row gap-2">

                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-gray-600">
                        {{ t('auth.register.email') }}
                    </label>
                    <input
                        v-model="form.email"
                        type="email"
                        required
                        class="w-full border border-gray-200 rounded-2xl px-4 py-2 text-sm focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-colors"
                    />
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-gray-600">
                        {{ t('auth.register.phone') }}
                    </label>
                    <input
                        v-model="form.phone"
                        type="tel"
                        class="w-full border border-gray-200 rounded-2xl px-4 py-2 text-sm focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-colors"
                    />
                </div>

            </div>

            <div class="flex flex-col gap-1.5">
                <label class="text-sm font-semibold text-gray-600">
                    {{ t('auth.register.password') }}
                </label>
                <input
                    v-model="form.password"
                    type="password"
                    required
                    class="w-full border border-gray-200 rounded-2xl px-4 py-2 text-sm focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-colors"
                />
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="text-sm font-semibold text-gray-600">
                    {{ t('auth.register.password_confirm') }}
                </label>
                <input
                    v-model="form.password_confirmation"
                    type="password"
                    required
                    class="w-full border border-gray-200 rounded-2xl px-4 py-2 text-sm focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-colors"
                />
            </div>

            <button
                type="submit"
                :disabled="loading"
                class="w-full bg-sky-400 hover:bg-sky-500 disabled:opacity-50 text-white font-dynapuff font-semibold py-4 rounded-2xl text-base transition-colors mt-2"
            >
                {{ loading ? t('common.loading') : t('auth.register.submit') }}
            </button>
        </form>
    </AuthLayout>
</template>
