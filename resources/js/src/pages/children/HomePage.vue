<script setup>
import { reactive, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import {useRoute, useRouter} from 'vue-router'
import { useAuthStore } from '@/src/stores/auth'
import AuthLayout from '@/src/layouts/AuthLayout.vue'
import DefaultLayout from "@/src/layouts/DefaultLayout.vue";

const { t } = useI18n()
const router = useRouter()
const authStore = useAuthStore()

const route = useRoute()

</script>

<template>
    <DefaultLayout>
    </DefaultLayout>
</template>
