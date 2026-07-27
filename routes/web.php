<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImmunizationFormController;
use App\Http\Controllers\RespondenDashboardController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Main Page redirect
Route::get('/', function () {
    if (Auth::check()) {
        /** @var User $user */
        $user = Auth::user();

        return $user->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('form.show');
    }

    return redirect()->route('register');
})->name('home');

// Authenticated Routes (User & Admin)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Responden Form & Confirmation
    Route::get('/form', [ImmunizationFormController::class, 'showForm'])->name('form.show');
    Route::post('/form', [ImmunizationFormController::class, 'submitForm'])->name('form.submit');
    Route::get('/confirmation', [ImmunizationFormController::class, 'showConfirmation'])->name('form.confirmation');

    // Responden Dashboard
    Route::get('/dashboard', [RespondenDashboardController::class, 'index'])->name('dashboard');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->as('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::delete('/respondents/{id}', [AdminDashboardController::class, 'destroy'])->name('respondents.destroy');
});

// Production Deployment & Artisan Helper Routes (Protected by DEPLOY_SECRET)
Route::prefix('artisan-runner')->group(function () {
    $validateSecret = function (Request $request) {
        $secret = env('DEPLOY_SECRET', 'SimunaSecure2026!');
        if ($request->query('secret') !== $secret) {
            abort(403, 'Akses Ditolak: Secret Token Tidak Valid.');
        }
    };

    // Full Deploy: Migrate + Seed + Optimize
    Route::get('/deploy', function (Request $request) use ($validateSecret) {
        $validateSecret($request);

        $logs = [];

        // 1. Run Database Migration
        Artisan::call('migrate', ['--force' => true]);
        $logs[] = '=== 1. MIGRATE DATABASE ===';
        $logs[] = Artisan::output();

        // 2. Run Database Seeder
        Artisan::call('db:seed', ['--class' => 'ImmunizationSeeder', '--force' => true]);
        $logs[] = '=== 2. SEEDING VACCINE DATA ===';
        $logs[] = Artisan::output();

        // 3. Create Storage Link (if missing)
        try {
            Artisan::call('storage:link');
            $logs[] = '=== 3. STORAGE LINK ===';
            $logs[] = Artisan::output();
        } catch (Throwable $e) {
            $logs[] = '=== 3. STORAGE LINK ===';
            $logs[] = $e->getMessage();
        }

        // 4. Optimize Laravel Cache
        Artisan::call('optimize');
        $logs[] = '=== 4. LARAVEL OPTIMIZATION ===';
        $logs[] = Artisan::output();

        return response('<div style="font-family: sans-serif; padding: 20px; background: #f8fafc;"><h2 style="color: #059669;">🚀 SIMUNA Production Deployment Sukses!</h2><pre style="background: #0f172a; color: #38bdf8; padding: 20px; border-radius: 16px; overflow-x: auto; font-size: 13px;">'.implode("\n", $logs).'</pre></div>');
    });

    // Clear All Caches Only
    Route::get('/clear-cache', function (Request $request) use ($validateSecret) {
        $validateSecret($request);

        Artisan::call('optimize:clear');
        $output = Artisan::output();

        return response('<div style="font-family: sans-serif; padding: 20px; background: #f8fafc;"><h2 style="color: #059669;">⚡ Cache Cleared!</h2><pre style="background: #0f172a; color: #38bdf8; padding: 20px; border-radius: 16px; overflow-x: auto; font-size: 13px;">'.$output.'</pre></div>');
    });
});
