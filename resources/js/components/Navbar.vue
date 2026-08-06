<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3'
import { HeartHandshake, ShieldCheck, FileText, LayoutDashboard, LogOut, User as UserIcon } from 'lucide-vue-next'
import { computed } from 'vue'

const page = usePage()
const authUser = computed(() => page.props.auth?.user as { name: string; role: string; unique_code: string } | null)
</script>

<template>
    <header class="sticky top-0 z-40 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-b border-emerald-100 dark:border-slate-800 shadow-xs w-full overflow-x-hidden">
        <div class="max-w-6xl mx-auto px-3 sm:px-6 h-16 flex items-center justify-between gap-2 sm:gap-4">
            <!-- Brand / Logo -->
            <Link href="/" class="flex items-center gap-1.5 sm:gap-2.5 group shrink-0">
                <div class="h-8 sm:h-10 px-2 py-0.5 rounded-xl bg-white shadow-xs border border-slate-200/80 dark:border-slate-700 flex items-center justify-center gap-1.5 shrink-0 group-hover:scale-105 transition-transform duration-200">
                    <img
                        src="/logo.jpeg"
                        alt="Logo FK Unimus"
                        class="h-5.5 sm:h-7.5 w-auto object-contain max-w-[65px] sm:max-w-[110px]"
                    />
                    <div class="h-4 sm:h-5 w-[1px] bg-slate-200 dark:bg-slate-700"></div>
                    <img
                        src="/logoPuskesmas.png"
                        alt="Logo Puskesmas"
                        class="h-5.5 sm:h-7.5 w-auto object-contain max-w-[65px] sm:max-w-[110px]"
                    />
                </div>
                <div class="flex flex-col justify-center">
                    <div class="flex items-center gap-1.5">
                        <span class="text-sm sm:text-base font-black text-slate-800 dark:text-white tracking-tight leading-none">
                            SIMUNA
                        </span>
                        <span class="hidden sm:inline-block px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 text-[10px] sm:text-xs font-bold border border-emerald-200 dark:border-emerald-800 shadow-2xs shrink-0">
                            Puskesmas Bulusan
                        </span>
                    </div>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 font-medium hidden lg:block mt-0.5">Sistem Informasi Monitoring Status Imunisasi Anak</p>
                </div>
            </Link>

            <!-- Navigation Actions -->
            <nav class="flex items-center gap-1.5 sm:gap-3 shrink-0">
                <template v-if="authUser">
                    <!-- Form Link -->
                    <Link
                        href="/form"
                        class="px-2.5 py-1.5 sm:px-3 sm:py-2 rounded-xl text-xs sm:text-sm font-medium transition flex items-center gap-1.5"
                        :class="$page.url.startsWith('/form') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 font-semibold' : 'text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800'"
                    >
                        <FileText class="w-4 h-4 text-emerald-600 shrink-0" />
                        <span class="hidden sm:inline">Form Imunisasi</span>
                        <span class="sm:hidden">Form</span>
                    </Link>

                    <!-- Responden Dashboard -->
                    <Link
                        v-if="authUser.role === 'user'"
                        href="/dashboard"
                        class="px-2.5 py-1.5 sm:px-3 sm:py-2 rounded-xl text-xs sm:text-sm font-medium transition flex items-center gap-1.5"
                        :class="$page.url.startsWith('/dashboard') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 font-semibold' : 'text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800'"
                    >
                        <LayoutDashboard class="w-4 h-4 text-teal-600 shrink-0" />
                        <span class="hidden sm:inline">Dashboard Saya</span>
                        <span class="sm:hidden">Dashboard</span>
                    </Link>

                    <!-- Admin Dashboard -->
                    <Link
                        v-if="authUser.role === 'admin'"
                        href="/admin/dashboard"
                        class="px-2.5 py-1.5 sm:px-3 sm:py-2 rounded-xl text-xs sm:text-sm font-medium bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 hover:bg-rose-100 transition flex items-center gap-1.5"
                    >
                        <ShieldCheck class="w-4 h-4 text-rose-600 shrink-0" />
                        <span class="hidden sm:inline">Panel Admin</span>
                        <span class="sm:hidden">Admin</span>
                    </Link>

                    <!-- Separator -->
                    <div class="h-5 w-[1px] bg-slate-200 dark:bg-slate-800 mx-0.5 hidden sm:block"></div>

                    <!-- User Info & Logout -->
                    <div class="flex items-center gap-1 sm:gap-2">
                        <div class="hidden lg:flex flex-col text-right">
                            <span class="text-xs font-semibold text-slate-800 dark:text-slate-200 leading-tight max-w-[120px] truncate">{{ authUser.name }}</span>
                            <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-mono font-bold">{{ authUser.unique_code }}</span>
                        </div>

                        <Link
                            href="/logout"
                            method="post"
                            as="button"
                            title="Keluar"
                            class="p-1.5 sm:p-2 rounded-xl text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-slate-800 transition"
                        >
                            <LogOut class="w-4 h-4" />
                        </Link>
                    </div>
                </template>

                <template v-else>
                    <Link
                        href="/login"
                        class="px-2.5 py-1.5 sm:px-3 sm:py-2 rounded-xl text-xs sm:text-sm font-medium text-slate-600 hover:text-slate-900 dark:text-slate-300 transition"
                    >
                        Masuk
                    </Link>
                    <Link
                        href="/register"
                        class="px-3 py-1.5 sm:px-4 sm:py-2 rounded-xl text-xs sm:text-sm font-semibold bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs shadow-emerald-600/30 transition flex items-center gap-1.5"
                    >
                        <UserIcon class="w-4 h-4 shrink-0" />
                        <span>Daftar</span>
                    </Link>
                </template>
            </nav>
        </div>
    </header>
</template>
