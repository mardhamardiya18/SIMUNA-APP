<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import Navbar from '@/components/Navbar.vue'
import Toast from '@/components/Toast.vue'
import QrCodeCard from '@/components/QrCodeCard.vue'
import QrScannerModal from '@/components/QrScannerModal.vue'
import {
    ShieldCheck, Users, CheckCircle2, AlertCircle, Search,
    Filter, Trash2, Eye, Baby, Phone, MapPin, Download, RefreshCw, X, QrCode
} from 'lucide-vue-next'
import { ref, watch } from 'vue'

const props = defineProps<{
    records: {
        data: Array<{
            id: number
            child_name: string
            head_of_family: string
            mother_name: string
            father_job?: string
            mother_job?: string
            gender: string
            age_text: string
            birth_date: string
            address: string
            phone: string
            email?: string
            immunization_status: string
            immunization_types: string[]
            incomplete_reason?: string
            created_at: string
            user?: {
                id: number
                name: string
                email: string
                unique_code: string
            }
        }>
        links: Array<{ url: string | null; label: string; active: boolean }>
    }
    stats: {
        total_respondents: number
        total_users: number
        complete_count: number
        incomplete_count: number
        male_count: number
        female_count: number
    }
    filters: {
        search: string
        status: string
    }
}>()

const searchInput = ref(props.filters.search || '')
const statusFilter = ref(props.filters.status || 'all')
const selectedRecord = ref<any>(null)
const isScannerOpen = ref(false)

let debounceTimer: ReturnType<typeof setTimeout> | null = null

function applyFilters() {
    router.get(
        '/admin/dashboard',
        {
            search: searchInput.value,
            status: statusFilter.value,
        },
        { preserveState: true, replace: true }
    )
}

watch([searchInput, statusFilter], () => {
    if (debounceTimer) clearTimeout(debounceTimer)
    debounceTimer = setTimeout(() => {
        applyFilters()
    }, 300)
})

function handleScanResult(code: string) {
    isScannerOpen.value = false
    const cleanCode = code.trim().toLowerCase()

    const match = props.records.data.find(rec =>
        (rec.user?.unique_code && rec.user.unique_code.toLowerCase() === cleanCode) ||
        rec.child_name.toLowerCase().includes(cleanCode) ||
        rec.phone.includes(cleanCode)
    )

    if (match) {
        selectedRecord.value = match
    } else {
        searchInput.value = code
        router.get(
            '/admin/dashboard',
            { search: code, status: statusFilter.value },
            {
                preserveState: true,
                replace: true,
                onSuccess: (page) => {
                    const newRecords = (page.props.records as any)?.data || []
                    if (newRecords.length > 0) {
                        selectedRecord.value = newRecords[0]
                    }
                }
            }
        )
    }
}

function resetFilters() {
    searchInput.value = ''
    statusFilter.value = 'all'
    applyFilters()
}

function deleteRecord(id: number, name: string) {
    if (confirm(`Apakah Anda yakin ingin menghapus data responden untuk "${name}"?`)) {
        router.delete(`/admin/respondents/${id}`, {
            onSuccess: () => {
                if (selectedRecord.value?.id === id) {
                    selectedRecord.value = null
                }
            },
        })
    }
}
</script>

