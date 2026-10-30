<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SesiController extends Controller
{
    /**
     * Tampilkan halaman informasi sesi login & riwayat keamanan autentikasi.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Ambil riwayat log aktivitas autentikasi / login pengguna
        $riwayatLogin = LogAktivitas::where('user_id', $user->id)
            ->where(function ($q) {
                $q->where('modul', 'autentikasi')
                    ->orWhere('aksi', 'like', '%login%');
            })
            ->latest('created_at')
            ->take(10)
            ->get();

        // Ambil data sesi lain jika session driver database aktif
        $sesiTersimpan = collect();
        if (config('session.driver') === 'database' && Schema::hasTable('sessions')) {
            $sesiTersimpan = DB::table('sessions')
                ->where('user_id', $user->id)
                ->orderBy('last_activity', 'desc')
                ->get();
        }

        // Parsing User Agent untuk informasi ramah pengguna
        $userAgent = $request->userAgent() ?? 'Peramban Web';
        $browser = 'Web Browser';
        $os = 'Perangkat';

        if (str_contains($userAgent, 'Windows')) {
            $os = 'Windows PC';
        } elseif (str_contains($userAgent, 'Macintosh') || str_contains($userAgent, 'Mac OS')) {
            $os = 'macOS';
        } elseif (str_contains($userAgent, 'Android')) {
            $os = 'Android Device';
        } elseif (str_contains($userAgent, 'iPhone') || str_contains($userAgent, 'iPad')) {
            $os = 'iOS Device';
        } elseif (str_contains($userAgent, 'Linux')) {
            $os = 'Linux';
        }

        if (str_contains($userAgent, 'Edg')) {
            $browser = 'Microsoft Edge';
        } elseif (str_contains($userAgent, 'Chrome')) {
            $browser = 'Google Chrome';
        } elseif (str_contains($userAgent, 'Firefox')) {
            $browser = 'Mozilla Firefox';
        } elseif (str_contains($userAgent, 'Safari') && ! str_contains($userAgent, 'Chrome')) {
            $browser = 'Apple Safari';
        }

        return view('sesi.index', [
            'user' => $user,
            'riwayatLogin' => $riwayatLogin,
            'sesiTersimpan' => $sesiTersimpan,
            'currentSessionId' => $request->session()->getId(),
            'ipSaatIni' => $request->ip() ?? '127.0.0.1',
            'userAgent' => $userAgent,
            'perangkatInfo' => "{$os} - {$browser}",
            'osInfo' => $os,
            'browserInfo' => $browser,
        ]);
    }
}

