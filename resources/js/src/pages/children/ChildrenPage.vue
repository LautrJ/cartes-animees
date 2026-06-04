<script setup>
import DefaultLayout from '@/src/layouts/DefaultLayout.vue'
import {useI18n} from "vue-i18n";
import {useChildrenStore} from "@/src/stores/children.js";
import ChildCard from "@/src/components/children/ChildCard.vue";

const { t } = useI18n()

const childrenStore = useChildrenStore()

</script>

<template>
    <DefaultLayout>
        <div class="text-center">
            <h2 class="font-dynapuff font-bold text-2xl text-theme">
                {{ t('pages.children.title') }}
            </h2>
        </div>

        <div class="p-6">
            <p v-if="childrenStore.loading">{{ t('common.loading') }}</p>

            <p v-else-if="childrenStore.children.length === 0">
                {{ t('pages.children.empty') }}
            </p>

            <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <ChildCard
                    v-for="child in childrenStore.children"
                    :key="child.id"
                    :child="child"
                />
            </div>
        </div>
    </DefaultLayout>
</template>
