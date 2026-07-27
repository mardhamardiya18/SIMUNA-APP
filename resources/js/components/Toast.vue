<script setup lang="ts">
import { usePage } from '@inertiajs/vue3'
import { CheckCircle2, AlertCircle, Info, X } from 'lucide-vue-next'
import { computed, ref, watch, onMounted, onUnmounted } from 'vue'

const page = usePage()
const visible = ref(false)
const toastMessage = ref('')
const toastType = ref<'success' | 'error' | 'info'>('error')
let timer: ReturnType<typeof setTimeout> | null = null

const flash = computed(() => page.props.flash as { success?: string; error?: string } | undefined)
const errors = computed(() => page.props.errors as Record<string, string> | undefined)

// Watch flash messages
watch(
    () => flash.value,
    (newFlash) => {
        if (newFlash?.success) {
            triggerToast(newFlash.success, 'success')
        } else if (newFlash?.error) {
            triggerToast(newFlash.error, 'error')
        }
    },
    { immediate: true, deep: true }
)

// Watch form validation errors
watch(
    () => errors.value,
    (newErrors) => {
        if (newErrors && Object.keys(newErrors).length > 0) {
            const firstErrorMsg = Object.values(newErrors)[0]
            if (firstErrorMsg) {
                triggerToast(firstErrorMsg, 'error')
            }
        }
    },
    { deep: true }
)

function triggerToast(msg: string, type: 'success' | 'error' | 'info' = 'error') {
    if (timer) clearTimeout(timer)
    toastMessage.value = msg
    toastType.value = type
    visible.value = true
    timer = setTimeout(() => {
        visible.value = false
    }, 4500)
}

function handleCustomToast(event: Event) {
    const customEvent = event as CustomEvent<{ message: string; type?: 'success' | 'error' | 'info' }>
    if (customEvent.detail?.message) {
        triggerToast(customEvent.detail.message, customEvent.detail.type || 'info')
    }
}

onMounted(() => {
    window.addEventListener('show-toast', handleCustomToast)
})

onUnmounted(() => {
    window.removeEventListener('show-toast', handleCustomToast)
})
</script>

<template>
    <Transition
        enter-active-class="transform transition duration-300 ease-out"
        enter-from-class="-translate-y-6 opacity-0 scale-95"
        enter-to-class="translate-y-0 opacity-100 scale-100"
        leave-active-class="transform transition duration-200 ease-in"
        leave-from-class="translate-y-0 opacity-100 scale-100"
        leave-to-class="-translate-y-6 opacity-0 scale-95"
    >
        <div
            v-if="visible && toastMessage"
            class="fixed top-5 left-1/2 -translate-x-1/2 z-50 max-w-md w-11/12 sm:w-full bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl rounded-2xl shadow-2xl p-4 flex items-start gap-3.5 border-2 transition"
            :class="{
                'border-emerald-500/50 shadow-emerald-500/10': toastType === 'success',
                'border-rose-500/50 shadow-rose-500/20 ring-4 ring-rose-500/10': toastType === 'error',
                'border-teal-500/50 shadow-teal-500/10': toastType === 'info'
            }"
        >
            <!-- Icon -->
            <div
                class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
                :class="{
                    'bg-emerald-100 text-emerald-600 dark:bg-emerald-950/80 dark:text-emerald-400': toastType === 'success',
                    'bg-rose-100 text-rose-600 dark:bg-rose-950/80 dark:text-rose-400 animate-bounce-short': toastType === 'error',
                    'bg-teal-100 text-teal-600 dark:bg-teal-950/80 dark:text-teal-400': toastType === 'info'
                }"
            >
                <CheckCircle2 v-if="toastType === 'success'" class="w-5 h-5" />
                <AlertCircle v-else-if="toastType === 'error'" class="w-5 h-5" />
                <Info v-else class="w-5 h-5" />
            </div>

            <!-- Text Content -->
            <div class="flex-1 min-w-0">
                <p
                    class="text-xs font-bold uppercase tracking-wider"
                    :class="{
                        'text-emerald-700 dark:text-emerald-400': toastType === 'success',
                        'text-rose-700 dark:text-rose-400': toastType === 'error',
                        'text-teal-700 dark:text-teal-400': toastType === 'info'
                    }"
                >
                    {{ toastType === 'error' ? 'Gagal / Perhatian' : toastType === 'success' ? 'Berhasil' : 'Informasi' }}
                </p>
                <p class="text-xs font-medium text-slate-800 dark:text-slate-100 mt-0.5 leading-snug break-words">
                    {{ toastMessage }}
                </p>
            </div>

            <!-- Close Button -->
            <button
                @click="visible = false"
                class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded-lg transition shrink-0 cursor-pointer"
            >
                <X class="w-4 h-4" />
            </button>
        </div>
    </Transition>
</template>
