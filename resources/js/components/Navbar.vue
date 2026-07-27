<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3'
import { HeartHandshake, ShieldCheck, FileText, LayoutDashboard, LogOut, User as UserIcon } from 'lucide-vue-next'
import { computed } from 'vue'

const page = usePage()
const authUser = computed(() => page.props.auth?.user as { name: string; role: string; unique_code: string } | null)
</script>

<template>
    <header class="sticky top-0 z-40 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-b border-emerald-100 dark:border-slate-800 shadow-xs">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
            <!-- Brand / Logo -->
            <Link href="/" class="flex items-center gap-2.5 group">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white shadow-md shadow-emerald-500/20 group-hover:scale-105 transition-transform duration-200">
                    <HeartHandshake class="w-6 h-6" />
                </div>
                <div>
                    <span class="text-lg font-bold text-slate-800 dark:text-white tracking-tight flex items-center gap-1.5">
                        SIMUNA <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 font-semibold">Puskesmas</span>
                    </span>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 hidden sm:block">Sistem Informasi Rekap Imunisasi Anak</p>
                </div>
            </Link>

            <!-- Navigation Actions -->
            <nav class="flex items-center gap-2 sm:gap-3">
                <template v-if="authUser">
                    <!-- Form Link -->
                    <Link
                        href="/form"
                        class="px-3 py-2 rounded-xl text-xs sm:text-sm font-medium transition flex items-center gap-1.5"
                        :class="$page.url.startsWith('/form') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 font-semibold' : 'text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800'"
                    >
                        <FileText class="w-4 h-4 text-emerald-600" />
                        <span class="hidden sm:inline">Form Imunisasi</span>
                    </Link>

                    <!-- Responden Dashboard -->
                    <Link
                        v-if="authUser.role === 'user'"
                        href="/dashboard"
                        class="px-3 py-2 rounded-xl text-xs sm:text-sm font-medium transition flex items-center gap-1.5"
                        :class="$page.url.startsWith('/dashboard') ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 font-semibold' : 'text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800'"
                    >
                        <LayoutDashboard class="w-4 h-4 text-teal-600" />
                        <span class="hidden sm:inline">Dashboard Saya</span>
                    </Link>

                    <!-- Admin Dashboard -->
                    <Link
                        v-if="authUser.role === 'admin'"
                        href="/admin/dashboard"
                        class="px-3 py-2 rounded-xl text-xs sm:text-sm font-medium bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 hover:bg-rose-100 transition flex items-center gap-1.5"
                    >
                        <ShieldCheck class="w-4 h-4 text-rose-600" />
                        <span>Panel Admin</span>
                    </Link>

                    <!-- User Pill & Logout -->
                    <div class="h-6 w-[1px] bg-slate-200 dark:bg-slate-800 mx-1 hidden sm:block"></div>

                    <div class="flex items-center gap-2 pl-1">
                        <div class="hidden md:flex flex-col text-right">
                            <span class="text-xs font-semibold text-slate-800 dark:text-slate-200 leading-tight">{{ authUser.name }}</span>
                            <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-mono font-bold">{{ authUser.unique_code }}</span>
                        </div>

                        <Link
                            href="/logout"
                            method="post"
                            as="button"
                            title="Keluar"
                            class="p-2 rounded-xl text-slate-500 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-slate-800 transition"
                        >
                            <LogOut class="w-4 h-4" />
                        </Link>
                    </div>
                </template>

                <template v-else>
                    <Link
                        href="/login"
                        class="px-3 py-2 rounded-xl text-xs sm:text-sm font-medium text-slate-600 hover:text-slate-900 dark:text-slate-300 transition"
                    >
                        Masuk
                    </Link>
                    <Link
                        href="/register"
                        class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs shadow-emerald-600/30 transition flex items-center gap-1.5"
                    >
                        <UserIcon class="w-4 h-4" />
                        Daftar Ortu
                    </Link>
                </template>
            </nav>
        </div>
    </header>
</template>
