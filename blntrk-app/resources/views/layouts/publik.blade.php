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
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-urbanist { font-family: 'Urbanist', 'Plus Jakarta Sans', sans-serif; }
        .bl-d { font-family: 'Urbanist', 'Plus Jakarta Sans', sans-serif; letter-spacing: -0.02em; }
    </style>
</head>
<body class="bg-[#f4f5f6] text-stone-900 antialiased flex flex-col min-h-screen">

    <!-- Top Contact Bar (Foresta Adventure Style) -->
    <div class="bg-[#0e1b15] text-stone-300 text-xs py-2 px-4 border-b border-[#1e3a2c]/60">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-2">
            <div class="flex items-center flex-wrap gap-4 text-[11px] sm:text-xs">
                <a href="https://wa.me/{{ \App\Models\Pengaturan::ambil('no_wa_toko', '6281234567890') }}" target="_blank" class="hover:text-white flex items-center gap-1.5 transition">
                    <svg class="w-3.5 h-3.5 text-[#5edd5b]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <span>{{ \App\Models\Pengaturan::ambil('no_wa_toko', '6281234567890') }}</span>
                </a>
                <span class="hidden md:inline text-stone-600">•</span>
                <span class="hover:text-white flex items-center gap-1.5 transition">
                    <svg class="w-3.5 h-3.5 text-[#5edd5b]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>blntrkoutdoor@gmail.com</span>
                </span>
                <span class="hidden md:inline text-stone-600">•</span>
                <span class="flex items-center gap-1.5 text-stone-300">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#5edd5b] opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-[#5edd5b]"></span>
                    </span>
                    <span class="text-white/90 font-medium">Buka 24 Jam • Subang</span>
                </span>
            </div>
            <div class="flex items-center gap-3 text-[11px] sm:text-xs">
                <span class="text-stone-300">Lebih dari 60+ alat outdoor siap sewa</span>
                <a href="{{ route('katalog.index') }}" class="bg-[#5edd5b] hover:bg-[#4ed04b] text-black font-extrabold text-[11px] px-3 py-1 rounded-full flex items-center gap-1 transition shadow-xs">
                    <span>Lihat</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar (Foresta Adventure Style) -->
    <header class="bg-white/95 backdrop-blur-md border-b border-stone-200 sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 sm:h-20">
                <!-- Brand Logo (Mountain Silhouette) -->
                <div class="flex items-center">
                    <a href="{{ route('beranda') }}" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-xl bg-[#0e1b15] flex items-center justify-center text-[#5edd5b] shadow-md group-hover:scale-105 transition">
                            <!-- Mountain Silhouette SVG -->
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M14 6l4 8h-3l2 4H7l4-7 2 3 1-2v-6zm0-4L9 11l-3-5L1 18h22L14 2z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="font-urbanist text-xl font-black tracking-tight text-[#0e1b15] block leading-none">BLNTRK</span>
                            <span class="text-[10px] tracking-widest uppercase font-extrabold text-[#1e3a2c] block mt-0.5 leading-none">Outdoor Subang</span>
                        </div>
                    </a>

                    <!-- Desktop Navigation Links -->
                    <nav class="hidden md:flex ml-10 space-x-7 text-sm font-bold">
                        <a href="{{ route('beranda') }}" class="{{ request()->routeIs('beranda') ? 'text-[#0e1b15] font-extrabold border-b-2 border-[#5edd5b] pb-1' : 'text-stone-600 hover:text-stone-900' }} transition">
                            Beranda
                        </a>
                        <a href="{{ route('katalog.index') }}" class="{{ request()->routeIs('katalog.*') || request()->is('alat*') ? 'text-[#0e1b15] font-extrabold border-b-2 border-[#5edd5b] pb-1' : 'text-stone-600 hover:text-stone-900' }} transition">
                            Katalog Alat
                        </a>
                        <a href="{{ route('beranda') }}#alur-sewa" class="text-stone-600 hover:text-stone-900 transition">
                            Cara Sewa
                        </a>
                        <a href="{{ route('beranda') }}#kontak" class="text-stone-600 hover:text-stone-900 transition">
                            Kontak
                        </a>
                        @auth
                            @if(auth()->user()->punyaRole('pelanggan'))
                                <a href="{{ url('/pesanan') }}" class="{{ request()->is('pesanan*') ? 'text-[#0e1b15] font-extrabold border-b-2 border-[#5edd5b] pb-1' : 'text-stone-600 hover:text-stone-900' }} transition">
                                    Riwayat Sewa
                                </a>
                            @elseif(auth()->user()->punyaRole('admin'))
                                <a href="{{ route('admin.dashboard') }}" class="text-[#1e3a2c] hover:text-black font-extrabold transition flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>Panel Admin</span>
                                </a>
                            @elseif(auth()->user()->punyaRole('pemilik'))
                                <a href="{{ route('pemilik.dashboard') }}" class="text-blue-700 hover:text-blue-800 font-extrabold transition flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                    <span>Panel Pemilik</span>
                                </a>
                            @endif
                        @endauth
                    </nav>
                </div>

                <!-- Right Actions: Auth + Cart Button (Foresta Style) -->
                <div class="flex items-center gap-3">
                    @auth
                        <div class="relative flex items-center gap-2 text-xs sm:text-sm">
                            <span class="hidden sm:inline text-stone-600 font-medium">Halo, <strong>{{ auth()->user()->nama }}</strong></span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-stone-100 text-stone-700 border border-stone-200">
                                {{ auth()->user()->role?->label ?? 'User' }}
                            </span>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="ml-1 px-3 py-1.5 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 text-xs font-semibold border border-rose-200 transition">
                                    Keluar
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-xs sm:text-sm font-bold text-stone-700 hover:text-black px-3 py-2 transition flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-stone-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>Masuk</span>
                        </a>
                        <a href="{{ route('register') }}" class="hidden sm:inline-flex text-xs sm:text-sm font-bold text-black bg-[#5edd5b] hover:bg-[#4ed04b] px-4 py-2 rounded-xl shadow-xs transition">
                            Daftar
                        </a>
                    @endauth

                    <!-- Cart Button (Foresta Pill Button with Counter) -->
                    <a href="{{ route('katalog.index') }}" title="Lihat Keranjang & Katalog" class="flex items-center justify-center p-2.5 rounded-xl bg-white border border-stone-200 text-stone-800 shadow-xs hover:border-stone-400 transition relative">
                        <svg class="w-5 h-5 text-stone-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="absolute -top-1.5 -right-1.5 bg-rose-500 text-white font-black text-[10px] w-4 h-4 rounded-full flex items-center justify-center shadow-xs">0</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Body -->
    <main class="flex-grow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <x-alert />
            {{ $slot }}
        </div>
    </main>

    <!-- Footer Foresta Adventure Style (Deep Forest Green) -->
    <footer id="kontak" class="bg-[#0e1b15] text-stone-400 text-sm mt-16 border-t border-[#1e3a2c]">
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
                        <a href="https://wa.me/{{ \App\Models\Pengaturan::ambil('no_wa_toko', '6281234567890') }}" target="_blank" class="text-base sm:text-lg font-black text-[#5edd5b] hover:underline font-mono block">
                            {{ \App\Models\Pengaturan::ambil('no_wa_toko', '6281234567890') }}
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

</body>
</html>

