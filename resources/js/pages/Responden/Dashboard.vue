<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import Navbar from '@/components/Navbar.vue'
import Toast from '@/components/Toast.vue'
import QrCodeCard from '@/components/QrCodeCard.vue'
import {
    HeartPulse, CheckCircle2, Clock, ShieldCheck, MapPin, Phone,
    Calendar, Baby, FileText, ChevronRight, User, AlertCircle, Sparkles
} from 'lucide-vue-next'
import { ref } from 'vue'

const props = defineProps<{
    user: { name: string; email: string; phone: string; unique_code: string }
    record?: {
        child_name: string
        head_of_family: string
        gender: string
        age_text: string
        birth_date: string
        address: string
        immunization_status: string
        immunization_types: string[]
        incomplete_reason?: string
    }
    completedList: Array<{ name: string; code: string; category: string; recommended_age: string; description?: string }>
    upcomingList: Array<{ name: string; code: string; category: string; recommended_age: string; description?: string }>
    allImmunizations: Array<{ name: string; code: string; category: string; recommended_age: string; description?: string }>
    puskesmasInfo: {
        name: string
        address: string
        phone: string
        hours: string
        midwife: string
        posyandu_schedule: string
    }
}>()

const activeTab = ref<'completed' | 'upcoming' | 'all' | 'puskesmas'>('completed')
const showQrModal = ref(false)
</script>

