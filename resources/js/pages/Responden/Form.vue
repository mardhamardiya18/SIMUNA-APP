<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import Navbar from '@/components/Navbar.vue'
import Toast from '@/components/Toast.vue'
import {
    Baby, User, Briefcase, Calendar, MapPin, Phone, Mail,
    CheckCircle2, AlertCircle, Send, Sparkles, HeartPulse, FileCheck, Calculator
} from 'lucide-vue-next'
import { ref, watch, computed } from 'vue'

const props = defineProps<{
    existingRecord?: any
    immunizationOptions?: Array<{ id: number; name: string; code: string; category: string; recommended_age: string }>
    defaultUserData: { name: string; email: string; phone: string }
}>()

const defaultStaticVaccines = [
    { id: 1, name: 'Hepatitis B (HB-0)', code: 'HB0', category: 'Wajib Dasar', recommended_age: '0 Bulan (24 jam)' },
    { id: 2, name: 'BCG', code: 'BCG', category: 'Wajib Dasar', recommended_age: '1 Bulan' },
    { id: 3, name: 'Polio Tetes 1 (BOPV 1)', code: 'BOPV 1', category: 'Wajib Dasar', recommended_age: '1 Bulan' },
    { id: 4, name: 'DPT-HB-Hib 1', code: 'DPT-HB-Hib 1', category: 'Wajib Dasar', recommended_age: '2 Bulan' },
    { id: 5, name: 'Polio Tetes 2 (BOPV 2)', code: 'BOPV 2', category: 'Wajib Dasar', recommended_age: '2 Bulan' },
    { id: 6, name: 'DPT-HB-Hib 2', code: 'DPT-HB-Hib 2', category: 'Wajib Dasar', recommended_age: '3 Bulan' },
    { id: 7, name: 'Polio Tetes 3 (BOPV 3)', code: 'BOPV 3', category: 'Wajib Dasar', recommended_age: '3 Bulan' },
    { id: 8, name: 'DPT-HB-Hib 3', code: 'DPT-HB-Hib 3', category: 'Wajib Dasar', recommended_age: '4 Bulan' },
    { id: 9, name: 'Polio Tetes 4 (BOPV 4)', code: 'BOPV 4', category: 'Wajib Dasar', recommended_age: '4 Bulan' },
    { id: 10, name: 'Polio Suntik 1 (IPV 1)', code: 'IPV 1', category: 'Wajib Dasar', recommended_age: '4 Bulan' },
    { id: 11, name: 'Campak Rubella (MR 1)', code: 'MR 1', category: 'Wajib Dasar', recommended_age: '9 Bulan' },
    { id: 12, name: 'Polio Suntik 2 (IPV 2)', code: 'IPV 2', category: 'Wajib Dasar', recommended_age: '9 Bulan' },
    { id: 13, name: 'DPT-HB-Hib Lanjutan (Booster)', code: 'DPT Booster', category: 'Lanjutan', recommended_age: '18 Bulan' },
    { id: 14, name: 'Campak Rubella Lanjutan (MR 2)', code: 'MR 2', category: 'Lanjutan', recommended_age: '18 Bulan' },
]

const activeVaccineOptions = computed(() => {
    if (props.immunizationOptions && props.immunizationOptions.length > 0) {
        return props.immunizationOptions
    }
    return defaultStaticVaccines
})

const form = useForm({
    head_of_family: props.existingRecord?.head_of_family || props.defaultUserData.name || '',
    email: props.existingRecord?.email || props.defaultUserData.email || '',
    phone: props.existingRecord?.phone || props.defaultUserData.phone || '',
    child_name: props.existingRecord?.child_name || '',
    father_job: props.existingRecord?.father_job || '',
    mother_name: props.existingRecord?.mother_name || '',
    mother_job: props.existingRecord?.mother_job || '',
    gender: props.existingRecord?.gender || 'laki-laki',
    age_text: props.existingRecord?.age_text || '',
    birth_date: props.existingRecord?.birth_date || '',
    address: props.existingRecord?.address || '',
    immunization_status: props.existingRecord?.immunization_status || 'lengkap',
    immunization_types: props.existingRecord?.immunization_types || [],
    incomplete_reason: props.existingRecord?.incomplete_reason || '',
})

