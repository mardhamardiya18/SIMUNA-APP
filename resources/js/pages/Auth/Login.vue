<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import Navbar from '@/components/Navbar.vue'
import Toast from '@/components/Toast.vue'
import { HeartHandshake, User, Lock, LogIn, ShieldCheck, UserCheck, Eye, EyeOff } from 'lucide-vue-next'
import { ref } from 'vue'

const showPassword = ref(false)

const form = useForm({
    login_id: '',
    password: '',
    remember: false,
})

function submit() {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
    <Head title="Masuk Akun - SIMUNA" />
    <Toast />

    <div class="min-h-screen bg-gradient-to-br from-emerald-50/60 via-slate-50 to-teal-50/40 dark:from-slate-950 dark:via-slate-900 dark:to-emerald-950/30 flex flex-col text-slate-800 dark:text-slate-100">
        <Navbar />

        <main class="flex-1 flex items-center justify-center p-4 sm:p-6 md:p-8">
            <div class="max-w-md w-full my-auto">
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center gap-2.5 px-3.5 py-1.5 rounded-2xl bg-white shadow-sm border border-slate-200/80 dark:border-slate-700 mx-auto mb-3">
                        <img src="/logo.jpeg" alt="Logo FK Unimus" class="h-9 sm:h-11 w-auto object-contain" />
                        <div class="h-6 w-[1px] bg-slate-200 dark:bg-slate-700"></div>
                        <img src="/logoPuskesmas.png" alt="Logo Puskesmas Bulusan" class="h-9 sm:h-11 w-auto object-contain" />
                    </div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Selamat Datang Kembali</h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        Masuk untuk melihat atau memperbarui rekap data imunisasi
                    </p>
                </div>

                <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-emerald-100 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl shadow-emerald-950/5">
                    <form @submit.prevent="submit" class="space-y-4">
                        <!-- Email / WhatsApp Input -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Email atau No. WhatsApp Terdaftar <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <User class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" />
                                <input
                                    v-model="form.login_id"
                                    type="text"
                                    placeholder="nama@gmail.com atau 08xxxxxxxxxx"
                                    class="w-full pl-10 pr-4 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition"
                                    :class="{ 'border-rose-500 ring-rose-200': form.errors.login_id }"
                                />
                            </div>
                            <p v-if="form.errors.login_id" class="text-xs text-rose-500 mt-1">{{ form.errors.login_id }}</p>
                        </div>

                        <!-- Password -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Password <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <Lock class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" />
                                <input
                                    v-model="form.password"
                                    :type="showPassword ? 'text' : 'password'"
                                    placeholder="Masukkan password Anda"
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

                        <!-- Remember Me -->
                        <div class="flex items-center justify-between text-xs">
                            <label class="flex items-center gap-2 cursor-pointer text-slate-600 dark:text-slate-400">
                                <input v-model="form.remember" type="checkbox" class="rounded text-emerald-600 focus:ring-emerald-500" />
                                <span>Ingat saya di perangkat ini</span>
                            </label>
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full py-3 px-4 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold text-sm shadow-md shadow-emerald-600/30 transition flex items-center justify-center gap-2 disabled:opacity-50 mt-2 cursor-pointer"
                        >
                            <LogIn class="w-4 h-4" />
                            <span>{{ form.processing ? 'Memproses...' : 'Masuk' }}</span>
                        </button>
                    </form>

                    <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 text-center">
                        <p class="text-xs text-slate-500">
                            Belum punya akun?
                            <Link href="/register" class="text-emerald-600 hover:text-emerald-700 font-semibold underline">
                                Registrasi Orang Tua Baru
                            </Link>
                        </p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
