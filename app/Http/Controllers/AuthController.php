<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\LogAktivitas;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login
     */
    public function showLogin()
    {
        // Jika sudah login, redirect ke dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Proses login
     */
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username harus diisi',
            'password.required' => 'Password harus diisi',
        ]);

        // Ambil credentials
        $credentials = $request->only('username', 'password');

        // Coba login menggunakan Auth::attempt
        // Laravel akan otomatis compare password yang di-hash
        if (Auth::attempt($credentials)) {
            // Regenerate session untuk keamanan
            $request->session()->regenerate();

            // Log aktivitas login
            $this->logActivity('Login', 'User ' . Auth::user()->username . ' berhasil login');

            // Redirect ke dashboard dengan pesan sukses
            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang, ' . Auth::user()->username . '!');
        }

        // Jika login gagal
        return back()->withErrors([
            'login' => 'Username atau password salah!',
        ])->onlyInput('username');
    }

    /**
     * Proses logout
     */
    public function logout(Request $request)
    {
        // Log aktivitas logout sebelum logout
        if (Auth::check()) {
            $this->logActivity('Logout', 'User ' . Auth::user()->username . ' logout');
        }

        // Logout user
        Auth::logout();

        // Invalidate session
        $request->session()->invalidate();

        // Regenerate CSRF token
        $request->session()->regenerateToken();

        // Redirect ke login dengan pesan
        return redirect()->route('login')
            ->with('success', 'Anda berhasil logout');
    }

    /**
     * Helper function untuk log aktivitas
     */
    private function logActivity($modul, $aktivitas)
    {
        try {
            LogAktivitas::create([
                'user_id' => Auth::id(),
                'modul' => $modul,
                'aktivitas' => $aktivitas,
            ]);
        } catch (\Exception $e) {
            // Silent fail jika log gagal, tidak mengganggu proses utama
            \Log::error('Failed to log activity: ' . $e->getMessage());
        }
    }
}