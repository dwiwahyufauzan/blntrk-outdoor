<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\LogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Tampilkan form login.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Proses otentikasi login dengan pengecekan keaktifan akun & pengalihan role.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = $request->user();

        // Validasi keaktifan akun
        if (! $user->is_aktif) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Akun Anda sedang dinonaktifkan oleh pengelola toko. Silakan hubungi admin BLNTRK Outdoor.',
            ]);
        }

        $request->session()->regenerate();

        $role = $user->role?->nama;

        LogService::catat(
            $user,
            'login',
            'autentikasi',
            $user->id,
            "Pengguna {$user->nama} ({$role}) berhasil login ke sistem."
        );

        return redirect()->intended(match ($role) {
            'pemilik' => route('pemilik.dashboard'),
            'admin' => route('admin.dashboard'),
            default => route('beranda'),
        });
    }

    /**
     * Keluar dari sesi (logout).
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user) {
            LogService::catat(
                $user,
                'logout',
                'autentikasi',
                $user->id,
                "Pengguna {$user->nama} keluar dari sistem."
            );
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