<template>
    <Head title="Dashboard Imunisasi Saya - SIMUNA" />
    <Toast />

    <div class="min-h-screen bg-gradient-to-br from-emerald-50/60 via-slate-50 to-teal-50/40 dark:from-slate-950 dark:via-slate-900 dark:to-emerald-950/30 flex flex-col text-slate-800 dark:text-slate-100 w-full overflow-x-hidden">
        <Navbar />

        <main class="flex-1 py-4 sm:py-8 px-3.5 sm:px-6 max-w-5xl mx-auto w-full">
            <!-- Header Welcome Banner -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-emerald-100 dark:border-slate-800 shadow-xl mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 text-xs font-semibold mb-2">
                        <Sparkles class="w-3.5 h-3.5" /> Dashboard Resmi Responden Posyandu
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                        Halo, {{ user.name }} 👋
                    </h1>

                    <p v-if="record" class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mt-1 flex items-center gap-2">
                        <span>Anak: <strong class="text-slate-900 dark:text-white">{{ record.child_name }}</strong> ({{ record.age_text }})</span>
                        <span class="text-slate-300">•</span>
                        <span class="capitalize font-semibold text-emerald-600 dark:text-emerald-400">
                            Status: {{ record.immunization_status }}
                        </span>
                    </p>
                    <p v-else class="text-xs text-amber-600 dark:text-amber-400 mt-1">
                        Anda belum mengisi formulir imunisasi anak.
                    </p>
                </div>

                <!-- Right Card: ID Barcode Action -->
                <div class="bg-gradient-to-br from-emerald-600 to-teal-600 text-white rounded-2xl p-4 shadow-lg shadow-emerald-600/20 shrink-0 w-full md:w-auto flex items-center justify-between md:justify-start gap-4">
                    <div>
                        <span class="text-[10px] text-emerald-200 uppercase tracking-widest font-semibold block">ID Verifikasi</span>
                        <span class="text-xl font-black font-mono tracking-wider">{{ user.unique_code }}</span>
                    </div>

                    <button
                        @click="showQrModal = true"
                        class="px-3.5 py-2 rounded-xl bg-white/20 hover:bg-white/30 backdrop-blur-md text-xs font-bold transition flex items-center gap-1.5 cursor-pointer"
                    >
                        <span>Tampilkan QR</span>
                        <ChevronRight class="w-4 h-4" />
                    </button>
                </div>
            </div>

            <!-- Warning if no record filled -->
            <div v-if="!record" class="mb-6 p-4 rounded-3xl bg-amber-50 dark:bg-amber-950/50 border border-amber-200 dark:border-amber-900 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <AlertCircle class="w-6 h-6 text-amber-600 shrink-0" />
                    <div>
                        <p class="text-sm font-bold text-amber-900 dark:text-amber-200">Form Imunisasi Belum Diisi</p>
                        <p class="text-xs text-amber-700 dark:text-amber-400">Silakan isi formulir rekap kelengkapan imunisasi si kecil terlebih dahulu.</p>
                    </div>
                </div>
                <Link href="/form" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold transition shrink-0">
                    Isi Form Now
                </Link>
            </div>

            <!-- Quick Overview Stats Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                        <CheckCircle2 class="w-6 h-6" />
                    </div>
                    <div>
                        <span class="text-2xl font-black text-slate-900 dark:text-white">{{ completedList.length }}</span>
                        <span class="text-xs text-slate-500 block">Sudah Dilakukan</span>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 dark:bg-amber-950 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                        <Clock class="w-6 h-6" />
                    </div>
                    <div>
                        <span class="text-2xl font-black text-slate-900 dark:text-white">{{ upcomingList.length }}</span>
                        <span class="text-xs text-slate-500 block">Terjadwal / Belum</span>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-teal-100 dark:bg-teal-950 text-teal-600 dark:text-teal-400 flex items-center justify-center shrink-0">
                        <HeartPulse class="w-6 h-6" />
                    </div>
                    <div>
                        <span class="text-2xl font-black text-slate-900 dark:text-white">{{ allImmunizations.length }}</span>
                        <span class="text-xs text-slate-500 block">Jenis Vaksin</span>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900 p-5 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-sky-100 dark:bg-sky-950 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0">
                        <Baby class="w-6 h-6" />
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-900 dark:text-white capitalize block truncate max-w-[100px]">
                            {{ record?.child_name || '-' }}
                        </span>
                        <span class="text-[11px] text-slate-500 block">{{ record?.gender || 'Belum diisi' }}</span>
                    </div>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <div class="flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 mb-6 overflow-x-auto pb-1 scrollbar-none">
                <button
                    @click="activeTab = 'completed'"
                    class="px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-semibold transition flex items-center gap-2 whitespace-nowrap cursor-pointer"
                    :class="activeTab === 'completed' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'"
                >
                    <CheckCircle2 class="w-4 h-4" />
                    <span>Sudah Dilakukan ({{ completedList.length }})</span>
                </button>

                <button
                    @click="activeTab = 'upcoming'"
                    class="px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-semibold transition flex items-center gap-2 whitespace-nowrap cursor-pointer"
                    :class="activeTab === 'upcoming' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'"
                >
                    <Clock class="w-4 h-4" />
                    <span>Terjadwal / Belum ({{ upcomingList.length }})</span>
                </button>

                <button
                    @click="activeTab = 'all'"
                    class="px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-semibold transition flex items-center gap-2 whitespace-nowrap cursor-pointer"
                    :class="activeTab === 'all' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'"
                >
                    <HeartPulse class="w-4 h-4" />
                    <span>Jenis Imunisasi Tersedia</span>
                </button>

                <button
                    @click="activeTab = 'puskesmas'"
                    class="px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-semibold transition flex items-center gap-2 whitespace-nowrap cursor-pointer"
                    :class="activeTab === 'puskesmas' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'"
                >
                    <ShieldCheck class="w-4 h-4" />
                    <span>Info Puskesmas</span>
                </button>
            </div>

            <!-- Tab Content 1: Sudah Dilakukan -->
            <div v-if="activeTab === 'completed'" class="space-y-4">
                <div v-if="completedList.length === 0" class="bg-white dark:bg-slate-900 rounded-3xl p-8 text-center border border-slate-100 dark:border-slate-800">
                    <Clock class="w-12 h-12 text-slate-300 mx-auto mb-2" />
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Belum ada riwayat imunisasi yang dicentang</p>
                    <p class="text-xs text-slate-500 mt-1">Silakan perbarui form imunisasi Anda jika ada vaksin yang sudah pernah diberikan.</p>
                    <Link href="/form" class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold">
                        <FileText class="w-4 h-4" /> Edit Form Imunisasi
                    </Link>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div
                        v-for="vax in completedList"
                        :key="vax.code"
                        class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-emerald-100 dark:border-emerald-950/80 shadow-xs flex items-start justify-between gap-3"
                    >
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 mt-0.5">
                                <CheckCircle2 class="w-5 h-5" />
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ vax.name }}</h3>
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 font-mono font-bold">{{ vax.code }}</span>
                                </div>
                                <p class="text-xs text-slate-500 mt-1">{{ vax.description }}</p>
                                <span class="inline-block text-[10px] text-slate-400 mt-2 font-medium">Usia Rekomendasi: {{ vax.recommended_age }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Content 2: Terjadwal / Belum -->
            <div v-if="activeTab === 'upcoming'" class="space-y-4">
                <div v-if="upcomingList.length === 0" class="bg-emerald-50 dark:bg-emerald-950/40 rounded-3xl p-8 text-center border border-emerald-200 dark:border-emerald-900">
                    <CheckCircle2 class="w-12 h-12 text-emerald-600 mx-auto mb-2" />
                    <h3 class="text-base font-bold text-emerald-900 dark:text-emerald-200">Selamat! Imunisasi Sudah Lengkap 🎉</h3>
                    <p class="text-xs text-emerald-700 dark:text-emerald-400 mt-1">Semua jenis vaksin wajib posyandu telah diselesaikan dengan baik.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div
                        v-for="vax in upcomingList"
                        :key="vax.code"
                        class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-amber-100 dark:border-amber-950/60 shadow-xs flex items-start justify-between gap-3"
                    >
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-950 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 mt-0.5">
                                <Clock class="w-5 h-5" />
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ vax.name }}</h3>
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 font-mono font-bold">{{ vax.code }}</span>
                                </div>
                                <p class="text-xs text-slate-500 mt-1">{{ vax.description }}</p>
                                <div class="mt-2 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300 text-[11px] font-semibold">
                                    <Calendar class="w-3.5 h-3.5" />
                                    Jadwal Usia: {{ vax.recommended_age }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Content 3: Semua Vaksin Tersedia -->
            <div v-if="activeTab === 'all'" class="space-y-4">
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-100 dark:border-slate-800 shadow-sm">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-1">Katalog Imunisasi Wajib & Lanjutan Posyandu</h3>
                    <p class="text-xs text-slate-500 mb-6">Daftar vaksin standar yang disediakan oleh Puskesmas SIMUNA</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div
                            v-for="vax in allImmunizations"
                            :key="vax.code"
                            class="p-4 rounded-2xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40"
                        >
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 font-mono">{{ vax.code }}</span>
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold">{{ vax.category }}</span>
                            </div>
                            <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100 mt-1">{{ vax.name }}</h4>
                            <p class="text-xs text-slate-500 mt-1">{{ vax.description }}</p>
                            <div class="mt-2 text-[11px] text-teal-600 dark:text-teal-400 font-semibold">
                                Recommended: {{ vax.recommended_age }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Content 4: Info Puskesmas -->
            <div v-if="activeTab === 'puskesmas'" class="space-y-6">
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-emerald-100 dark:border-slate-800 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                        <ShieldCheck class="w-6 h-6 text-emerald-600" />
                        Layanan Layanan & Hotline Puskesmas SIMUNA
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                                    <MapPin class="w-5 h-5" />
                                </div>
                                <div>
                                    <span class="text-xs font-semibold text-slate-400 block">Alamat Puskesmas</span>
                                    <span class="text-sm font-bold text-slate-900 dark:text-white">{{ puskesmasInfo.name }}</span>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ puskesmasInfo.address }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-teal-100 text-teal-600 flex items-center justify-center shrink-0">
                                    <Clock class="w-5 h-5" />
                                </div>
                                <div>
                                    <span class="text-xs font-semibold text-slate-400 block">Jam Operasional Imunisasi</span>
                                    <span class="text-sm font-bold text-slate-900 dark:text-white">{{ puskesmasInfo.hours }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                                    <Phone class="w-5 h-5" />
                                </div>
                                <div>
                                    <span class="text-xs font-semibold text-slate-400 block">Hotline WhatsApp Puskesmas</span>
                                    <a :href="`https://wa.me/${puskesmasInfo.phone.replace(/[^0-9]/g, '')}`" target="_blank" class="text-sm font-bold text-emerald-600 hover:underline">
                                        {{ puskesmasInfo.phone }}
                                    </a>
                                    <p class="text-xs text-slate-500 mt-0.5">Penanggung Jawab: {{ puskesmasInfo.midwife }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                                    <Calendar class="w-5 h-5" />
                                </div>
                                <div>
                                    <span class="text-xs font-semibold text-slate-400 block">Jadwal Posyandu Rutin</span>
                                    <span class="text-sm font-bold text-slate-900 dark:text-white">{{ puskesmasInfo.posyandu_schedule }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Modal QR Barcode ID -->
        <div v-if="showQrModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="max-w-sm w-full bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-2xl relative">
                <button @click="showQrModal = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 text-xl font-bold">✕</button>

                <QrCodeCard
                    :unique-code="user.unique_code"
                    :child-name="record?.child_name"
                    :head-of-family="record?.head_of_family"
                    :size="200"
                />

                <button @click="showQrModal = false" class="w-full mt-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-semibold text-xs">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</template>
