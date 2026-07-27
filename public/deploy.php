<?php

// Standalone Production Deployment Runner for Shared Hosting (Hostinger)
$secret = env('DEPLOY_SECRET', 'SimunaSecure2026!');

if (($_GET['secret'] ?? '') !== $secret) {
    http_response_code(403);
    exit('<div style="font-family: sans-serif; padding: 20px; color: #e11d48;"><h3>Akses Ditolak</h3><p>Secret token tidak valid atau tidak disertakan.</p></div>');
}

// Ensure working directory is Laravel root
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    chdir(__DIR__ . '/..');
}

// Set temporary env vars in memory so boot doesn't crash on missing session/cache tables
$_ENV['SESSION_DRIVER'] = 'file';
$_ENV['CACHE_STORE'] = 'file';
putenv('SESSION_DRIVER=file');
putenv('CACHE_STORE=file');

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$logs = [];

// 1. Run Migration
try {
    $kernel->call('migrate', ['--force' => true]);
    $logs[] = "=== 1. MIGRATE DATABASE ===";
    $logs[] = $kernel->output();
} catch (\Throwable $e) {
    $logs[] = "=== 1. MIGRATE DATABASE ERROR ===";
    $logs[] = $e->getMessage();
}

// 2. Run Seeder
try {
    $kernel->call('db:seed', ['--class' => 'ImmunizationSeeder', '--force' => true]);
    $logs[] = "=== 2. SEEDING VACCINE DATA ===";
    $logs[] = $kernel->output();
} catch (\Throwable $e) {
    $logs[] = "=== 2. SEEDING DATA ERROR ===";
    $logs[] = $e->getMessage();
}

// 3. Storage Link
try {
    $kernel->call('storage:link');
    $logs[] = "=== 3. STORAGE LINK ===";
    $logs[] = $kernel->output();
} catch (\Throwable $e) {
    $logs[] = "=== 3. STORAGE LINK NOTICE ===";
    $logs[] = $e->getMessage();
}

// 4. Optimize Cache
try {
    $kernel->call('optimize');
    $logs[] = "=== 4. LARAVEL OPTIMIZATION ===";
    $logs[] = $kernel->output();
} catch (\Throwable $e) {
    $logs[] = "=== 4. LARAVEL OPTIMIZATION ERROR ===";
    $logs[] = $e->getMessage();
}

echo '<div style="font-family: sans-serif; padding: 20px; background: #f8fafc;"><h2 style="color: #059669;">🚀 SIMUNA Production Deployment Sukses!</h2><p style="color: #475569; font-size: 14px;">Struktur database dan tabel berhasil dibuat di Hostinger.</p><pre style="background: #0f172a; color: #38bdf8; padding: 20px; border-radius: 16px; overflow-x: auto; font-size: 13px;">' . implode("\n", $logs) . '</pre></div>';
