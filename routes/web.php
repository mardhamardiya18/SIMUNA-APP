<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImmunizationFormController;
use App\Http\Controllers\RespondenDashboardController;
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
    if (auth()->check()) {
        return auth()->user()->isAdmin()
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
