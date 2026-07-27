<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import Navbar from '@/components/Navbar.vue'
import Toast from '@/components/Toast.vue'
import { HeartHandshake, User, Mail, Phone, Lock, ArrowRight, ShieldCheck, Baby, Eye, EyeOff } from 'lucide-vue-next'
import { ref } from 'vue'

const showPassword = ref(false)
const showPasswordConfirmation = ref(false)

const form = useForm({
    name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
})

function submit() {
    form.post('/register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    })
}
</script>

<template>
    <Head title="Registrasi Akun Ortu - SIMUNA" />
    <Toast />

    <div class="min-h-screen bg-gradient-to-br from-emerald-50/60 via-slate-50 to-teal-50/40 dark:from-slate-950 dark:via-slate-900 dark:to-emerald-950/30 flex flex-col text-slate-800 dark:text-slate-100">
        <Navbar />

        <main class="flex-1 flex items-center justify-center p-4 sm:p-6 md:p-8">
            <div class="max-w-md w-full my-auto">
                <!-- Header Banner -->
                <div class="text-center mb-6">
                    <div class="w-14 h-14 rounded-3xl bg-gradient-to-tr from-emerald-600 to-teal-400 text-white flex items-center justify-center mx-auto shadow-lg shadow-emerald-500/20 mb-3">
                        <Baby class="w-7 h-7" />
                    </div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Registrasi Akun Orang Tua</h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-xs mx-auto">
                        Daftarkan diri Anda untuk mengisi data imunisasi anak & mendapatkan ID Verifikasi Posyandu
                    </p>
                </div>

                <!-- Registration Card -->
                <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-emerald-100 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl shadow-emerald-950/5">
                    <form @submit.prevent="submit" class="space-y-4">
                        <!-- Nama Kepala Keluarga -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Nama Kepala Keluarga / Ayah / Ibu <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <User class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" />
                                <input
                                    v-model="form.name"
                                    type="text"
                                    placeholder="Contoh: Bpk. Gilar / Ibu Dila"
                                    class="w-full pl-10 pr-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition"
                                    :class="{ 'border-rose-500 ring-rose-200': form.errors.name }"
                                />
                            </div>
                            <p v-if="form.errors.name" class="text-xs text-rose-500 mt-1">{{ form.errors.name }}</p>
                        </div>

                        <!-- Email Aktif -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Email Aktif <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <Mail class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" />
                                <input
                                    v-model="form.email"
                                    type="email"
                                    placeholder="nama@gmail.com"
                                    class="w-full pl-10 pr-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition"
                                    :class="{ 'border-rose-500 ring-rose-200': form.errors.email }"
                                />
                            </div>
                            <p v-if="form.errors.email" class="text-xs text-rose-500 mt-1">{{ form.errors.email }}</p>
                        </div>

                        <!-- No WhatsApp -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                No. WhatsApp Aktif <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <Phone class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" />
                                <input
                                    v-model="form.phone"
                                    type="tel"
                                    placeholder="08xxxxxxxxxx"
                                    class="w-full pl-10 pr-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition"
                                    :class="{ 'border-rose-500 ring-rose-200': form.errors.phone }"
                                />
                            </div>
                            <p v-if="form.errors.phone" class="text-xs text-rose-500 mt-1">{{ form.errors.phone }}</p>
                        </div>

                        <!-- Password -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Buat Password <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <Lock class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" />
                                <input
                                    v-model="form.password"
                                    :type="showPassword ? 'text' : 'password'"
                                    placeholder="Minimal 6 karakter"
                                    class="w-full pl-10 pr-10 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition"
                                    :class="{ 'border-rose-500 ring-rose-200': form.errors.password }"
                                />
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute right-3.5 top-2.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition focus:outline-none cursor-pointer"
                                    tabindex="-1"
                                >
                                    <EyeOff v-if="showPassword" class="w-4 h-4" />
                                    <Eye v-else class="w-4 h-4" />
                                </button>
                            </div>
                            <p v-if="form.errors.password" class="text-xs text-rose-500 mt-1">{{ form.errors.password }}</p>
                        </div>

                        <!-- Konfirmasi Password -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Ulangi Password <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <Lock class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" />
                                <input
                                    v-model="form.password_confirmation"
                                    :type="showPasswordConfirmation ? 'text' : 'password'"
                                    placeholder="Ketik ulang password Anda"
                                    class="w-full pl-10 pr-10 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition"
                                />
                                <button
                                    type="button"
                                    @click="showPasswordConfirmation = !showPasswordConfirmation"
                                    class="absolute right-3.5 top-2.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition focus:outline-none cursor-pointer"
                                    tabindex="-1"
                                >
                                    <EyeOff v-if="showPasswordConfirmation" class="w-4 h-4" />
                                    <Eye v-else class="w-4 h-4" />
                                </button>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full py-3 px-4 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold text-sm shadow-md shadow-emerald-600/30 transition flex items-center justify-center gap-2 disabled:opacity-50 mt-2 cursor-pointer"
                        >
                            <span>{{ form.processing ? 'Mendaftarkan...' : 'Lanjut Isi Form Imunisasi' }}</span>
                            <ArrowRight class="w-4 h-4" />
                        </button>
                    </form>

                    <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 text-center">
                        <p class="text-xs text-slate-500">
                            Sudah pernah mendaftar?
                            <Link href="/login" class="text-emerald-600 hover:text-emerald-700 font-semibold underline">
                                Masuk ke Akun Anda
                            </Link>
                        </p>
                    </div>
                </div>

                <div class="mt-6 text-center flex items-center justify-center gap-1.5 text-xs text-slate-400">
                    <ShieldCheck class="w-4 h-4 text-emerald-500" />
                    Data terjaga aman & rahasia oleh Puskesmas
                </div>
            </div>
        </main>
    </div>
</template>
