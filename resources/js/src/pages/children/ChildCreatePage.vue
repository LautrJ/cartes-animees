<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import DefaultLayout from '@/src/layouts/DefaultLayout.vue'
import { childrenService } from '@/src/services/children.service'

const { t } = useI18n()
const router = useRouter()

const form = reactive({ first_name: '', last_name: '', birthdate: '' })
const loading = ref(false)
const error = ref('')

async function submit() {
    loading.value = true
    error.value = ''
    try {
        const { data } = await childrenService.create(form)
        router.push({ name: 'child-detail', params: { id: data.id } })
    } catch {
        error.value = t('common.error')
    } finally {
        loading.value = false
    }
}
</script>

<template>
    <DefaultLayout>
        <div class="max-w-lg mx-auto p-6">
            <h1 class="font-dynapuff font-bold text-2xl text-theme mb-6">
                {{ t('pages.child_create.title') }}
            </h1>

            <form class="bg-white rounded-2xl shadow-sm p-6 space-y-4" @submit.prevent="submit">
                <div>
                    <label class="block text-sm font-medium text-theme-muted mb-1">
                        {{ t('auth.register.first_name') }}*
                    </label>
                    <input v-model="form.first_name" type="text" required
                           class="w-full border border-theme rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]/40" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-theme-muted mb-1">
                        {{ t('auth.register.last_name') }}*
                    </label>
                    <input v-model="form.last_name" type="text" required
                           class="w-full border border-theme rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]/40" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-theme-muted mb-1">
                        {{ t('pages.child_create.birthdate') }}
                    </label>
                    <input v-model="form.birthdate" type="date"
                           class="w-full border border-theme rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]/40" />
                </div>

                <p v-if="error" class="text-sm text-red-500">{{ error }}</p>

                <div class="flex gap-3 pt-2">
                    <button type="button"
                            class="flex-1 py-2 rounded-xl border border-theme text-sm text-theme-muted"
                            @click="router.back()">
                        {{ t('common.cancel') }}
                    </button>
                    <button type="submit" :disabled="loading"
                            class="flex-1 btn-theme py-2 rounded-xl text-sm font-medium">
                        {{ loading ? t('common.loading') : t('common.save') }}
                    </button>
                </div>
            </form>
        </div>
    </DefaultLayout>
</template>
