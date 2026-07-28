<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import Navbar from '@/components/Navbar.vue'
import Toast from '@/components/Toast.vue'
import QrCodeCard from '@/components/QrCodeCard.vue'
import {
    CheckCircle2, ArrowRight, LayoutDashboard, FileText,
    Baby, Phone, MapPin, Calendar, HeartPulse, Sparkles
} from 'lucide-vue-next'

import { onMounted } from 'vue'

const props = defineProps<{
    user: { name: string; email: string; phone: string; unique_code: string }
    record: {
        id: number
        child_name: string
        head_of_family: string
        mother_name: string
        gender: string
        age_text: string
        birth_date: string
        address: string
        phone: string
        immunization_status: string
        immunization_types: string[]
        incomplete_reason?: string
    }
}>()

onMounted(() => {
    window.scrollTo({ top: 0, left: 0, behavior: 'instant' })
})
</script>

<template>
    <Head title="Konfirmasi Rekap Imunisasi - SIMUNA" />
    <Toast />

    <div class="min-h-screen bg-gradient-to-br from-emerald-50/60 via-slate-50 to-teal-50/40 dark:from-slate-950 dark:via-slate-900 dark:to-emerald-950/30 flex flex-col text-slate-800 dark:text-slate-100 w-full overflow-x-hidden">
        <Navbar />

        <main class="flex-1 py-4 sm:py-8 px-3.5 sm:px-6 max-w-3xl mx-auto w-full">
            <!-- Success Hero Banner -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-emerald-100 dark:border-slate-800 shadow-xl text-center relative overflow-hidden mb-6">
                <div class="w-16 h-16 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto mb-4 animate-bounce">
                    <CheckCircle2 class="w-10 h-10" />
                </div>

                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold mb-2">
                    <Sparkles class="w-3.5 h-3.5" /> Data Berhasil Tersimpan
                </span>

                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                    Terima Kasih, {{ record.head_of_family }}!
                </h1>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mt-1 max-w-md mx-auto">
                    Data kelengkapan imunisasi <span class="font-bold text-slate-900 dark:text-white">{{ record.child_name }}</span> telah tersimpan di database Puskesmas Bulusan.
                </p>

                <!-- Unique ID & Barcode QR Code Card -->
                <div class="mt-8 max-w-sm mx-auto">
                    <QrCodeCard
                        :unique-code="user.unique_code"
                        :child-name="record.child_name"
                        :head-of-family="record.head_of_family"
                        :size="170"
                    />
                </div>

                <!-- CTA Button to Dashboard -->
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                    <Link
                        href="/dashboard"
                        class="w-full sm:w-auto px-8 py-3.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-sm shadow-lg shadow-emerald-600/30 transition flex items-center justify-center gap-2"
                    >
                        <LayoutDashboard class="w-4 h-4" />
                        <span>Menuju Dashboard Saya</span>
                        <ArrowRight class="w-4 h-4" />
                    </Link>

                    <Link
                        href="/form"
                        class="w-full sm:w-auto px-6 py-3.5 rounded-2xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-sm transition flex items-center justify-center gap-2"
                    >
                        <FileText class="w-4 h-4" />
                        <span>Edit Form</span>
                    </Link>
                </div>
            </div>

            <!-- Details Summary Card -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-100 dark:border-slate-800 shadow-md">
                <h2 class="text-base font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                    <HeartPulse class="w-5 h-5 text-emerald-600" />
                    Rincian Rekap Data Imunisasi
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700">
                        <span class="text-slate-400 block mb-0.5">Nama Anak</span>
                        <span class="font-bold text-sm text-slate-800 dark:text-slate-100">{{ record.child_name }}</span>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700">
                        <span class="text-slate-400 block mb-0.5">Jenis Kelamin</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-100 capitalize">
                            {{ record.gender === 'laki-laki' ? '👦 Laki-laki' : '👧 Perempuan' }}
                        </span>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700">
                        <span class="text-slate-400 block mb-0.5">Usia & Tgl Lahir</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-100">
                            {{ record.age_text }} ({{ record.birth_date }})
                        </span>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700">
                        <span class="text-slate-400 block mb-0.5">Status Imunisasi</span>
                        <span
                            class="inline-block font-bold px-2 py-0.5 rounded-full capitalize"
                            :class="record.immunization_status === 'lengkap' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'"
                        >
                            {{ record.immunization_status }}
                        </span>
                    </div>
                </div>

                <!-- Vaksin Diberikan -->
                <div class="mt-4 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700">
                    <span class="text-xs text-slate-400 block mb-2 font-semibold">Vaksin yang Sudah Diterima:</span>
                    <div class="flex flex-wrap gap-1.5">
                        <span
                            v-for="type in record.immunization_types"
                            :key="type"
                            class="px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 text-xs font-semibold"
                        >
                            ✓ {{ type }}
                        </span>
                        <span v-if="!record.immunization_types || record.immunization_types.length === 0" class="text-xs text-slate-400">
                            Belum ada vaksin yang dipilih
                        </span>
                    </div>
                </div>

                <div v-if="record.incomplete_reason" class="mt-3 p-3.5 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 text-xs">
                    <span class="font-bold text-amber-800 dark:text-amber-300 block">Alasan Belum Lengkap:</span>
                    <p class="text-amber-700 dark:text-amber-400 mt-0.5">{{ record.incomplete_reason }}</p>
                </div>
            </div>
        </main>
    </div>
</template>
