<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Panel Admin' }} - BLNTRK Outdoor</title>

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
<body class="bg-stone-100 text-stone-800 antialiased flex min-h-screen">

    <!-- Sidebar Admin -->
    <aside class="w-64 bg-stone-900 text-stone-300 flex-shrink-0 flex flex-col justify-between hidden md:flex border-r border-stone-800">
        <div>
            <!-- Header Brand -->
            <div class="h-16 flex items-center px-6 bg-stone-950 border-b border-stone-800 gap-3">
                <div class="w-8 h-8 rounded bg-emerald-600 flex items-center justify-center text-white font-black text-sm">B</div>
                <div>
                    <span class="font-bold text-sm text-white tracking-wide block leading-tight">BLNTRK OUTDOOR</span>
                    <span class="text-[10px] text-emerald-400 font-semibold tracking-wider uppercase block">Panel Admin</span>
                </div>
            </div>

            <!-- Nav Links -->
            <nav class="p-4 space-y-1 text-sm font-medium">
                <div class="text-[10px] font-bold uppercase tracking-wider text-stone-500 px-3 py-2">Utama</div>
                
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-700 text-white font-semibold' : 'hover:bg-stone-800 text-stone-300' }} transition">
                    <span>📊</span>
                    <span>Dashboard</span>
                </a>

                <div class="text-[10px] font-bold uppercase tracking-wider text-stone-500 px-3 pt-4 pb-2">Transaksi Rental</div>
                
                <a href="{{ url('/admin/pesanan') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->is('admin/pesanan*') ? 'bg-emerald-700 text-white font-semibold' : 'hover:bg-stone-800 text-stone-300' }} transition">
                    <span>🛒</span>
                    <span>Daftar Pesanan</span>
                </a>

                <a href="{{ url('/admin/pengembalian') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->is('admin/pengembalian*') ? 'bg-emerald-700 text-white font-semibold' : 'hover:bg-stone-800 text-stone-300' }} transition">
                    <span>🔄</span>
                    <span>Pengembalian & Denda</span>
                </a>

                <div class="text-[10px] font-bold uppercase tracking-wider text-stone-500 px-3 pt-4 pb-2">Katalog & Stok</div>

                <a href="{{ url('/admin/alat') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->is('admin/alat*') ? 'bg-emerald-700 text-white font-semibold' : 'hover:bg-stone-800 text-stone-300' }} transition">
                    <span>⛺</span>
                    <span>Data Alat Outdoor</span>
                </a>

                <a href="{{ url('/admin/kategori') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->is('admin/kategori*') ? 'bg-emerald-700 text-white font-semibold' : 'hover:bg-stone-800 text-stone-300' }} transition">
                    <span>📂</span>
                    <span>Kategori Alat</span>
                </a>

                <a href="{{ url('/admin/perawatan') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->is('admin/perawatan*') ? 'bg-emerald-700 text-white font-semibold' : 'hover:bg-stone-800 text-stone-300' }} transition">
                    <span>🛠️</span>
                    <span>Perawatan Alat</span>
                </a>

                <div class="text-[10px] font-bold uppercase tracking-wider text-stone-500 px-3 pt-4 pb-2">Pelanggan & User</div>

                <a href="{{ url('/admin/pelanggan') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->is('admin/pelanggan*') ? 'bg-emerald-700 text-white font-semibold' : 'hover:bg-stone-800 text-stone-300' }} transition">
                    <span>👥</span>
                    <span>Data Pelanggan</span>
                </a>

                <a href="{{ url('/admin/user') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->is('admin/user*') ? 'bg-emerald-700 text-white font-semibold' : 'hover:bg-stone-800 text-stone-300' }} transition">
                    <span>🔑</span>
                    <span>Kelola Akun User</span>
                </a>

                <div class="text-[10px] font-bold uppercase tracking-wider text-stone-500 px-3 pt-4 pb-2">Laporan</div>

                <a href="{{ url('/admin/laporan') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->is('admin/laporan*') ? 'bg-emerald-700 text-white font-semibold' : 'hover:bg-stone-800 text-stone-300' }} transition">
                    <span>📄</span>
                    <span>Laporan & Cetak PDF</span>
                </a>
            </nav>
        </div>

        <!-- User info footer in sidebar -->
        <div class="p-4 border-t border-stone-800 bg-stone-950/60">
            <div class="text-xs text-stone-400">Login sebagai:</div>
            <div class="text-sm font-bold text-white truncate">{{ auth()->user()->nama }}</div>
            <div class="text-[11px] text-emerald-400">Admin Toko</div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Top Navbar -->
        <header class="h-16 bg-white border-b border-stone-200 flex items-center justify-between px-6 sticky top-0 z-30 shadow-sm">
            <div class="flex items-center gap-3">
                <span class="text-sm font-semibold text-stone-600">Panel Operasional Toko</span>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('beranda') }}" target="_blank" class="text-xs text-stone-600 hover:text-emerald-700 font-medium flex items-center gap-1">
                    <span>Lihat Web Utama ↗</span>
                </a>

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 text-xs font-semibold border border-rose-200 transition">
                        Keluar
                    </button>
                </form>
            </div>
        </header>

        <!-- Page Body Content -->
        <main class="flex-1 p-6 md:p-8 overflow-y-auto">
            <x-alert />
            {{ $slot }}
        </main>
    </div>

</body>
</html>
