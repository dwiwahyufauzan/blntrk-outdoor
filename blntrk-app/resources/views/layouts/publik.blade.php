<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Penyewaan Alat Outdoor Subang' }} - BLNTRK Outdoor</title>

    <!-- Google Fonts: Bricolage Grotesque & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .bl-d { font-family: 'Bricolage Grotesque', sans-serif; letter-spacing: -0.02em; }
    </style>
</head>
<body class="bg-[#fbfbf9] text-[#14201a] antialiased flex flex-col min-h-screen">

    <!-- Top Info Bar (Nuansa Forest & Lantern) -->
    <div class="bg-[#0e1b15] text-stone-300 text-xs py-2 px-4 border-b border-[#1e3a2c]/60">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-1.5">
            <div class="flex items-center gap-2">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#f6c453] opacity-75"></span>
                    <span class="relative inline-flex h-2 w-2 rounded-full bg-[#f6c453]"></span>
                </span>
                <span class="text-white/90 font-medium">Buka 24 Jam • Desa Kumpay, Jalancagak, Subang</span>
            </div>
            <div class="flex items-center gap-4 text-[11px] sm:text-xs">
                <span class="text-stone-400">Bayar Manual di Toko (Tanpa Biaya Online)</span>
                <a href="https://wa.me/{{ \App\Models\Pengaturan::ambil('no_wa_toko', '6281234567890') }}" target="_blank" class="text-[#f6c453] hover:text-white font-bold flex items-center gap-1 transition">
                    <span>WhatsApp Toko 💬</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <header class="bg-white/95 backdrop-blur-md border-b border-stone-200 sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Brand Logo -->
                <div class="flex items-center">
                    <a href="{{ route('beranda') }}" class="flex items-center gap-2.5 group">
                        <div class="w-10 h-10 rounded-xl bg-[#0e1b15] flex items-center justify-center text-[#f6c453] shadow-md font-extrabold text-lg bl-d group-hover:scale-105 transition">
                            B
                        </div>
                        <div>
                            <span class="bl-d text-xl font-extrabold tracking-tight text-[#0e1b15] block leading-none">BLNTRK</span>
                            <span class="text-[10px] tracking-widest uppercase font-extrabold text-[#4e7a5a] block mt-0.5 leading-none">Outdoor Subang</span>
                        </div>
                    </a>

                    <!-- Desktop Nav Links -->
                    <nav class="hidden md:flex ml-10 space-x-7 text-sm font-semibold">
                        <a href="{{ route('beranda') }}" class="{{ request()->routeIs('beranda') ? 'text-[#1e3a2c] font-bold border-b-2 border-[#1e3a2c] pb-1' : 'text-stone-600 hover:text-[#1e3a2c]' }} transition">
                            Beranda
                        </a>
                        <a href="{{ route('katalog.index') }}" class="{{ request()->routeIs('katalog.*') || request()->is('alat*') ? 'text-[#1e3a2c] font-bold border-b-2 border-[#1e3a2c] pb-1' : 'text-stone-600 hover:text-[#1e3a2c]' }} transition">
                            Katalog Alat
                        </a>
                        @auth
                            @if(auth()->user()->punyaRole('pelanggan'))
                                <a href="{{ url('/pesanan') }}" class="{{ request()->is('pesanan*') ? 'text-[#1e3a2c] font-bold border-b-2 border-[#1e3a2c] pb-1' : 'text-stone-600 hover:text-[#1e3a2c]' }} transition">
                                    Riwayat Sewa Saya
                                </a>
                            @elseif(auth()->user()->punyaRole('admin'))
                                <a href="{{ route('admin.dashboard') }}" class="text-[#e0a526] hover:text-[#b87c18] font-bold transition flex items-center gap-1">
                                    <span>⚙️ Panel Admin</span>
                                </a>
                            @elseif(auth()->user()->punyaRole('pemilik'))
                                <a href="{{ route('pemilik.dashboard') }}" class="text-blue-700 hover:text-blue-800 font-bold transition flex items-center gap-1">
                                    <span>📈 Panel Pemilik</span>
                                </a>
                            @endif
                        @endauth
                    </nav>
                </div>

                <!-- Auth Buttons / User Menu -->
                <div class="flex items-center gap-3">
                    @auth
                        <div class="relative flex items-center gap-2 text-xs sm:text-sm">
                            <span class="hidden sm:inline text-stone-600 font-medium">Halo, <strong>{{ auth()->user()->nama }}</strong></span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-stone-100 text-stone-700 border border-stone-200">
                                {{ auth()->user()->role?->label ?? 'User' }}
                            </span>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="ml-2 px-3 py-1.5 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 text-xs font-semibold border border-rose-200 transition">
                                    Keluar
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-xs sm:text-sm font-bold text-stone-700 hover:text-[#1e3a2c] px-3 py-2 transition">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="text-xs sm:text-sm font-bold text-[#14201a] bg-[#f6c453] hover:bg-[#ffd36e] px-4 py-2 rounded-xl shadow-sm transition">
                            Daftar Akun
                        </a>
                    @endauth
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

    <!-- Footer Konsisten Deep Forest -->
    <footer class="bg-[#0e1b15] text-stone-400 text-sm mt-16 border-t border-[#1e3a2c]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center gap-2.5 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-[#f6c453] text-[#0e1b15] flex items-center justify-center font-extrabold text-sm bl-d">
                            B
                        </div>
                        <span class="text-white font-extrabold text-base bl-d">BLNTRK OUTDOOR</span>
                    </div>
                    <p class="text-xs text-stone-400 leading-relaxed">
                        Pusat persewaan perlengkapan camping, hiking, dan aktivitas outdoor di Desa Kumpay, Jalancagak, Subang. Buka 24 jam dengan unit selalu bersih, lengkap, dan terawat.
                    </p>
                </div>
                <div>
                    <h4 class="text-white font-bold text-sm mb-3 bl-d">Ketentuan Sewa</h4>
                    <ul class="text-xs space-y-2 text-stone-400">
                        <li>• Satuan sewa dihitung per hari (24 jam)</li>
                        <li>• Batas jam pengembalian default pukul 12:00 siang</li>
                        <li>• Jaminan kartu identitas asli (KTP/SIM) saat ambil barang</li>
                        <li>• Pembayaran manual di toko (Tunai / QRIS) saat serah terima</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-bold text-sm mb-3 bl-d">Lokasi & Kontak</h4>
                    <p class="text-xs text-stone-400 leading-relaxed mb-2">
                        📍 Desa Kumpay, Kec. Jalancagak, Kab. Subang, Jawa Barat
                    </p>
                    <p class="text-xs text-stone-300 font-semibold">
                        WhatsApp: <span class="text-[#f6c453] font-mono">{{ \App\Models\Pengaturan::ambil('no_wa_toko', '6281234567890') }}</span>
                    </p>
                </div>
            </div>
            <div class="mt-10 pt-6 border-t border-white/10 text-center text-xs text-stone-500">
                &copy; {{ date('Y') }} BLNTRK Outdoor. Sistem Informasi Penyewaan Alat Outdoor Berbasis Web.
            </div>
        </div>
    </footer>

</body>
</html>