function calculateAgeFromBirthDate(birthDateStr: string): string {
    if (!birthDateStr) return ''

    const birthDate = new Date(birthDateStr)
    const today = new Date()

    if (isNaN(birthDate.getTime()) || birthDate > today) {
        return ''
    }

    let years = today.getFullYear() - birthDate.getFullYear()
    let months = today.getMonth() - birthDate.getMonth()
    let days = today.getDate() - birthDate.getDate()

    if (days < 0) {
        months -= 1
    }
    if (months < 0) {
        years -= 1
        months += 12
    }

    if (years === 0 && months === 0) {
        const diffTime = Math.abs(today.getTime() - birthDate.getTime())
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
        return `${diffDays} hari`
    }

    if (years === 0) {
        return `${months} bulan`
    }

    if (months === 0) {
        return `${years} tahun`
    }

    return `${years} tahun ${months} bulan`
}

function handleBirthDateChange() {
    if (!form.birth_date) return

    const calculatedAge = calculateAgeFromBirthDate(form.birth_date)
    if (calculatedAge) {
        form.age_text = calculatedAge

        // Trigger Toast Notification
        window.dispatchEvent(
            new CustomEvent('show-toast', {
                detail: {
                    message: `Usia anak berhasil dihitung otomatis: ${calculatedAge}`,
                    type: 'info',
                },
            })
        )
    }
}

watch(
    () => form.birth_date,
    (newVal, oldVal) => {
        if (newVal && newVal !== oldVal) {
            handleBirthDateChange()
        }
    }
)

const allVaccineCodes = computed(() => activeVaccineOptions.value.map(item => item.code))

watch(
    () => form.immunization_status,
    (newStatus) => {
        if (newStatus === 'lengkap') {
            if (form.immunization_types.length !== allVaccineCodes.value.length) {
                form.immunization_types = [...allVaccineCodes.value]
            }
            form.incomplete_reason = ''
        } else if (newStatus === 'tidak lengkap') {
            if (form.immunization_types.length === allVaccineCodes.value.length) {
                form.immunization_types = []
            }
        }
    }
)

watch(
    () => form.immunization_types,
    (newTypes) => {
        const total = allVaccineCodes.value.length
        if (total > 0) {
            if (newTypes.length === total) {
                if (form.immunization_status !== 'lengkap') {
                    form.immunization_status = 'lengkap'
                }
            } else {
                if (form.immunization_status !== 'tidak lengkap') {
                    form.immunization_status = 'tidak lengkap'
                }
            }
        }
    },
    { deep: true }
)

function toggleImmunization(code: string) {
    const current = [...form.immunization_types]
    const index = current.indexOf(code)
    if (index > -1) {
        current.splice(index, 1)
    } else {
        current.push(code)
    }
    form.immunization_types = current
}

function selectAllVaccines() {
    form.immunization_types = [...allVaccineCodes.value]
    form.immunization_status = 'lengkap'
}

function clearVaccines() {
    form.immunization_types = []
    form.immunization_status = 'tidak lengkap'
}

function submit() {
    form.post('/form', {
        preserveScroll: false,
        onSuccess: () => {
            window.scrollTo({ top: 0, left: 0, behavior: 'instant' })
        },
    })
}
</script>

