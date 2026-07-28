<script setup lang="ts">
import QrcodeVue from 'qrcode.vue'
import { QrCode, ShieldCheck, Sparkles } from 'lucide-vue-next'

const props = defineProps<{
    uniqueCode: string
    childName?: string
    headOfFamily?: string
    size?: number
}>()
</script>

<template>
    <div class="bg-gradient-to-b from-white to-emerald-50/40 dark:from-slate-800/90 dark:to-slate-900 border border-emerald-200/80 dark:border-slate-700/80 rounded-3xl p-5 text-center shadow-lg shadow-emerald-500/5 relative overflow-hidden flex flex-col items-center justify-center w-full">
        <!-- Background Decorative Pill -->
        <div class="absolute -top-12 -right-12 w-28 h-28 bg-emerald-400/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 text-xs font-semibold mb-4">
            <Sparkles class="w-3.5 h-3.5" />
            Kartu Verifikasi Posyandu
        </div>

        <!-- QR Code Frame -->
        <div class="bg-white p-4 rounded-2xl inline-block shadow-md border border-slate-100 mx-auto my-2">
            <QrcodeVue
                :value="uniqueCode"
                :size="size || 160"
                level="H"
                render-as="svg"
                margin="2"
                foreground="#0f172a"
                background="#ffffff"
            />
        </div>

        <div class="mt-3">
            <span class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-widest font-semibold block">ID UNIK RESPONDEN</span>
            <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 tracking-wider font-mono my-1">
                {{ uniqueCode }}
            </div>
            <p v-if="childName" class="text-sm font-medium text-slate-700 dark:text-slate-300">
                Anak: <span class="font-bold text-slate-900 dark:text-white">{{ childName }}</span>
            </p>
            <p v-if="headOfFamily" class="text-xs text-slate-500 dark:text-slate-400">
                KK: {{ headOfFamily }}
            </p>
        </div>

        <div class="mt-4 pt-3 border-t border-dashed border-emerald-200 dark:border-slate-700 flex items-center justify-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400">
            <ShieldCheck class="w-4 h-4 text-emerald-500 shrink-0" />
            Tunjukkan kode ini ke petugas Puskesmas Bulusan saat verifikasi offline
        </div>
    </div>
</template>