<template>
    <Head title="Panel Admin - SIMUNA" />
    <Toast />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950 flex flex-col text-slate-800 dark:text-slate-100">
        <Navbar />

        <main class="flex-1 py-8 px-4 sm:px-6 max-w-7xl mx-auto w-full">
            <!-- Header Title -->
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-8">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300 text-xs font-semibold mb-2">
                        <ShieldCheck class="w-3.5 h-3.5" /> Panel Petugas Puskesmas SIMUNA
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                        Rekapitulasi Data Responden
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Kelola data survei kelengkapan imunisasi anak & verifikasi ID posyandu secara offline.
                    </p>
                </div>
            </div>

            <!-- Statistics Overview Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
                <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                    <span class="text-xs text-slate-400 font-semibold block">Total Responden</span>
                    <span class="text-2xl font-black text-slate-900 dark:text-white mt-1 block">{{ stats.total_respondents }}</span>
                    <span class="text-[10px] text-slate-500">Form Terisi</span>
                </div>

                <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                    <span class="text-xs text-slate-400 font-semibold block">Akun Terdaftar</span>
                    <span class="text-2xl font-black text-teal-600 dark:text-teal-400 mt-1 block">{{ stats.total_users }}</span>
                    <span class="text-[10px] text-slate-500">User Ortu</span>
                </div>

                <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                    <span class="text-xs text-emerald-600 font-semibold block">Imunisasi Lengkap</span>
                    <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1 block">{{ stats.complete_count }}</span>
                    <span class="text-[10px] text-emerald-600 font-medium">Lengkap Sesuai Usia</span>
                </div>

                <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                    <span class="text-xs text-amber-600 font-semibold block">Tidak Lengkap</span>
                    <span class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1 block">{{ stats.incomplete_count }}</span>
                    <span class="text-[10px] text-amber-600 font-medium">Perlu Pengawasan</span>
                </div>

                <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                    <span class="text-xs text-sky-600 font-semibold block">Anak Laki-Laki</span>
                    <span class="text-2xl font-black text-sky-600 dark:text-sky-400 mt-1 block">{{ stats.male_count }}</span>
                    <span class="text-[10px] text-slate-500">Anak Laki-laki</span>
                </div>

                <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs">
                    <span class="text-xs text-pink-600 font-semibold block">Anak Perempuan</span>
                    <span class="text-2xl font-black text-pink-600 dark:text-pink-400 mt-1 block">{{ stats.female_count }}</span>
                    <span class="text-[10px] text-slate-500">Anak Perempuan</span>
                </div>
            </div>

            <!-- Filter & Search Controls -->
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm mb-6 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto flex-1">
                    <!-- Search input -->
                    <div class="relative w-full sm:w-80">
                        <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" />
                        <input
                            v-model="searchInput"
                            @keyup.enter="applyFilters"
                            type="text"
                            placeholder="Cari Nama Anak / Ortu / ID Unik..."
                            class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        />
                    </div>

                    <!-- Status Filter -->
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <Filter class="w-4 h-4 text-slate-400" />
                        <select
                            v-model="statusFilter"
                            @change="applyFilters"
                            class="py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 w-full sm:w-auto"
                        >
                            <option value="all">Semua Status Imunisasi</option>
                            <option value="lengkap">Lengkap</option>
                            <option value="tidak lengkap">Tidak Lengkap</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-2 self-end md:self-auto">
                    <button
                        @click="isScannerOpen = true"
                        class="px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition cursor-pointer flex items-center gap-1.5"
                    >
                        <QrCode class="w-4 h-4" />
                        <span>Scan QR Code</span>
                    </button>
                    <button
                        @click="resetFilters"
                        class="p-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                        title="Reset Filter"
                    >
                        <RefreshCw class="w-4 h-4" />
                    </button>
                </div>
            </div>

            <!-- Respondents Table -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-800 text-slate-500 font-semibold uppercase tracking-wider">
                                <th class="py-3.5 px-4">ID Unik</th>
                                <th class="py-3.5 px-4">Nama Anak</th>
                                <th class="py-3.5 px-4">Nama Kepala Keluarga</th>
                                <th class="py-3.5 px-4">Nama Ibu</th>
                                <th class="py-3.5 px-4">J. Kelamin</th>
                                <th class="py-3.5 px-4">Status Imunisasi</th>
                                <th class="py-3.5 px-4">No. WA</th>
                                <th class="py-3.5 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr
                                v-for="rec in records.data"
                                :key="rec.id"
                                class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition"
                            >
                                <!-- ID Unik -->
                                <td class="py-3.5 px-4">
                                    <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400 px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/80">
                                        {{ rec.user?.unique_code || 'N/A' }}
                                    </span>
                                </td>

                                <!-- Nama Anak -->
                                <td class="py-3.5 px-4 font-bold text-slate-900 dark:text-white">
                                    {{ rec.child_name }}
                                    <span class="block text-[10px] text-slate-400 font-normal">{{ rec.age_text }}</span>
                                </td>

                                <!-- Kepala Keluarga -->
                                <td class="py-3.5 px-4 font-medium text-slate-700 dark:text-slate-300">
                                    {{ rec.head_of_family }}
                                </td>

                                <!-- Nama Ibu -->
                                <td class="py-3.5 px-4 text-slate-600 dark:text-slate-400">
                                    {{ rec.mother_name }}
                                </td>

                                <!-- Gender -->
                                <td class="py-3.5 px-4">
                                    <span
                                        class="px-2 py-0.5 rounded-full font-semibold text-[10px] capitalize"
                                        :class="rec.gender === 'laki-laki' ? 'bg-sky-100 text-sky-700 dark:bg-sky-950 dark:text-sky-300' : 'bg-pink-100 text-pink-700 dark:bg-pink-950 dark:text-pink-300'"
                                    >
                                        {{ rec.gender }}
                                    </span>
                                </td>

                                <!-- Status Imunisasi -->
                                <td class="py-3.5 px-4">
                                    <span
                                        class="px-2.5 py-1 rounded-full font-bold text-[10px] capitalize inline-flex items-center gap-1"
                                        :class="rec.immunization_status === 'lengkap' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300'"
                                    >
                                        <CheckCircle2 v-if="rec.immunization_status === 'lengkap'" class="w-3 h-3" />
                                        <AlertCircle v-else class="w-3 h-3" />
                                        {{ rec.immunization_status }}
                                    </span>
                                </td>

                                <!-- No WA -->
                                <td class="py-3.5 px-4 font-mono text-slate-600 dark:text-slate-400">
                                    {{ rec.phone }}
                                </td>

                                <!-- Aksi -->
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button
                                            @click="selectedRecord = rec"
                                            class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition"
                                            title="Lihat Detail & QR Barcode"
                                        >
                                            <Eye class="w-4 h-4 text-teal-600" />
                                        </button>

                                        <button
                                            @click="deleteRecord(rec.id, rec.child_name)"
                                            class="p-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/60 dark:hover:bg-rose-900 text-rose-600 dark:text-rose-400 transition"
                                            title="Hapus Record"
                                        >
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="records.data.length === 0">
                                <td colspan="8" class="py-12 text-center text-slate-400">
                                    Tidak ditemukan data responden yang sesuai.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Links -->
                <div v-if="records.links.length > 3" class="p-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2 overflow-x-auto">
                    <div class="flex items-center gap-1">
                        <template v-for="(link, i) in records.links" :key="i">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                class="px-3 py-1.5 rounded-lg text-xs font-semibold transition"
                                :class="link.active ? 'bg-emerald-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 hover:bg-slate-200'"
                                v-html="link.label"
                            />
                            <span v-else class="px-2 py-1 text-xs text-slate-300" v-html="link.label"></span>
                        </template>
                    </div>
                </div>
            </div>
        </main>

        <!-- Respondent Detail Modal -->
        <div v-if="selectedRecord" class="fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-md flex items-center justify-center p-4">
            <div class="max-w-2xl w-full bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-7 shadow-2xl relative max-h-[90vh] overflow-y-auto border border-slate-200 dark:border-slate-800">
                <!-- Header -->
                <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <ShieldCheck class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                            Detail Verifikasi Responden
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Verifikasi identitas posyandu & rekap imunisasi</p>
                    </div>
                    <button
                        @click="selectedRecord = null"
                        class="p-2 rounded-2xl hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 transition"
                    >
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <!-- Main Content Layout Grid -->
                <div class="grid grid-cols-1 md:grid-cols-12 gap-5 mb-5">
                    <!-- Left: QR Code Card (5 cols) -->
                    <div class="md:col-span-5 flex flex-col items-center justify-center">
                        <QrCodeCard
                            :unique-code="selectedRecord.user?.unique_code || 'N/A'"
                            :child-name="selectedRecord.child_name"
                            :head-of-family="selectedRecord.head_of_family"
                            :size="140"
                            class="h-full"
                        />
                    </div>

                    <!-- Right: Primary Info Cards (7 cols) -->
                    <div class="md:col-span-7 space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="p-3 bg-slate-50 dark:bg-slate-800/80 rounded-2xl border border-slate-100 dark:border-slate-800">
                                <span class="text-[10px] text-slate-400 font-semibold block">Nama Anak</span>
                                <span class="font-bold text-xs text-slate-900 dark:text-white block truncate">{{ selectedRecord.child_name }}</span>
                                <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-medium block capitalize mt-0.5">{{ selectedRecord.gender }} ({{ selectedRecord.age_text }})</span>
                            </div>

                            <div class="p-3 bg-slate-50 dark:bg-slate-800/80 rounded-2xl border border-slate-100 dark:border-slate-800">
                                <span class="text-[10px] text-slate-400 font-semibold block">Status Imunisasi</span>
                                <span
                                    class="inline-flex items-center gap-1 font-bold text-xs px-2 py-0.5 rounded-full capitalize mt-1"
                                    :class="selectedRecord.immunization_status === 'lengkap' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300'"
                                >
                                    <CheckCircle2 v-if="selectedRecord.immunization_status === 'lengkap'" class="w-3 h-3" />
                                    <AlertCircle v-else class="w-3 h-3" />
                                    {{ selectedRecord.immunization_status }}
                                </span>
                            </div>

                            <div class="p-3 bg-slate-50 dark:bg-slate-800/80 rounded-2xl border border-slate-100 dark:border-slate-800">
                                <span class="text-[10px] text-slate-400 font-semibold block">Kepala Keluarga</span>
                                <span class="font-bold text-xs text-slate-900 dark:text-white block truncate">{{ selectedRecord.head_of_family }}</span>
                                <span class="text-[10px] text-slate-500 block truncate mt-0.5">Pekerjaan: {{ selectedRecord.father_job || '-' }}</span>
                            </div>

                            <div class="p-3 bg-slate-50 dark:bg-slate-800/80 rounded-2xl border border-slate-100 dark:border-slate-800">
                                <span class="text-[10px] text-slate-400 font-semibold block">Nama Ibu</span>
                                <span class="font-bold text-xs text-slate-900 dark:text-white block truncate">{{ selectedRecord.mother_name }}</span>
                                <span class="text-[10px] text-slate-500 block truncate mt-0.5">Pekerjaan: {{ selectedRecord.mother_job || '-' }}</span>
                            </div>
                        </div>

                        <!-- Contact & Address -->
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/80 rounded-2xl border border-slate-100 dark:border-slate-800">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[10px] text-slate-400 font-semibold">No. WhatsApp</span>
                                <span class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400">{{ selectedRecord.phone }}</span>
                            </div>
                            <div class="border-t border-slate-200/60 dark:border-slate-700/60 pt-1.5 mt-1.5">
                                <span class="text-[10px] text-slate-400 font-semibold block mb-0.5">Alamat Tinggal</span>
                                <p class="text-xs text-slate-700 dark:text-slate-300 leading-snug">{{ selectedRecord.address }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vaksin Diberikan Box -->
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-100 dark:border-slate-800 mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-200">
                            Vaksin Diberikan ({{ selectedRecord.immunization_types?.length || 0 }} Vaksin)
                        </span>
                    </div>
                    <div class="flex flex-wrap gap-1.5 max-h-32 overflow-y-auto">
                        <span
                            v-for="vax in selectedRecord.immunization_types"
                            :key="vax"
                            class="px-2.5 py-1 rounded-lg bg-emerald-100 dark:bg-emerald-950/90 text-emerald-800 dark:text-emerald-300 text-xs font-semibold border border-emerald-200/60 dark:border-emerald-800/60"
                        >
                            ✓ {{ vax }}
                        </span>
                        <span v-if="!selectedRecord.immunization_types || selectedRecord.immunization_types.length === 0" class="text-xs text-slate-400">
                            Belum ada vaksin yang dipilih
                        </span>
                    </div>

                    <div v-if="selectedRecord.incomplete_reason" class="mt-3 text-xs text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/40 p-2.5 rounded-xl border border-amber-200 dark:border-amber-900">
                        <strong>Alasan Belum Lengkap:</strong> {{ selectedRecord.incomplete_reason }}
                    </div>
                </div>

                <button
                    @click="selectedRecord = null"
                    class="w-full py-3 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition cursor-pointer"
                >
                    Tutup Detail Verifikasi
                </button>
            </div>
        </div>

        <!-- QR Code Scanner Modal -->
        <QrScannerModal
            v-if="isScannerOpen"
            @scan="handleScanResult"
            @close="isScannerOpen = false"
        />
    </div>
</template>
