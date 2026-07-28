<script setup lang="ts">
import { usePage } from '@inertiajs/vue3'
import { CheckCircle2, AlertTriangle, AlertCircle, Info, X } from 'lucide-vue-next'
import { computed, ref, watch, onMounted, onUnmounted } from 'vue'

const page = usePage()
const visible = ref(false)
const toastMessage = ref('')
const toastTitle = ref('')
const toastType = ref<'success' | 'error' | 'warning' | 'info'>('success')
const progressWidth = ref(100)

let timer: ReturnType<typeof setTimeout> | null = null
let interval: ReturnType<typeof setInterval> | null = null

const flash = computed(() => page.props.flash as { success?: string; error?: string; warning?: string; info?: string; message?: string } | undefined)
const errors = computed(() => page.props.errors as Record<string, string> | undefined)

// Watch flash messages
watch(
    () => flash.value,
    (newFlash) => {
        if (newFlash?.success) {
            triggerToast(newFlash.success, 'success', 'Berhasil!')
        } else if (newFlash?.error) {
            triggerToast(newFlash.error, 'error', 'Gagal')
        } else if (newFlash?.warning) {
            triggerToast(newFlash.warning, 'warning', 'Perhatian!')
        } else if (newFlash?.info || newFlash?.message) {
            triggerToast(newFlash.info || newFlash.message || '', 'info', 'Informasi')
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
                triggerToast(firstErrorMsg, 'error', 'Validasi Gagal')
            }
        }
    },
    { deep: true }
)

function triggerToast(msg: string, type: 'success' | 'error' | 'warning' | 'info' = 'info', customTitle?: string) {
    if (timer) clearTimeout(timer)
    if (interval) clearInterval(interval)

    toastMessage.value = msg
    toastType.value = type

    if (customTitle) {
        toastTitle.value = customTitle
    } else {
        switch (type) {
            case 'success': toastTitle.value = 'Berhasil!'; break;
            case 'error': toastTitle.value = 'Terjadi Kesalahan'; break;
            case 'warning': toastTitle.value = 'Peringatan'; break;
            default: toastTitle.value = 'Informasi'; break;
        }
    }

    progressWidth.value = 100
    visible.value = true

    const duration = 4000
    const updateRate = 40
    const decrement = (100 / (duration / updateRate))

    interval = setInterval(() => {
        progressWidth.value = Math.max(0, progressWidth.value - decrement)
    }, updateRate)

    timer = setTimeout(() => {
        visible.value = false
        if (interval) clearInterval(interval)
    }, duration)
}

function handleCustomToast(event: Event) {
    const customEvent = event as CustomEvent<{ message: string; type?: 'success' | 'error' | 'warning' | 'info'; title?: string }>
    if (customEvent.detail?.message) {
        triggerToast(customEvent.detail.message, customEvent.detail.type || 'info', customEvent.detail.title)
    }
}

onMounted(() => {
    window.addEventListener('show-toast', handleCustomToast)
})

onUnmounted(() => {
    window.removeEventListener('show-toast', handleCustomToast)
    if (timer) clearTimeout(timer)
    if (interval) clearInterval(interval)
})
</script>

<template>
    <Transition
        enter-active-class="transform transition duration-300 ease-out"
        enter-from-class="-translate-y-10 opacity-0 scale-90"
        enter-to-class="translate-y-0 opacity-100 scale-100"
        leave-active-class="transform transition duration-200 ease-in"
        leave-from-class="translate-y-0 opacity-100 scale-100"
        leave-to-class="-translate-y-10 opacity-0 scale-90"
    >
        <div
            v-if="visible && toastMessage"
            class="fixed top-5 left-1/2 -translate-x-1/2 z-50 max-w-md w-[92vw] sm:w-[420px] bg-white/95 dark:bg-slate-900/95 backdrop-blur-2xl rounded-3xl shadow-2xl overflow-hidden border-2 transition-all duration-300"
            :class="{
                'border-emerald-500/80 shadow-emerald-500/20 ring-4 ring-emerald-500/10': toastType === 'success',
                'border-rose-500/80 shadow-rose-500/20 ring-4 ring-rose-500/10': toastType === 'error',
                'border-amber-500/80 shadow-amber-500/20 ring-4 ring-amber-500/10': toastType === 'warning',
                'border-teal-500/80 shadow-teal-500/20 ring-4 ring-teal-500/10': toastType === 'info'
            }"
        >
            <div class="p-4 sm:p-5 flex items-start gap-3.5 relative">
                <!-- SweetAlert Style Icon -->
                <div
                    class="w-11 h-11 rounded-2xl flex items-center justify-center shrink-0 shadow-lg transition-transform duration-300 scale-105"
                    :class="{
                        'bg-gradient-to-tr from-emerald-600 to-teal-400 text-white shadow-emerald-500/30': toastType === 'success',
                        'bg-gradient-to-tr from-rose-600 to-red-400 text-white shadow-rose-500/30 animate-pulse': toastType === 'error',
                        'bg-gradient-to-tr from-amber-500 to-orange-400 text-white shadow-amber-500/30': toastType === 'warning',
                        'bg-gradient-to-tr from-teal-600 to-cyan-400 text-white shadow-teal-500/30': toastType === 'info'
                    }"
                >
                    <CheckCircle2 v-if="toastType === 'success'" class="w-6 h-6 stroke-[2.5]" />
                    <AlertCircle v-else-if="toastType === 'error'" class="w-6 h-6 stroke-[2.5]" />
                    <AlertTriangle v-else-if="toastType === 'warning'" class="w-6 h-6 stroke-[2.5]" />
                    <Info v-else class="w-6 h-6 stroke-[2.5]" />
                </div>

                <!-- Text Content -->
                <div class="flex-1 min-w-0 pr-6">
                    <h4
                        class="text-xs sm:text-sm font-extrabold tracking-tight flex items-center gap-1.5"
                        :class="{
                            'text-emerald-700 dark:text-emerald-300': toastType === 'success',
                            'text-rose-700 dark:text-rose-300': toastType === 'error',
                            'text-amber-700 dark:text-amber-300': toastType === 'warning',
                            'text-teal-700 dark:text-teal-300': toastType === 'info'
                        }"
                    >
                        {{ toastTitle }}
                    </h4>
                    <p class="text-xs font-medium text-slate-700 dark:text-slate-200 mt-1 leading-snug break-words">
                        {{ toastMessage }}
                    </p>
                </div>

                <!-- Close Button -->
                <button
                    @click="visible = false"
                    class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer"
                >
                    <X class="w-4 h-4" />
                </button>
            </div>

            <!-- SweetAlert Countdown Progress Bar -->
            <div class="w-full h-1.5 bg-slate-100 dark:bg-slate-800 overflow-hidden">
                <div
                    class="h-full transition-all ease-linear"
                    :style="{ width: `${progressWidth}%` }"
                    :class="{
                        'bg-emerald-500': toastType === 'success',
                        'bg-rose-500': toastType === 'error',
                        'bg-amber-500': toastType === 'warning',
                        'bg-teal-500': toastType === 'info'
                    }"
                ></div>
            </div>
        </div>
    </Transition>
</template>
