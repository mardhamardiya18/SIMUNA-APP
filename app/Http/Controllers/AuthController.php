<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class AuthController extends Controller
{
    public function showRegister(): Response
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('form.show');
        }

        return Inertia::render('Auth/Register');
    }

    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        // Generate clean unique code like SMN-081
        $count = User::where('role', 'user')->count() + 1;
        $uniqueCode = 'SMN-'.str_pad((string) $count, 3, '0', STR_PAD_LEFT);

        $user = User::create([
            'unique_code' => $uniqueCode,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
        ]);

        Auth::login($user);

        return redirect()->route('form.show')->with('success', 'Registrasi berhasil! Silakan lengkapi form imunisasi anak Anda.');
    }

    public function showLogin(): Response
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('form.show');
        }

        return Inertia::render('Auth/Login');
    }

    public function login(LoginRequest $request)
    {
        $validated = $request->validated();
        $loginId = trim($validated['login_id']);
        $password = $validated['password'];

        $user = User::where('email', $loginId)->orWhere('phone', $loginId)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return back()->withErrors([
                'login_id' => 'Waduh, Email/No. WhatsApp atau password yang dimasukkan belum pas nih. Coba cek lagi ya!',
            ]);
        }

        Auth::login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard')->with('success', 'Selamat datang kembali, Petugas Puskesmas!');
        }

        return redirect()->route('form.show')->with('success', 'Berhasil masuk.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }
}
