<script setup>
import { reactive, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { childrenService } from '@/src/services/children.service'

const { t } = useI18n()

const props = defineProps({
    child: { type: Object, required: true },
})

const emit = defineEmits(['close', 'updated'])

const form = reactive({
    first_name: props.child.first_name,
    last_name:  props.child.last_name,
    birthdate:  props.child.birthdate
        ? props.child.birthdate.split('T')[0]
        : '',
})

const loading = ref(false)
const error   = ref('')

async function handleSubmit() {
    error.value = ''
    loading.value = true
    try {
        await childrenService.update(props.child.id, form)
        emit('updated', { ...form })
    } catch {
        error.value = t('common.error')
    } finally {
        loading.value = false
    }
}
</script>

<template>
    <div
        class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
        @click.self="emit('close')"
    >
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">

            <div class="flex items-center justify-between p-6 border-b border-theme">
                <h2 class="font-dynapuff font-bold text-lg text-theme">
                    {{ t('pages.child_detail.edit.title') }}
                </h2>
                <button
                    class="text-gray-400 hover:text-gray-600 transition-colors"
                    @click="emit('close')"
                >
                    ✕
                </button>
            </div>

            <form class="p-6 space-y-4" @submit.prevent="handleSubmit">

                <div>
                    <label class="block text-sm font-medium text-theme-muted mb-1">
                        {{ t('pages.children.form.first_name') }}
                    </label>
                    <input
                        v-model="form.first_name"
                        type="text"
                        required
                        class="w-full border border-theme rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]/40"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-theme-muted mb-1">
                        {{ t('pages.children.form.last_name') }}
                    </label>
                    <input
                        v-model="form.last_name"
                        type="text"
                        required
                        class="w-full border border-theme rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]/40"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-theme-muted mb-1">
                        {{ t('pages.children.form.birth_date') }}
                    </label>
                    <input
                        v-model="form.birthdate"
                        type="date"
                        class="w-full border border-theme rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]/40"
                    />
                </div>

                <p v-if="error" class="text-sm text-red-500">{{ error }}</p>

                <div class="flex gap-3 pt-2">
                    <button
                        type="button"
                        class="flex-1 py-2 rounded-xl border border-theme text-sm text-theme-muted"
                        @click="emit('close')"
                    >
                        {{ t('common.cancel') }}
                    </button>
                    <button
                        type="submit"
                        class="flex-1 btn-theme py-2 rounded-xl text-sm font-medium"
                        :disabled="loading"
                    >
                        {{ loading ? t('common.loading') : t('pages.children.form.submit_edit') }}
                    </button>
                </div>

            </form>
        </div>
    </div>
</template>
