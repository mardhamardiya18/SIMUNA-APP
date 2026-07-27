<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { Html5Qrcode } from 'html5-qrcode'
import { Camera, X, QrCode, AlertCircle } from 'lucide-vue-next'

const emit = defineEmits<{
    (e: 'scan', result: string): void
    (e: 'close'): void
}>()

const scannerId = 'qr-reader-container'
let html5QrcodeScanner: Html5Qrcode | null = null

const isCameraReady = ref(false)
const errorMessage = ref('')
const manualCode = ref('')

onMounted(async () => {
    try {
        html5QrcodeScanner = new Html5Qrcode(scannerId)
        const cameras = await Html5Qrcode.getCameras()

        if (cameras && cameras.length > 0) {
            // Prefer back camera ("environment") or fallback to first available camera
            const cameraConfig = { facingMode: 'environment' }

            await html5QrcodeScanner.start(
                cameraConfig,
                {
                    fps: 10,
                    qrbox: { width: 220, height: 220 },
                },
                (decodedText) => {
                    handleSuccess(decodedText)
                },
                () => {
                    // Ignore frame scanning errors
                }
            )
            isCameraReady.value = true
        } else {
            errorMessage.value = 'Kamera tidak ditemukan pada perangkat Anda.'
        }
    } catch (err: any) {
        errorMessage.value = 'Tidak dapat mengakses kamera. Pastikan izin kamera telah diberikan di peramban Web Anda.'
    }
})

function handleSuccess(code: string) {
    stopScanner()
    emit('scan', code.trim())
}

function handleManualSubmit() {
    if (manualCode.value.trim()) {
        handleSuccess(manualCode.value.trim())
    }
}

async function stopScanner() {
    if (html5QrcodeScanner && html5QrcodeScanner.isScanning) {
        try {
            await html5QrcodeScanner.stop()
            html5QrcodeScanner.clear()
        } catch (e) {
            // Ignore stop errors
        }
    }
}

onBeforeUnmount(() => {
    stopScanner()
})

function close() {
    stopScanner()
    emit('close')
}
</script>

<template>
    <div class="fixed inset-0 z-50 bg-slate-900/80 backdrop-blur-md flex items-center justify-center p-4">
        <div class="max-w-md w-full bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-2xl relative border border-slate-200 dark:border-slate-800 text-center">
            <!-- Close Button -->
            <button
                @click="close"
                class="absolute top-4 right-4 p-2 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 transition"
                title="Tutup Scanner"
            >
                <X class="w-5 h-5" />
            </button>

            <!-- Header -->
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 text-xs font-semibold mb-2">
                <Camera class="w-3.5 h-3.5" /> Scanner QR Code Posyandu
            </div>
            <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">
                Pindai Kartu Imunisasi
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">
                Arahkan kamera perangkat ke QR Code pada kartu responden.
            </p>

            <!-- Scanner Viewport Area -->
            <div class="relative w-full aspect-square bg-slate-950 rounded-2xl overflow-hidden shadow-inner border-2 border-emerald-500/50 flex flex-col items-center justify-center mb-4">
                <div :id="scannerId" class="w-full h-full object-cover"></div>

                <!-- Scanning Animation Line -->
                <div v-if="isCameraReady && !errorMessage" class="absolute inset-0 pointer-events-none flex flex-col items-center justify-center">
                    <div class="w-56 h-56 border-2 border-dashed border-emerald-400/80 rounded-2xl relative overflow-hidden">
                        <div class="w-full h-1 bg-gradient-to-r from-transparent via-emerald-400 to-transparent shadow-lg shadow-emerald-500 animate-pulse"></div>
                    </div>
                </div>

                <!-- Error Fallback Message -->
                <div v-if="errorMessage" class="absolute inset-0 bg-slate-900/95 flex flex-col items-center justify-center p-6 text-center text-rose-400">
                    <AlertCircle class="w-10 h-10 mb-2 text-rose-500" />
                    <p class="text-xs font-semibold mb-3">{{ errorMessage }}</p>
                    <span class="text-[11px] text-slate-400">Silakan gunakan pencarian ID Unik di bawah ini.</span>
                </div>
            </div>

            <!-- Manual Input Fallback -->
            <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
                <span class="text-[11px] text-slate-400 font-semibold block mb-2">Atau masukkan ID Unik / Kode secara manual:</span>
                <form @submit.prevent="handleManualSubmit" class="flex gap-2">
                    <input
                        v-model="manualCode"
                        type="text"
                        placeholder="Contoh: SMN-002"
                        class="flex-1 px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 font-mono font-bold text-slate-900 dark:text-white"
                    />
                    <button
                        type="submit"
                        class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition cursor-pointer"
                    >
                        Proses
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
