<script setup>
import DefaultLayout from '@/src/layouts/DefaultLayout.vue'
import {useI18n} from "vue-i18n";

const { t } = useI18n()
</script>

<template>
    <DefaultLayout>
        <h1 class="font-dynapuff font-bold text-2xl text-theme">{{ t('pages.children.title') }}</h1>
    </DefaultLayout>
</template>
