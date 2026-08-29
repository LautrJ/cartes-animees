import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { authService } from '@/src/services/auth.service'

export const useAuthStore = defineStore('auth', () => {
    const token = ref(
        sessionStorage.getItem('impersonate_token') ?? localStorage.getItem('auth_token')
    )
    const user = ref(null)
    const isImpersonating = ref(!!sessionStorage.getItem('impersonate_token'))
    const impersonateReturnUrl = ref(sessionStorage.getItem('impersonate_return_url'))

    const isAuthenticated = computed(() => !!token.value)

    function setAuth(userData, authToken) {
        token.value = authToken
        user.value = userData
        localStorage.setItem('auth_token', authToken)
    }

    const INACTIVITY_LIMIT = 2 * 60 * 60 * 1000 // 2 heures en ms

    function checkSession() {
        if (!token.value || isImpersonating.value) return
        const lastActivity = parseInt(localStorage.getItem('last_activity') ?? '0')
        if (!lastActivity) return
        if (Date.now() - lastActivity > INACTIVITY_LIMIT) {
            clearAuth()
        }
    }

    function clearAuth() {
        token.value = null
        user.value = null
        localStorage.removeItem('auth_token')
        localStorage.removeItem('last_activity')
    }

    async function login(email, password) {
        const { data } = await authService.login(email, password)
        setAuth(data.user, data.token)
    }

    async function register(formData) {
        const { data } = await authService.register(formData)
        setAuth(data.user, data.token)
    }

    async function logout() {
        try {
            await authService.logout()
        } catch {}
        clearAuth()
    }

    async function init() {
        if (!token.value) return
        checkSession()
        if (!token.value) return
        try {
            const { data } = await authService.me()
            user.value = data
        } catch {
            clearAuth()
            clearImpersonation()
        }
    }

    function startImpersonation(authToken, returnUrl) {
        token.value = authToken
        isImpersonating.value = true
        impersonateReturnUrl.value = returnUrl
        sessionStorage.setItem('impersonate_token', authToken)
        sessionStorage.setItem('impersonate_return_url', returnUrl)
    }

    function clearImpersonation() {
        isImpersonating.value = false
        impersonateReturnUrl.value = null
        sessionStorage.removeItem('impersonate_token')
        sessionStorage.removeItem('impersonate_return_url')
    }

    async function leaveImpersonation() {
        const returnUrl = impersonateReturnUrl.value ?? '/filament-impersonate/leave'
        try {
            await authService.logout()
        } catch {}
        token.value = localStorage.getItem('auth_token')
        user.value = null
        clearImpersonation()
        window.location.href = returnUrl
    }

    async function forgotPassword(email) {
        await authService.forgotPassword(email)
    }

    async function resetPassword(formData) {
        await authService.resetPassword(formData)
    }

    return {
        token, user, isAuthenticated, isImpersonating, impersonateReturnUrl,
        setAuth, clearAuth, checkSession, login, register, logout, init,
        startImpersonation, clearImpersonation, leaveImpersonation,
        forgotPassword, resetPassword,
    }
})