<template>
    <Head title="Form Kelengkapan Imunisasi - SIMUNA" />
    <Toast />

    <div class="min-h-screen bg-gradient-to-br from-emerald-50/60 via-slate-50 to-teal-50/40 dark:from-slate-950 dark:via-slate-900 dark:to-emerald-950/30 flex flex-col text-slate-800 dark:text-slate-100 w-full overflow-x-hidden">
        <Navbar />

        <main class="flex-1 py-4 sm:py-8 px-3.5 sm:px-6 max-w-4xl mx-auto w-full">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 text-white rounded-2xl sm:rounded-3xl p-5 sm:p-8 shadow-xl shadow-emerald-600/20 mb-6 sm:mb-8 relative overflow-hidden">
                <div class="absolute -right-8 -bottom-8 w-40 h-40 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-[11px] sm:text-xs font-semibold mb-2 sm:mb-3">
                            <Sparkles class="w-3.5 h-3.5" />
                            Pendataan Imunisasi Anak Posyandu
                        </div>
                        <h1 class="text-xl sm:text-3xl font-extrabold tracking-tight">Form Kelengkapan Imunisasi</h1>
                        <p class="text-emerald-100 text-xs sm:text-sm mt-1 max-w-xl">
                            Silakan lengkapi formulir di bawah ini. Data ter-autofill dari pendaftaran Anda. Setelah disimpan, Anda akan menerima ID Verifikasi Posyandu.
                        </p>
                    </div>

                    <div class="bg-white/15 backdrop-blur-md rounded-2xl p-2.5 sm:p-3 border border-white/20 text-center shrink-0 flex items-center justify-start sm:justify-center gap-2.5 sm:gap-3">
                        <HeartPulse class="w-6 h-6 sm:w-8 sm:h-8 text-emerald-200 shrink-0" />
                        <div class="text-left">
                            <p class="text-[9px] sm:text-[10px] text-emerald-200 font-semibold uppercase tracking-wider">Puskesmas Mitra</p>
                            <p class="text-xs font-bold text-white">Puskesmas Bulusan</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <form @submit.prevent="submit" class="space-y-8">
                <!-- Section 1: Data Kepala Keluarga & Orang Tua -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-emerald-100 dark:border-slate-800 shadow-md">
                    <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100 dark:border-slate-800">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-sm">
                            01
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Data Orang Tua / Kepala Keluarga</h2>
                            <p class="text-xs text-slate-500">Informasi penanggung jawab & kontak yang dapat dihubungi</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Nama Kepala Keluarga -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Nama Kepala Keluarga <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <User class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" />
                                <input
                                    v-model="form.head_of_family"
                                    type="text"
                                    placeholder="Bapak / Kepala Keluarga"
                                    class="w-full pl-10 pr-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                />
                            </div>
                            <p v-if="form.errors.head_of_family" class="text-xs text-rose-500 mt-1">{{ form.errors.head_of_family }}</p>
                        </div>

                        <!-- Pekerjaan Ayah -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Pekerjaan Ayah
                            </label>
                            <div class="relative">
                                <Briefcase class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" />
                                <input
                                    v-model="form.father_job"
                                    type="text"
                                    placeholder="Contoh: Wiraswasta, Karyawan, PNS"
                                    class="w-full pl-10 pr-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                />
                            </div>
                        </div>

                        <!-- Nama Ibu -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Nama Ibu <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <User class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" />
                                <input
                                    v-model="form.mother_name"
                                    type="text"
                                    placeholder="Lengkapi nama lengkap ibu"
                                    class="w-full pl-10 pr-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                />
                            </div>
                            <p v-if="form.errors.mother_name" class="text-xs text-rose-500 mt-1">{{ form.errors.mother_name }}</p>
                        </div>

                        <!-- Pekerjaan Ibu -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Pekerjaan Ibu
                            </label>
                            <div class="relative">
                                <Briefcase class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" />
                                <input
                                    v-model="form.mother_job"
                                    type="text"
                                    placeholder="Contoh: Ibu Rumah Tangga, Dokter, Guru"
                                    class="w-full pl-10 pr-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                />
                            </div>
                        </div>

                        <!-- No WA -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                No. WhatsApp Aktif <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <Phone class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" />
                                <input
                                    v-model="form.phone"
                                    type="tel"
                                    class="w-full pl-10 pr-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                />
                            </div>
                            <p v-if="form.errors.phone" class="text-xs text-rose-500 mt-1">{{ form.errors.phone }}</p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Email Aktif
                            </label>
                            <div class="relative">
                                <Mail class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" />
                                <input
                                    v-model="form.email"
                                    type="email"
                                    class="w-full pl-10 pr-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Data Anak -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-emerald-100 dark:border-slate-800 shadow-md">
                    <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100 dark:border-slate-800">
                        <div class="w-10 h-10 rounded-2xl bg-teal-100 dark:bg-teal-950 text-teal-600 dark:text-teal-400 flex items-center justify-center font-bold text-sm">
                            02
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Identitas Anak</h2>
                            <p class="text-xs text-slate-500">Informasi nama, tanggal lahir, dan usia si kecil</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Nama Anak -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Nama Anak <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <Baby class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" />
                                <input
                                    v-model="form.child_name"
                                    type="text"
                                    placeholder="Contoh: An. X / Brahim Elvano"
                                    class="w-full pl-10 pr-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                />
                            </div>
                            <p v-if="form.errors.child_name" class="text-xs text-rose-500 mt-1">{{ form.errors.child_name }}</p>
                        </div>

                        <!-- Jenis Kelamin -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Jenis Kelamin <span class="text-rose-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <button
                                    type="button"
                                    @click="form.gender = 'laki-laki'"
                                    class="py-2.5 px-4 rounded-2xl border text-sm font-medium transition flex items-center justify-center gap-2 cursor-pointer"
                                    :class="form.gender === 'laki-laki' ? 'bg-sky-50 border-sky-500 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300 font-semibold' : 'border-slate-200 dark:border-slate-700 text-slate-600 hover:bg-slate-50'"
                                >
                                    <span>👦 Laki-laki</span>
                                </button>
                                <button
                                    type="button"
                                    @click="form.gender = 'perempuan'"
                                    class="py-2.5 px-4 rounded-2xl border text-sm font-medium transition flex items-center justify-center gap-2 cursor-pointer"
                                    :class="form.gender === 'perempuan' ? 'bg-pink-50 border-pink-500 text-pink-700 dark:bg-pink-950/60 dark:text-pink-300 font-semibold' : 'border-slate-200 dark:border-slate-700 text-slate-600 hover:bg-slate-50'"
                                >
                                    <span>👧 Perempuan</span>
                                </button>
                            </div>
                        </div>

                        <!-- Tanggal Lahir Anak -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Tanggal Lahir Anak <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <Calendar class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" />
                                <input
                                    v-model="form.birth_date"
                                    @change="handleBirthDateChange"
                                    type="date"
                                    class="w-full pl-10 pr-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                />
                            </div>
                            <p v-if="form.errors.birth_date" class="text-xs text-rose-500 mt-1">{{ form.errors.birth_date }}</p>
                        </div>

                        <!-- Usia Anak -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1 flex items-center justify-between">
                                <span>Usia Anak <span class="text-rose-500">*</span></span>
                                <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-normal flex items-center gap-1">
                                    <Calculator class="w-3 h-3" /> Otomatis terhitung
                                </span>
                            </label>
                            <div class="relative">
                                <Calendar class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" />
                                <input
                                    v-model="form.age_text"
                                    type="text"
                                    placeholder="Otomatis terisi saat memilih tanggal lahir"
                                    class="w-full pl-10 pr-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                />
                            </div>
                            <p v-if="form.errors.age_text" class="text-xs text-rose-500 mt-1">{{ form.errors.age_text }}</p>
                        </div>

                        <!-- Alamat -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Alamat Tinggal Lengkap <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <MapPin class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" />
                                <textarea
                                    v-model="form.address"
                                    rows="2"
                                    placeholder="Jalan, RT/RW, Kelurahan, Kecamatan"
                                    class="w-full pl-10 pr-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                ></textarea>
                            </div>
                            <p v-if="form.errors.address" class="text-xs text-rose-500 mt-1">{{ form.errors.address }}</p>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Status Imunisasi & Riwayat -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-emerald-100 dark:border-slate-800 shadow-md">
                    <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100 dark:border-slate-800">
                        <div class="w-10 h-10 rounded-2xl bg-rose-100 dark:bg-rose-950 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold text-sm">
                            03
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Status & Riwayat Imunisasi</h2>
                            <p class="text-xs text-slate-500">Pilih jenis imunisasi yang pernah diberikan kepada si kecil</p>
                        </div>
                    </div>

                    <!-- Radio Status -->
                    <div class="mb-6">
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            Status Kelengkapan Imunisasi Saat Ini <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label
                                class="p-4 rounded-2xl border cursor-pointer transition flex items-center gap-3"
                                :class="form.immunization_status === 'lengkap' ? 'bg-emerald-50 border-emerald-500 dark:bg-emerald-950/60' : 'border-slate-200 dark:border-slate-700 hover:bg-slate-50'"
                            >
                                <input
                                    type="radio"
                                    v-model="form.immunization_status"
                                    value="lengkap"
                                    class="text-emerald-600 focus:ring-emerald-500"
                                />
                                <div>
                                    <span class="block text-sm font-bold text-slate-900 dark:text-white">Lengkap</span>
                                    <span class="text-xs text-slate-500">Imunisasi sesuai rekomendasi usia anak</span>
                                </div>
                            </label>

                            <label
                                class="p-4 rounded-2xl border cursor-pointer transition flex items-center gap-3"
                                :class="form.immunization_status === 'tidak lengkap' ? 'bg-amber-50 border-amber-500 dark:bg-amber-950/60' : 'border-slate-200 dark:border-slate-700 hover:bg-slate-50'"
                            >
                                <input
                                    type="radio"
                                    v-model="form.immunization_status"
                                    value="tidak lengkap"
                                    class="text-amber-600 focus:ring-amber-500"
                                />
                                <div>
                                    <span class="block text-sm font-bold text-slate-900 dark:text-white">Tidak Lengkap / Belum Selesai</span>
                                    <span class="text-xs text-slate-500">Masih ada beberapa vaksin yang terlewat</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Checklist Jenis Imunisasi -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-3">
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                                Pilih Vaksin yang Sudah Diberikan ({{ activeVaccineOptions.length }} Pilihan Vaksin)
                            </label>
                            <div class="flex items-center gap-2 text-xs">
                                <button type="button" @click="selectAllVaccines" class="text-emerald-600 hover:underline font-medium">Pilih Semua</button>
                                <span class="text-slate-300">|</span>
                                <button type="button" @click="clearVaccines" class="text-rose-500 hover:underline font-medium">Kosongkan</button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 max-h-80 overflow-y-auto p-1">
                            <div
                                v-for="vax in activeVaccineOptions"
                                :key="vax.code"
                                @click="toggleImmunization(vax.code)"
                                class="p-3.5 rounded-2xl border text-left cursor-pointer transition flex items-center justify-between gap-3"
                                :class="form.immunization_types.includes(vax.code) ? 'bg-emerald-50/90 border-emerald-500 dark:bg-emerald-950/80 shadow-sm ring-1 ring-emerald-500/30' : 'border-slate-200 dark:border-slate-800 hover:border-emerald-300 dark:hover:border-emerald-800 hover:bg-slate-50 dark:hover:bg-slate-800/50'"
                            >
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-100 leading-snug">{{ vax.name }}</span>
                                <CheckCircle2 v-if="form.immunization_types.includes(vax.code)" class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" />
                            </div>
                        </div>
                    </div>

                    <!-- Conditional Logic: Alasan Tidak Lengkap -->
                    <div v-if="form.immunization_status === 'tidak lengkap'" class="p-4 rounded-2xl bg-amber-50/80 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900 transition">
                        <label class="block text-xs font-semibold text-amber-900 dark:text-amber-300 mb-1">
                            Alasan Tidak Lengkap / Belum Diberikan <span class="text-rose-500">*</span>
                        </label>
                        <textarea
                            v-model="form.incomplete_reason"
                            rows="2"
                            placeholder="Jelaskan alasan (misal: anak sempat sakit, stok vaksin habis, sibuk kerja, dll.)"
                            class="w-full px-4 py-2.5 rounded-xl border border-amber-300 dark:border-amber-800 bg-white dark:bg-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"
                        ></textarea>
                        <p v-if="form.errors.incomplete_reason" class="text-xs text-rose-500 mt-1">{{ form.errors.incomplete_reason }}</p>
                    </div>
                </div>

                <!-- Submit Button Bar -->
                <div class="flex items-center justify-end gap-4 pt-4">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full sm:w-auto px-8 py-3.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-sm shadow-lg shadow-emerald-600/30 transition flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50"
                    >
                        <Send class="w-4 h-4" />
                        <span>{{ form.processing ? 'Menyimpan Data...' : 'Simpan & Rekap Data Imunisasi' }}</span>
                    </button>
                </div>
            </form>
        </main>
    </div>
</template>
