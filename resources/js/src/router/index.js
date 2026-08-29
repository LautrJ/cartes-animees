import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/src/stores/auth'

const routes = [
    {
        path: '/login',
        name: 'login',
        component: () => import('@/src/pages/auth/LoginPage.vue'),
        meta: { requiresGuest: true },
    },
    {
        path: '/register',
        name: 'register',
        component: () => import('@/src/pages/auth/RegisterPage.vue'),
        meta: { requiresGuest: true },
    },
    {
        path: '/forgot-password',
        name: 'forgot-password',
        component: () => import('@/src/pages/auth/ForgotPasswordPage.vue'),
        meta: { requiresGuest: true },
    },
    {
        path: '/reset-password',
        name: 'reset-password',
        component: () => import('@/src/pages/auth/ResetPasswordPage.vue'),
        meta: { requiresGuest: true },
    },
    {
        path: '/impersonate-parent',
        name: 'impersonate-parent',
        component: () => import('@/src/pages/ImpersonateParentPage.vue'),
    },
    {
        path: '/home',
        name: 'home',
        component: () => import('@/src/pages/children/HomePage.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/children',
        name: 'children',
        component: () => import('@/src/pages/children/ChildrenPage.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/children/create',
        name: 'child-create',
        component: () => import('@/src/pages/children/ChildCreatePage.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/children/:id/subscribe',
        name: 'child-subscribe',
        component: () => import('@/src/pages/children/ChildSubscribePage.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/children/:id',
        name: 'child-detail',
        component: () => import('@/src/pages/children/ChildDetailPage.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/children/:id/series/:seriesId',
        name: 'child-series-player',
        component: () => import('@/src/pages/children/SeriesDetailPage.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/profile',
        name: 'profile',
        component: () => import('@/src/pages/ProfilePage.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/',
        name: 'landing',
        component: () => import('@/src/pages/LandingPage.vue'),
        meta: { requiresGuest: true },
    },
    {
        path: '/:pathMatch(.*)*',
        redirect: '/home',
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

router.beforeEach((to) => {
    const authStore = useAuthStore()

    authStore.checkSession()

    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        return { name: 'login' }
    }

    if (to.meta.requiresGuest && authStore.isAuthenticated) {
        return { name: 'home' }
    }
})

export default router
