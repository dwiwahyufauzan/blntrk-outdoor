<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Penyewaan Alat Outdoor Subang' }} - BLNTRK Outdoor</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-stone-50 text-stone-800 antialiased flex flex-col min-h-screen">

    <!-- Top Info Bar -->
    <div class="bg-emerald-950 text-emerald-200 text-xs py-1.5 px-4">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-1">
            <div class="flex items-center gap-2">
                <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Toko Buka 24 Jam • Desa Kumpay, Jalancagak, Subang</span>
            </div>
            <div class="flex items-center gap-4">
                <span>Pembayaran Manual di Toko (Tanpa Biaya Online)</span>
                <a href="https://wa.me/{{ \App\Models\Pengaturan::ambil('no_wa_toko', '6281234567890') }}" target="_blank" class="hover:text-white font-medium flex items-center gap-1">
                    <span>WhatsApp Kami</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <header class="bg-white border-b border-stone-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Brand Logo -->
                <div class="flex items-center">
                    <a href="{{ route('beranda') }}" class="flex items-center gap-2">
                        <div class="w-10 h-10 rounded-lg bg-emerald-700 flex items-center justify-center text-white shadow-sm font-black text-lg">
                            B
                        </div>
                        <div>
                            <span class="text-lg font-black tracking-tight text-stone-900 block leading-tight">BLNTRK</span>
                            <span class="text-[10px] tracking-widest uppercase font-bold text-emerald-700 block leading-none">Outdoor Rental</span>
                        </div>
                    </a>

                    <!-- Desktop Nav Links -->
                    <nav class="hidden md:flex ml-8 space-x-6 text-sm font-medium">
                        <a href="{{ route('beranda') }}" class="{{ request()->routeIs('beranda') ? 'text-emerald-700 font-semibold' : 'text-stone-600 hover:text-emerald-700' }} transition">
                            Beranda
                        </a>
                        <a href="{{ url('/alat') }}" class="{{ request()->is('alat*') ? 'text-emerald-700 font-semibold' : 'text-stone-600 hover:text-emerald-700' }} transition">
                            Katalog Alat
                        </a>
                        @auth
                            @if(auth()->user()->punyaRole('pelanggan'))
                                <a href="{{ url('/pesanan') }}" class="{{ request()->is('pesanan*') ? 'text-emerald-700 font-semibold' : 'text-stone-600 hover:text-emerald-700' }} transition">
                                    Riwayat Sewa Saya
                                </a>
                            @elseif(auth()->user()->punyaRole('admin'))
                                <a href="{{ route('admin.dashboard') }}" class="text-amber-700 hover:text-amber-800 font-semibold transition">
                                    Panel Admin &rarr;
                                </a>
                            @elseif(auth()->user()->punyaRole('pemilik'))
                                <a href="{{ route('pemilik.dashboard') }}" class="text-blue-700 hover:text-blue-800 font-semibold transition">
                                    Panel Pemilik &rarr;
                                </a>
                            @endif
                        @endauth
                    </nav>
                </div>

                <!-- Auth Buttons / User Menu -->
                <div class="flex items-center gap-3">
                    @auth
                        <div class="relative flex items-center gap-2 text-sm">
                            <span class="hidden sm:inline text-stone-600 font-medium">Halo, <strong>{{ auth()->user()->nama }}</strong></span>
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold uppercase bg-stone-100 text-stone-700 border border-stone-300">
                                {{ auth()->user()->role?->label ?? 'User' }}
                            </span>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="ml-2 text-xs text-rose-600 hover:text-rose-800 underline font-medium">
                                    Keluar
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-stone-700 hover:text-emerald-700 px-3 py-2 transition">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="text-sm font-semibold text-white bg-emerald-700 hover:bg-emerald-800 px-4 py-2 rounded-lg shadow-sm transition">
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

    <!-- Footer -->
    <footer class="bg-stone-900 text-stone-400 text-sm mt-12 border-t border-stone-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-700 flex items-center justify-center text-white font-black text-sm">B</div>
                        <span class="text-white font-bold text-base">BLNTRK OUTDOOR</span>
                    </div>
                    <p class="text-xs text-stone-400 leading-relaxed">
                        Jasa penyewaan perlengkapan hiking, camping, dan kegiatan outdoor di Jalancagak, Subang. Buka 24 jam dengan stok lengkap dan terawat.
                    </p>
                </div>
                <div>
                    <h4 class="text-white font-semibold text-sm mb-3">Ketentuan Sewa</h4>
                    <ul class="text-xs space-y-2 text-stone-400">
                        <li>• Satuan sewa dihitung per hari (24 jam)</li>
                        <li>• Jam pengembalian default pukul 12:00 siang</li>
                        <li>• Jaminan kartu identitas asli (KTP/SIM) saat ambil barang</li>
                        <li>• Pembayaran manual di toko saat serah terima alat</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold text-sm mb-3">Lokasi & Kontak</h4>
                    <p class="text-xs text-stone-400 leading-relaxed mb-2">
                        Desa Kumpay, Kec. Jalancagak, Kab. Subang, Jawa Barat
                    </p>
                    <p class="text-xs text-stone-300 font-medium">
                        WhatsApp: {{ \App\Models\Pengaturan::ambil('no_wa_toko', '6281234567890') }}
                    </p>
                </div>
            </div>
            <div class="mt-8 pt-6 border-t border-stone-800 text-center text-xs text-stone-500">
                &copy; {{ date('Y') }} BLNTRK Outdoor. Sistem Informasi Penyewaan Alat Outdoor Berbasis Web.
            </div>
        </div>
    </footer>

</body>
</html>
