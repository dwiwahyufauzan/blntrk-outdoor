@php
    $user = auth()->user();
    $isAdmin = $user?->punyaRole('admin');
    $isPemilik = $user?->punyaRole('pemilik');
    $layoutComponent = $isAdmin ? 'layouts.admin' : ($isPemilik ? 'layouts.pemilik' : 'layouts.publik');
    $backRoute = $isAdmin ? route('admin.dashboard') : ($isPemilik ? route('pemilik.dashboard') : route('beranda'));
    $backLabel = $isAdmin ? 'Dashboard Admin' : ($isPemilik ? 'Dashboard Pemilik' : 'Beranda');
@endphp

<x-dynamic-component :component="$layoutComponent">
    <x-slot:title>Sesi Login & Keamanan Akun - {{ $isAdmin ? 'Panel Admin' : ($isPemilik ? 'Panel Pemilik' : 'BLNTRK Outdoor') }}</x-slot:title>

    <div class="{{ $isAdmin || $isPemilik ? 'max-w-5xl mx-auto space-y-6 pb-12 font-urbanist' : 'bg-[#f4f5f6] min-h-screen py-10 sm:py-14 font-urbanist' }}">
        <div class="{{ $isAdmin || $isPemilik ? 'space-y-6' : 'max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6' }}">

            <!-- Navigasi Balik & Tombol Profil -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <a href="{{ $backRoute }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-stone-500 hover:text-[#0e1b15] transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali ke {{ $backLabel }}</span>
                </a>

                <div class="flex items-center gap-2">
                    <a href="{{ route('profile.edit') }}"
                       class="px-4 py-2 rounded-xl bg-white hover:bg-stone-50 text-stone-800 border border-stone-200 text-xs font-bold transition inline-flex items-center gap-2 shadow-2xs">
                        <svg class="w-4 h-4 text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Ke Halaman Profil</span>
                    </a>
                </div>
            </div>

            <!-- Page Header -->
            <div class="pb-4 border-b border-stone-200/90">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-widest bg-[#0e1b15] text-[#5edd5b] mb-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#5edd5b]"></span>
                    Keamanan Autentikasi {{ $isAdmin ? 'Admin' : ($isPemilik ? 'Pemilik' : 'Akun') }}
                </span>
                <h1 class="text-2xl sm:text-3xl font-black text-[#0e1b15] tracking-tight">
                    Sesi Login & Perangkat Aktif
                </h1>
                <p class="text-xs sm:text-sm text-stone-500 mt-1">
                    Pantau status perangkat yang sedang terhubung, rincian sesi autentikasi, dan riwayat login akun {{ $isAdmin ? 'admin toko' : ($isPemilik ? 'pemilik toko' : 'Anda') }}
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Kolom Kiri: Sesi Aktif Saat Ini & Riwayat Login (2 Kolom Lebar) -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Kartu 1: Sesi Login Perangkat Saat Ini -->
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-stone-200/90 shadow-sm relative overflow-hidden">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-stone-100">
                            <div class="flex items-center gap-3.5">
                                <div class="w-12 h-12 rounded-2xl bg-[#0e1b15] text-[#5edd5b] flex items-center justify-center shadow-xs shrink-0">
                                    <svg class="w-6 h-6 text-[#5edd5b]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h2 class="text-base sm:text-lg font-black text-[#0e1b15]">
                                            Perangkat Ini
                                        </h2>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-[#5edd5b]/20 text-stone-900 border border-[#5edd5b]/40">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#5edd5b] animate-ping"></span>
                                            Sesi Aktif
                                        </span>
                                    </div>
                                    <span class="text-xs text-stone-500 font-medium block mt-0.5">
                                        {{ $perangkatInfo }}
                                    </span>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="px-4 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold transition inline-flex items-center gap-1.5 cursor-pointer">
                                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    <span>Keluar Sesi Ini</span>
                                </button>
                            </form>
                        </div>

                        <!-- Grid Rincian Sesi -->
                        <div class="pt-6 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div class="p-4 rounded-2xl bg-stone-50/80 border border-stone-200/70">
                                <span class="text-stone-400 text-[10px] uppercase font-bold tracking-wider block mb-1">Alamat IP</span>
                                <div class="font-mono font-bold text-stone-900 text-sm">
                                    {{ $ipSaatIni }}
                                </div>
                                <span class="text-stone-400 text-[11px]">Koneksi jaringan terverifikasi</span>
                            </div>

                            <div class="p-4 rounded-2xl bg-stone-50/80 border border-stone-200/70">
                                <span class="text-stone-400 text-[10px] uppercase font-bold tracking-wider block mb-1">Durasi Inaktivitas</span>
                                <div class="font-bold text-stone-900 text-sm">
                                    120 Menit (2 Jam)
                                </div>
                                <span class="text-stone-400 text-[11px]">Otomatis logout jika tidak aktif</span>
                            </div>

                            <div class="p-4 rounded-2xl bg-stone-50/80 border border-stone-200/70">
                                <span class="text-stone-400 text-[10px] uppercase font-bold tracking-wider block mb-1">ID Sesi Aktif</span>
                                <div class="font-mono font-bold text-stone-900 text-xs truncate">
                                    {{ substr($currentSessionId, 0, 16) }}...
                                </div>
                                <span class="text-stone-400 text-[11px]">Enkripsi token sesi Laravel</span>
                            </div>

                            <div class="p-4 rounded-2xl bg-stone-50/80 border border-stone-200/70">
                                <span class="text-stone-400 text-[10px] uppercase font-bold tracking-wider block mb-1">Akses Saat Ini</span>
                                <div class="font-mono font-bold text-stone-900 text-xs">
                                    {{ now()->translatedFormat('d M Y, H:i') }} WIB
                                </div>
                                <span class="text-stone-400 text-[11px]">Waktu server sinkron</span>
                            </div>
                        </div>

                        <!-- Info User Agent Lengkap -->
                        <div class="mt-4 p-3 rounded-2xl bg-stone-50 border border-stone-200/60 text-[11px] text-stone-500 font-mono break-all">
                            <span class="text-stone-400 uppercase tracking-wider text-[9px] font-bold block mb-0.5">User Agent Header:</span>
                            {{ $userAgent }}
                        </div>
                    </div>

                    <!-- Kartu 2: Riwayat Login & Log Aktivitas Autentikasi -->
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-stone-200/90 shadow-sm">
                        <div class="flex items-center justify-between pb-4 mb-4 border-b border-stone-100">
                            <div>
                                <h3 class="text-base font-black text-[#0e1b15]">
                                    Riwayat Aktivitas Login
                                </h3>
                                <p class="text-xs text-stone-500 mt-0.5">Catatan waktu login dan alamat IP untuk mendeteksi aktivitas mencurigakan</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase bg-stone-100 text-stone-700">
                                {{ $riwayatLogin->count() }} Aktivitas Terakhir
                            </span>
                        </div>

                        @if($riwayatLogin->isNotEmpty())
                            <div class="divide-y divide-stone-100">
                                @foreach($riwayatLogin as $log)
                                    <div class="py-3.5 first:pt-0 last:pb-0 flex items-center justify-between gap-4 text-xs">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                            </div>
                                            <div>
                                                <span class="font-bold text-stone-900 block">
                                                    {{ $log->deskripsi ?? 'Pengguna berhasil masuk ke sistem' }}
                                                </span>
                                                <span class="text-stone-400 font-mono text-[11px]">
                                                    IP: {{ $log->ip_address ?? '127.0.0.1' }} &bull; Modul: {{ strtoupper($log->modul) }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="text-right shrink-0">
                                            <span class="font-mono text-stone-700 font-bold block text-[11px]">
                                                {{ $log->created_at->translatedFormat('d M Y') }}
                                            </span>
                                            <span class="text-stone-400 font-mono text-[10px]">
                                                {{ $log->created_at->format('H:i:s') }} WIB
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="py-8 text-center text-xs text-stone-400">
                                <svg class="w-10 h-10 text-stone-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Belum ada rekaman riwayat login sebelumnya. Sesi saat ini telah tercatat secara otomatis.</span>
                            </div>
                        @endif
                    </div>

                </div>

                <!-- Kolom Kanan: Identitas Akun & Tips Keamanan (1 Kolom) -->
                <div class="space-y-6">

                    <!-- Kartu Info Akun Login -->
                    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-stone-200/90 shadow-sm">
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-stone-400 block mb-3">Akun Terautentikasi</span>
                        <div class="flex items-center gap-3 pb-4 border-b border-stone-100">
                            <div class="w-11 h-11 rounded-2xl bg-[#0e1b15] text-[#5edd5b] flex items-center justify-center font-black text-base shadow-xs shrink-0">
                                {{ strtoupper(substr($user->nama, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <div class="font-black text-[#0e1b15] text-sm truncate">{{ $user->nama }}</div>
                                <div class="text-stone-500 font-mono text-[11px] truncate">{{ $user->email }}</div>
                            </div>
                        </div>

                        <div class="pt-4 space-y-2.5 text-xs">
                            <div class="flex justify-between items-center">
                                <span class="text-stone-500">Peran Akun:</span>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-[#5edd5b]/20 text-stone-900 border border-[#5edd5b]/40">
                                    {{ $user->role?->label ?? 'User' }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-stone-500">Status Akun:</span>
                                <span class="font-bold text-emerald-700 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Aktif & Terverifikasi
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-stone-500">Terdaftar Sejak:</span>
                                <span class="font-mono text-stone-700">{{ $user->created_at?->translatedFormat('d M Y') ?? '-' }}</span>
                            </div>
                        </div>

                        <div class="mt-5 pt-4 border-t border-stone-100">
                            <a href="{{ route('profile.edit') }}"
                               class="w-full py-2.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs font-bold transition flex items-center justify-center gap-2">
                                <svg class="w-3.5 h-3.5 text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                <span>Kelola Profil & Kata Sandi</span>
                            </a>
                        </div>
                    </div>

                    <!-- Kartu Tips Keamanan Akun Foresta -->
                    <div class="bg-[#0e1b15] text-white rounded-3xl p-6 sm:p-7 border border-[#1e3a2c] shadow-md space-y-4">
                        <div class="flex items-center gap-2 text-[#5edd5b]">
                            <svg class="w-5 h-5 text-[#5edd5b]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <h4 class="font-extrabold text-sm uppercase tracking-wider font-urbanist">Tips Keamanan Akun</h4>
                        </div>

                        <ul class="space-y-3 text-xs text-stone-300 leading-relaxed">
                            <li class="flex items-start gap-2">
                                <span class="text-[#5edd5b] font-bold">&bull;</span>
                                <span><strong>Logout di Perangkat Umum:</strong> Selalu keluar dari sesi jika menggunakan komputer rental atau perangkat bersama.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-[#5edd5b] font-bold">&bull;</span>
                                <span><strong>Jaga Kerahasiaan Kata Sandi:</strong> Jangan membagikan informasi login kepada siapa pun untuk melindungi keamanan operasional toko.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-[#5edd5b] font-bold">&bull;</span>
                                <span><strong>Batas Waktu Sesi:</strong> Sistem mengakhiri sesi otomatis setelah 120 menit tidak ada aktivitas guna mencegah akses tidak berwenang.</span>
                            </li>
                        </ul>
                    </div>

                </div>

            </div>

        </div>
    </div>
</x-dynamic-component>
