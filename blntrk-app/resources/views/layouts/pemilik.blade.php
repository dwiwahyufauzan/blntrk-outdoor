<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Panel Pemilik' }} - BLNTRK Outdoor</title>

    <!-- Google Fonts: Bricolage Grotesque & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .bl-d { font-family: 'Bricolage Grotesque', sans-serif; letter-spacing: -0.02em; }
    </style>
</head>
<body x-data="{ mobileNav: false }" class="bg-[#f4f5f6] text-[#14201a] antialiased min-h-screen flex flex-col">

    <!-- Top Unified Navbar (Menyambungkan Logo Sidebar dengan Navbar) -->
    <header class="h-16 bg-[#0e1b15] text-white border-b border-stone-800 sticky top-0 z-40 flex items-center justify-between shadow-xs">
        <!-- Area Logo Brand: Sejajar Sempurna di Atas Sidebar (w-64 di Desktop) -->
        <div class="flex items-center justify-between w-auto md:w-64 shrink-0 h-16 px-4 sm:px-6 md:border-r md:border-stone-800">
            <a href="{{ route('pemilik.dashboard') }}" class="flex items-center gap-3 group">
                <div class="w-9 h-9 rounded-xl bg-[#5edd5b] flex items-center justify-center text-black font-extrabold text-sm shadow-sm group-hover:scale-105 transition">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M8 3l4 8 5-5 5 15H2L8 3z"/>
                    </svg>
                </div>
                <div>
                    <span class="font-extrabold text-sm text-white tracking-wide block leading-tight">BLNTRK OUTDOOR</span>
                    <span class="text-[10px] text-[#5edd5b] font-extrabold tracking-wider uppercase block">Panel Pemilik Toko</span>
                </div>
            </a>

            <!-- Tombol Toggle Mobile Nav -->
            <button type="button" @click="mobileNav = !mobileNav" class="md:hidden text-stone-300 hover:text-white p-1.5 rounded-lg focus:outline-none" aria-label="Toggle Menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>

        <!-- Sisi Kanan Navbar: Kontrol Web & Dropdown Profil Pemilik -->
        <div class="flex-1 flex items-center justify-end px-4 sm:px-6 h-16 gap-3 sm:gap-4">
            <a href="{{ route('beranda') }}" target="_blank"
               class="text-xs text-stone-300 hover:text-white font-bold inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition shadow-2xs">
                <span>Lihat Web Utama</span>
                <svg class="w-3.5 h-3.5 text-[#5edd5b]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>

            <!-- Dropdown Profil Pemilik -->
            <div x-data="{ openProfile: false }" class="relative">
                <button @click="openProfile = !openProfile" type="button"
                        class="relative w-10 h-10 flex items-center justify-center rounded-xl bg-[#14251e] hover:bg-[#1b3329] text-[#5edd5b] border border-[#234234] shadow-xs cursor-pointer transition focus:outline-none focus:ring-2 focus:ring-[#5edd5b]/60"
                        :aria-expanded="openProfile.toString()" aria-haspopup="true"
                        aria-label="Menu Profil Pemilik"
                        title="Menu Profil: {{ auth()->user()->nama }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="absolute bottom-1 right-1 w-2.5 h-2.5 rounded-full bg-[#5edd5b] ring-2 ring-[#0e1b15]"></span>
                </button>

                <!-- Dropdown Menu Box -->
                <div x-show="openProfile" x-cloak
                     @click.away="openProfile = false"
                     @keydown.escape.window="openProfile = false"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                     class="absolute right-0 mt-3 w-72 sm:w-80 origin-top-right bg-white rounded-2xl shadow-xl ring-1 ring-stone-200 overflow-hidden z-50 text-stone-900 font-urbanist">

                    <!-- Identitas Akun Pemilik -->
                    <div class="px-4 py-3.5 bg-gradient-to-r from-stone-50 to-[#5edd5b]/10 border-b border-stone-100 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#0e1b15] text-[#5edd5b] flex items-center justify-center font-black text-sm shrink-0">
                            {{ strtoupper(substr(auth()->user()->nama, 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5">
                                <span class="font-black text-stone-900 text-sm truncate">{{ auth()->user()->nama }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-[#5edd5b]/25 text-stone-900 border border-[#5edd5b]/40 shrink-0 uppercase">
                                    {{ auth()->user()->role?->label ?? 'Pemilik Toko' }}
                                </span>
                            </div>
                            <span class="block text-[11px] text-stone-500 truncate mt-0.5">{{ auth()->user()->email }}</span>
                        </div>
                    </div>

                    <div class="p-2 space-y-1 text-xs">
                        <a href="{{ route('profile.edit') }}" @click="openProfile = false"
                           class="group flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-stone-50 transition">
                            <span class="w-9 h-9 rounded-lg bg-stone-100 group-hover:bg-[#5edd5b]/20 text-stone-600 group-hover:text-emerald-800 flex items-center justify-center transition shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </span>
                            <span class="flex-1 min-w-0">
                                <span class="block font-extrabold text-stone-900">Profil Saya</span>
                                <span class="block text-[10px] text-stone-400 font-normal">Akses data diri dan pengaturan akun</span>
                            </span>
                            <svg class="w-4 h-4 text-stone-300 group-hover:text-stone-600 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>

                        <a href="{{ route('sesi.index') }}" @click="openProfile = false"
                           class="group flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-stone-50 transition">
                            <span class="w-9 h-9 rounded-lg bg-stone-100 group-hover:bg-sky-100 text-stone-600 group-hover:text-sky-800 flex items-center justify-center transition shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </span>
                            <span class="flex-1 min-w-0">
                                <span class="flex items-center gap-1.5">
                                    <span class="font-extrabold text-stone-900">Sesi Login</span>
                                    <span class="px-1.5 py-0.5 rounded-full text-[9px] font-black bg-emerald-100 text-emerald-800 uppercase">AKTIF</span>
                                </span>
                                <span class="block text-[10px] text-stone-400 font-normal">Status & riwayat perangkat</span>
                            </span>
                            <svg class="w-4 h-4 text-stone-300 group-hover:text-stone-600 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    <div class="border-t border-stone-100 p-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="group w-full text-left flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-rose-50 text-rose-700 transition cursor-pointer">
                                <span class="w-9 h-9 rounded-lg bg-rose-100/70 group-hover:bg-rose-100 text-rose-600 flex items-center justify-center transition shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                </span>
                                <span class="flex-1">
                                    <span class="block text-xs font-extrabold">Logout</span>
                                    <span class="block text-[10px] text-rose-400 font-normal">Akhiri sesi akun pemilik</span>
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Drawer Mobile Sidebar (Layar HP/Tablet) -->
    <div x-show="mobileNav" x-cloak class="fixed inset-0 z-50 md:hidden flex">
        <div @click="mobileNav = false" class="fixed inset-0 bg-black/60 backdrop-blur-xs"></div>
        <aside class="relative w-72 max-w-[80vw] bg-[#0e1b15] text-stone-300 flex flex-col justify-between p-4 shadow-2xl z-10 border-r border-stone-800">
            <div class="flex items-center justify-between pb-4 border-b border-stone-800 mb-4">
                <span class="font-extrabold text-sm text-white">Menu Monitoring Usaha</span>
                <button type="button" @click="mobileNav = false" class="text-stone-400 hover:text-white p-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <nav class="space-y-1 text-xs font-semibold overflow-y-auto flex-1">
                <div class="text-[10px] font-bold uppercase tracking-wider text-stone-500 px-3 py-1.5">Monitoring</div>
                <a href="{{ route('pemilik.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('pemilik.dashboard') ? 'bg-[#14251e] text-white font-extrabold border-l-4 border-[#5edd5b]' : 'hover:bg-[#14251e]/60 text-stone-300' }} transition">
                    <svg class="w-4 h-4 text-[#5edd5b]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    <span>Dashboard Metrik</span>
                </a>
                <a href="{{ url('/pemilik/laporan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->is('pemilik/laporan*') ? 'bg-[#14251e] text-white font-extrabold border-l-4 border-[#5edd5b]' : 'hover:bg-[#14251e]/60 text-stone-300' }} transition">
                    <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Laporan Usaha & PDF</span>
                </a>

                <div class="text-[10px] font-bold uppercase tracking-wider text-stone-500 px-3 pt-3 pb-1.5">Kontrol & Kebijakan</div>
                <a href="{{ url('/pemilik/admin') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->is('pemilik/admin*') ? 'bg-[#14251e] text-white font-extrabold border-l-4 border-[#5edd5b]' : 'hover:bg-[#14251e]/60 text-stone-300' }} transition">
                    <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>Kelola Akun Admin</span>
                </a>
                <a href="{{ url('/pemilik/pengaturan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->is('pemilik/pengaturan*') ? 'bg-[#14251e] text-white font-extrabold border-l-4 border-[#5edd5b]' : 'hover:bg-[#14251e]/60 text-stone-300' }} transition">
                    <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Pengaturan Toko & Denda</span>
                </a>
                <a href="{{ route('pemilik.log-aktivitas.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->is('pemilik/log*') ? 'bg-[#14251e] text-white font-extrabold border-l-4 border-[#5edd5b]' : 'hover:bg-[#14251e]/60 text-stone-300' }} transition">
                    <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Log Aktivitas Sistem</span>
                </a>
            </nav>
        </aside>
    </div>

    <!-- Body Layout: Sidebar Desktop + Area Konten -->
    <div class="flex-1 flex min-h-0">
        <!-- Sidebar Desktop (Tema Deep Forest #0e1b15) -->
        <aside class="w-64 bg-[#0e1b15] text-stone-300 flex-shrink-0 flex flex-col justify-between hidden md:flex border-r border-stone-800">
            <nav class="p-4 space-y-1 text-xs font-semibold overflow-y-auto">
                <div class="text-[10px] font-bold uppercase tracking-wider text-stone-500 px-3 py-2">Monitoring</div>
                
                <a href="{{ route('pemilik.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('pemilik.dashboard') ? 'bg-[#14251e] text-white font-extrabold border-l-4 border-[#5edd5b]' : 'hover:bg-[#14251e]/60 text-stone-300' }} transition">
                    <svg class="w-4 h-4 text-[#5edd5b]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    <span>Dashboard Metrik</span>
                </a>

                <a href="{{ url('/pemilik/laporan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->is('pemilik/laporan*') ? 'bg-[#14251e] text-white font-extrabold border-l-4 border-[#5edd5b]' : 'hover:bg-[#14251e]/60 text-stone-300' }} transition">
                    <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Laporan Usaha & PDF</span>
                </a>

                <div class="text-[10px] font-bold uppercase tracking-wider text-stone-500 px-3 pt-4 pb-2">Kontrol & Kebijakan</div>

                <a href="{{ url('/pemilik/admin') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->is('pemilik/admin*') ? 'bg-[#14251e] text-white font-extrabold border-l-4 border-[#5edd5b]' : 'hover:bg-[#14251e]/60 text-stone-300' }} transition">
                    <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>Kelola Akun Admin</span>
                </a>

                <a href="{{ url('/pemilik/pengaturan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->is('pemilik/pengaturan*') ? 'bg-[#14251e] text-white font-extrabold border-l-4 border-[#5edd5b]' : 'hover:bg-[#14251e]/60 text-stone-300' }} transition">
                    <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Pengaturan Toko & Denda</span>
                </a>

                <a href="{{ route('pemilik.log-aktivitas.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->is('pemilik/log*') ? 'bg-[#14251e] text-white font-extrabold border-l-4 border-[#5edd5b]' : 'hover:bg-[#14251e]/60 text-stone-300' }} transition">
                    <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Log Aktivitas Sistem</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 bg-[#f4f5f6]">
            <main class="flex-1 p-6 md:p-8 overflow-y-auto">
                <x-alert />
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Modal Konfirmasi Pop-up Konsisten -->
    <x-modal-konfirmasi />

</body>
</html>
