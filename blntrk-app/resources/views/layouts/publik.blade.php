@php
    $user = auth()->user();
    $noWa = \App\Models\Pengaturan::ambil('no_wa_toko', '6281234567890');
    $jmlPesananAktifNav = 0;

    $navLinks = [
        [
            'label'  => 'Beranda',
            'href'   => route('beranda'),
            'active' => request()->routeIs('beranda'),
            'badge'  => 0,
        ],
        [
            'label'  => 'Katalog Alat',
            'href'   => route('katalog.index'),
            'active' => request()->routeIs('katalog.*') || request()->is('alat*'),
            'badge'  => 0,
        ],
        [
            'label'  => 'Paket Bundling',
            'href'   => route('paket-bundling.index'),
            'active' => request()->routeIs('paket-bundling.*'),
            'badge'  => 0,
        ],
    ];

    if ($user) {
        if ($user->punyaRole('pelanggan')) {
            $jmlPesananAktifNav = $user->pelanggan?->pesanan()
                ->whereIn('status', ['menunggu', 'dibayar', 'disewa', 'terlambat'])
                ->count() ?? 0;

            $navLinks[] = [
                'label'  => 'Pesanan Saya',
                'href'   => route('pesanan.index'),
                'active' => request()->is('pesanan*'),
                'badge'  => $jmlPesananAktifNav,
            ];
        } elseif ($user->punyaRole('admin')) {
            $navLinks[] = [
                'label'  => 'Panel Admin',
                'href'   => route('admin.dashboard'),
                'active' => request()->routeIs('admin.*'),
                'badge'  => 0,
            ];
        } elseif ($user->punyaRole('pemilik')) {
            $navLinks[] = [
                'label'  => 'Panel Pemilik',
                'href'   => route('pemilik.dashboard'),
                'active' => request()->routeIs('pemilik.*'),
                'badge'  => 0,
            ];
        }
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Sewa Perlengkapan Outdoor Subang - Buka 24 Jam' }} - BLNTRK Outdoor</title>

    <!-- Google Fonts: Urbanist & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Urbanist:wght@500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-urbanist { font-family: 'Urbanist', 'Plus Jakarta Sans', sans-serif; }
        .bl-d { font-family: 'Urbanist', 'Plus Jakarta Sans', sans-serif; letter-spacing: -0.02em; }
    </style>
</head>
<body class="bg-[#f4f5f6] text-stone-900 antialiased flex flex-col min-h-screen">

    <!-- Top Contact Bar (Foresta Adventure Style) -->
    <div class="bg-[#0e1b15] text-stone-300 text-xs border-b border-[#1e3a2c]/60 print:hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-9 flex items-center justify-between gap-4">
            <div class="flex items-center gap-4 min-w-0">
                <span class="flex items-center gap-1.5 shrink-0">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#5edd5b] opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-[#5edd5b]"></span>
                    </span>
                    <span class="text-white/90 font-semibold">Buka 24 jam</span>
                    <span class="hidden sm:inline text-stone-400">· Subang</span>
                </span>

                <span class="hidden md:block h-3 w-px bg-white/15"></span>

                <a href="https://wa.me/{{ $noWa }}" target="_blank" rel="noopener"
                   class="hidden md:flex items-center gap-1.5 hover:text-white transition">
                    <svg class="w-3.5 h-3.5 text-[#5edd5b]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <span>{{ $noWa }}</span>
                </a>

                <a href="mailto:blntrkoutdoor@gmail.com"
                   class="hidden lg:flex items-center gap-1.5 hover:text-white transition">
                    <svg class="w-3.5 h-3.5 text-[#5edd5b]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>blntrkoutdoor@gmail.com</span>
                </a>
            </div>

            <a href="{{ route('katalog.index') }}"
               class="group flex items-center gap-1.5 text-stone-300 hover:text-white transition shrink-0">
                <span class="hidden sm:inline">60+ alat outdoor siap sewa</span>
                <span class="sm:hidden">60+ alat siap sewa</span>
                <span class="text-[#5edd5b] font-bold flex items-center gap-0.5">
                    Lihat
                    <svg class="w-3 h-3 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </span>
            </a>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <header x-data="{ mobileOpen: false, scrolled: false }"
            @scroll.window="scrolled = window.scrollY > 8"
            @keydown.escape.window="mobileOpen = false"
            :class="scrolled ? 'shadow-[0_8px_24px_-12px_rgba(14,27,21,0.25)] border-stone-200' : 'border-stone-200/70'"
            class="bg-white/95 backdrop-blur-xl border-b sticky top-0 z-40 transition-shadow duration-300 print:hidden">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 lg:h-[72px] gap-4">

                <!-- Brand Logo -->
                <a href="{{ route('beranda') }}" class="flex items-center gap-3 group shrink-0">
                    <div class="w-10 h-10 rounded-xl bg-[#0e1b15] flex items-center justify-center text-[#5edd5b] shadow-sm ring-1 ring-[#1e3a2c] group-hover:scale-105 transition">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M14 6l4 8h-3l2 4H7l4-7 2 3 1-2v-6zm0-4L9 11l-3-5L1 18h22L14 2z"/>
                        </svg>
                    </div>
                    <div class="leading-none">
                        <span class="font-urbanist text-xl font-black tracking-tight text-[#0e1b15] block">BLNTRK</span>
                        <span class="text-[11px] font-bold text-[#1e3a2c]/70 block mt-1">Outdoor Subang</span>
                    </div>
                </a>

                <!-- Desktop Navigation Links (Pill Style) -->
                <nav class="hidden md:flex items-center gap-1 mr-auto ml-6 lg:ml-10" aria-label="Navigasi utama">
                    @foreach ($navLinks as $link)
                        <a href="{{ $link['href'] }}"
                           @if ($link['active']) aria-current="page" @endif
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold transition
                                  {{ $link['active']
                                      ? 'bg-[#0e1b15] text-white shadow-sm'
                                      : 'text-stone-600 hover:text-[#0e1b15] hover:bg-stone-100' }}">
                            <span>{{ $link['label'] }}</span>
                            @if ($link['badge'] > 0)
                                <span class="min-w-[18px] h-[18px] px-1 rounded-full bg-[#5edd5b] text-black text-[10px] font-black leading-none flex items-center justify-center">
                                    {{ $link['badge'] }}
                                </span>
                            @endif
                        </a>
                    @endforeach
                </nav>

                <!-- Right Side Actions -->
                <div class="flex items-center gap-2 sm:gap-3">

                    <!-- Tombol Keranjang Belanja (Foresta Style) -->
                    @php
                        $jumlahKeranjang = $cartCount ?? (int) array_sum(session()->get('keranjang_sewa', []));
                    @endphp
                    <a href="{{ route('keranjang.index') }}" title="Lihat Keranjang Belanja"
                       class="relative h-10 px-3 sm:px-3.5 flex items-center gap-1.5 rounded-xl bg-white border border-stone-300 text-stone-900 hover:border-[#0e1b15] hover:text-[#0e1b15] transition shadow-xs">
                        <svg class="w-5 h-5 text-stone-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="hidden sm:inline text-xs font-bold font-urbanist">Keranjang</span>
                        <span class="min-w-[19px] h-[19px] px-1 {{ $jumlahKeranjang > 0 ? 'bg-[#5edd5b] text-black font-black' : 'bg-stone-100 text-stone-500 font-bold' }} text-[10px] rounded-full flex items-center justify-center">
                            {{ $jumlahKeranjang }}
                        </span>
                    </a>

                    @auth
                        <!-- Dropdown Profil dengan Ikon User Rapi -->
                        <div x-data="{ openProfile: false }" class="relative">
                            <button @click="openProfile = !openProfile" type="button"
                                    class="relative w-10 h-10 flex items-center justify-center rounded-xl bg-[#0e1b15] text-[#5edd5b] hover:bg-[#192e24] border border-[#1e3a2c] shadow-xs cursor-pointer transition focus:outline-none focus:ring-2 focus:ring-[#5edd5b]/60"
                                    :aria-expanded="openProfile.toString()" aria-haspopup="true"
                                    aria-label="Menu Profil"
                                    title="Menu Profil: {{ $user->nama }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <!-- Titik status aktif/online -->
                                <span class="absolute bottom-1 right-1 w-2.5 h-2.5 rounded-full bg-[#5edd5b] ring-2 ring-[#0e1b15]"></span>
                            </button>

                            <div x-show="openProfile" x-cloak
                                 @click.away="openProfile = false"
                                 @keydown.escape.window="openProfile = false"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                                 class="absolute right-0 mt-3 w-72 sm:w-80 origin-top-right bg-white rounded-2xl shadow-xl ring-1 ring-stone-200 overflow-hidden z-50 font-urbanist">

                                <!-- Identitas akun -->
                                <div class="px-4 py-3.5 bg-gradient-to-r from-stone-50 to-[#5edd5b]/10 border-b border-stone-100 flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-[#0e1b15] text-[#5edd5b] flex items-center justify-center font-black text-sm shrink-0">
                                        {{ strtoupper(substr($user->nama, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-black text-stone-900 text-sm truncate">{{ $user->nama }}</span>
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-[#5edd5b]/25 text-stone-900 border border-[#5edd5b]/40 shrink-0">
                                                {{ $user->role?->label ?? 'Pelanggan' }}
                                            </span>
                                        </div>
                                        <span class="block text-[11px] text-stone-500 truncate mt-0.5">{{ $user->email }}</span>
                                    </div>
                                </div>

                                <div class="p-2 space-y-1 text-xs">
                                    <!-- Profil Saya -->
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

                                    <!-- Sesi Login -->
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

                                <!-- Logout -->
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
                                                <span class="block text-[10px] text-rose-400 font-normal">Akhiri sesi akun ini</span>
                                            </span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}"
                           class="hidden sm:inline-flex items-center h-10 px-4 rounded-xl text-sm font-bold text-stone-700 hover:text-black hover:bg-stone-100 transition">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}"
                           class="hidden sm:inline-flex items-center h-10 px-5 rounded-xl text-sm font-extrabold text-black bg-[#5edd5b] hover:bg-[#4ed04b] shadow-sm transition">
                            Daftar
                        </a>
                    @endauth

                    <!-- Tombol menu mobile -->
                    <button type="button" @click="mobileOpen = !mobileOpen"
                            class="md:hidden w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-stone-200 text-stone-800 hover:border-[#0e1b15] transition focus:outline-none focus:ring-2 focus:ring-[#5edd5b]/60 cursor-pointer"
                            :aria-expanded="mobileOpen.toString()" aria-controls="menu-mobile" aria-label="Buka menu navigasi">
                        <svg x-show="!mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
                        <svg x-show="mobileOpen" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Backdrop Redup & Blur Saat Menu Mobile Terbuka (Bisa diklik di mana saja untuk menutup) -->
        <div x-show="mobileOpen" x-cloak
             @click="mobileOpen = false"
             x-transition:enter="transition-opacity ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 top-16 lg:top-[72px] bg-black/70 backdrop-blur-md md:hidden cursor-pointer z-30"
             aria-hidden="true"></div>

        <!-- Menu Mobile Drawer Dropdown -->
        <div id="menu-mobile" x-show="mobileOpen" x-cloak
             @click.away="mobileOpen = false"
             x-transition:enter="transition ease-out duration-250"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="relative md:hidden border-t border-stone-200 bg-white shadow-2xl z-40">
            <nav class="max-w-7xl mx-auto px-4 sm:px-6 py-3 space-y-1" aria-label="Navigasi mobile">
                    <a href="{{ route('keranjang.index') }}" @click="mobileOpen = false"
                       class="flex items-center justify-between px-4 py-3 rounded-xl text-sm font-bold transition {{ request()->routeIs('keranjang.*') ? 'bg-[#0e1b15] text-white' : 'text-stone-700 hover:bg-stone-100' }}">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span>Keranjang Sewa</span>
                        </span>
                        <span class="min-w-[20px] h-5 px-1.5 rounded-full {{ $jumlahKeranjang > 0 ? 'bg-[#5edd5b] text-black font-black' : 'bg-stone-100 text-stone-600' }} text-[11px] flex items-center justify-center">
                            {{ $jumlahKeranjang }}
                        </span>
                    </a>
                @foreach ($navLinks as $link)
                    <a href="{{ $link['href'] }}" @click="mobileOpen = false"
                       @if ($link['active']) aria-current="page" @endif
                       class="flex items-center justify-between px-4 py-3 rounded-xl text-sm font-bold transition
                              {{ $link['active']
                                  ? 'bg-[#0e1b15] text-white'
                                  : 'text-stone-700 hover:bg-stone-100' }}">
                        <span>{{ $link['label'] }}</span>
                        @if ($link['badge'] > 0)
                            <span class="min-w-[20px] h-5 px-1.5 rounded-full bg-[#5edd5b] text-black text-[11px] font-black flex items-center justify-center">
                                {{ $link['badge'] }}
                            </span>
                        @endif
                    </a>
                @endforeach

                @guest
                    <div class="grid grid-cols-2 gap-2 pt-3 mt-2 border-t border-stone-100">
                        <a href="{{ route('login') }}"
                           class="text-center px-4 py-3 rounded-xl text-sm font-bold text-stone-800 border border-stone-200 hover:bg-stone-50 transition">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}"
                           class="text-center px-4 py-3 rounded-xl text-sm font-extrabold text-black bg-[#5edd5b] hover:bg-[#4ed04b] transition">
                            Daftar
                        </a>
                    </div>
                @endguest
            </nav>
        </div>
    </header>

    <!-- Main Content Body -->
    <main class="flex-grow print:p-0 print:m-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 print:p-0 print:m-0 print:max-w-none">
            <div class="print:hidden">
                <x-alert />
            </div>
            {{ $slot }}
        </div>
    </main>

    <!-- Footer Foresta Adventure Style (Deep Forest Green) -->
    <footer id="kontak" class="bg-[#0e1b15] text-stone-400 text-sm mt-16 border-t border-[#1e3a2c] print:hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Col 1: Brand Info -->
                <div class="md:col-span-1">
                    <div class="flex items-center gap-2.5 mb-4">
                        <div class="w-9 h-9 rounded-xl bg-[#5edd5b] text-black flex items-center justify-center font-black text-sm">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M14 6l4 8h-3l2 4H7l4-7 2 3 1-2v-6zm0-4L9 11l-3-5L1 18h22L14 2z"/>
                            </svg>
                        </div>
                        <span class="text-white font-black text-lg font-urbanist tracking-tight">BLNTRK OUTDOOR</span>
                    </div>
                    <p class="text-xs text-stone-400 leading-relaxed mb-4">
                        Sewa perlengkapan camping dan mendaki berkualitas dengan proses praktis dan harga terjangkau di Desa Kumpay, Jalancagak, Subang. Buka 24 jam.
                    </p>
                    <div class="text-xs text-stone-400 space-y-1.5">
                        <p class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-[#5edd5b] shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Desa Kumpay, Kec. Jalancagak, Kab. Subang, Jawa Barat 41281</span>
                        </p>
                        <p class="flex items-center gap-2 pt-1">
                            <svg class="w-4 h-4 text-[#5edd5b] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-white font-semibold">Setiap hari, Buka 24 Jam</span>
                        </p>
                    </div>
                </div>

                <!-- Col 2: Informasi -->
                <div>
                    <h4 class="text-white font-bold text-sm mb-4 font-urbanist uppercase tracking-wider">Informasi</h4>
                    <ul class="text-xs space-y-2.5 text-stone-400">
                        <li><a href="{{ route('beranda') }}#alur-sewa" class="hover:text-[#5edd5b] transition">Cara Sewa Alat</a></li>
                        <li><a href="{{ route('katalog.index') }}" class="hover:text-[#5edd5b] transition">Katalog Perlengkapan</a></li>
                        <li><a href="{{ route('beranda') }}#ketentuan" class="hover:text-[#5edd5b] transition">Syarat & Ketentuan Jaminan</a></li>
                        <li><a href="{{ route('beranda') }}#faq" class="hover:text-[#5edd5b] transition">Pertanyaan Umum (FAQ)</a></li>
                    </ul>
                </div>

                <!-- Col 3: Ketentuan Sewa -->
                <div>
                    <h4 class="text-white font-bold text-sm mb-4 font-urbanist uppercase tracking-wider">Ketentuan Sewa</h4>
                    <ul class="text-xs space-y-2 text-stone-400">
                        <li>• Satuan sewa dihitung per hari (24 jam)</li>
                        <li>• Batas jam pengembalian pukul 12:00 siang</li>
                        <li>• Jaminan kartu identitas asli (KTP / SIM)</li>
                        <li>• Pembayaran manual di toko (Tunai / QRIS)</li>
                    </ul>
                </div>

                <!-- Col 4: Layanan Bantuan & WhatsApp -->
                <div>
                    <h4 class="text-white font-bold text-sm mb-4 font-urbanist uppercase tracking-wider">Layanan Bantuan</h4>
                    <p class="text-xs text-stone-400 mb-3">Butuh bantuan mendesak untuk pendakian malam ini? Hubungi kami langsung:</p>
                    <div class="bg-white/5 border border-white/10 rounded-2xl p-4">
                        <span class="text-[11px] text-stone-400 block mb-1">WhatsApp Customer Support</span>
                        <a href="https://wa.me/{{ $noWa }}" target="_blank" class="text-base sm:text-lg font-black text-[#5edd5b] hover:underline font-mono block">
                            {{ $noWa }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="mt-12 pt-6 border-t border-white/10 flex flex-col sm:flex-row justify-between items-center gap-3 text-center sm:text-left text-xs text-stone-500">
                <p>&copy; {{ date('Y') }} BLNTRK Outdoor. Sistem Informasi Penyewaan Alat Outdoor Berbasis Web.</p>
                <p class="text-stone-400">Desain terinspirasi oleh Foresta Adventure</p>
            </div>
        </div>
    </footer>

    <!-- Modal Konfirmasi Pop-up Konsisten -->
    <x-modal-konfirmasi />

</body>
</html>
