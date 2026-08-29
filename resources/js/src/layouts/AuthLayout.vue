<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
</script>
<template>
    <div class="min-h-screen bg-sky-100 flex items-center justify-center p-4">

        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-dynapuff font-bold text-sky-600 tracking-tight">
                    {{ t('app_name') }}
                </h1>
            </div>

            <div class="bg-white rounded-3xl shadow-lg p-8">
                <slot />
            </div>
        </div>

    </div>
</template>
