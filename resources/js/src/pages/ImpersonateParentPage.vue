<script setup>
import { onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/src/stores/auth'
import { authService } from '@/src/services/auth.service'

const { t } = useI18n()
const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

onMounted(async () => {
    const token = route.query.token
    const returnUrl = route.query.return_url ?? '/filament-impersonate/leave'

    if (!token) {
        router.push({ name: 'login' })
        return
    }

    authStore.startImpersonation(token, returnUrl)

    try {
        const { data } = await authService.me()
        authStore.user = data
        router.push({ name: 'home' })
    } catch {
        authStore.clearImpersonation()
        authStore.clearAuth()
        router.push({ name: 'login' })
    }
})
</script>

<template>
    <div class="min-h-screen bg-sky-50 flex items-center justify-center">
        <p class="font-dynapuff font-bold text-sky-500 text-xl">
            {{ t('impersonation.connexion') }}
        </p>
    </div>
</template>
