<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Panel Admin' }} - BLNTRK Outdoor</title>

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
<body class="bg-[#fbfbf9] text-[#14201a] antialiased flex min-h-screen">

    <!-- Sidebar Admin (Tema Deep Forest #0e1b15 & Aksen Foresta Neon Lime #5edd5b) -->
    <aside class="w-64 bg-[#0e1b15] text-stone-300 flex-shrink-0 flex flex-col justify-between hidden md:flex border-r border-stone-800">
        <div>
            <!-- Header Brand -->
            <div class="h-16 flex items-center px-6 bg-[#09120e] border-b border-stone-800 gap-3">
                <div class="w-9 h-9 rounded-xl bg-[#5edd5b] flex items-center justify-center text-black font-extrabold text-sm shadow-sm">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M8 3l4 8 5-5 5 15H2L8 3z"/>
                    </svg>
                </div>
                <div>
                    <span class="font-extrabold text-sm text-white tracking-wide block leading-tight">BLNTRK OUTDOOR</span>
                    <span class="text-[10px] text-[#5edd5b] font-extrabold tracking-wider uppercase block">Panel Admin Toko</span>
                </div>
            </div>

            <!-- Nav Links with Crisp SVGs -->
            <nav class="p-4 space-y-1 text-xs font-semibold">
                <div class="text-[10px] font-bold uppercase tracking-wider text-stone-500 px-3 py-2">Utama</div>
                
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'bg-[#14251e] text-white font-extrabold border-l-4 border-[#5edd5b]' : 'hover:bg-[#14251e]/60 text-stone-300' }} transition">
                    <svg class="w-4 h-4 text-[#5edd5b]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Dashboard</span>
                </a>

                <div class="text-[10px] font-bold uppercase tracking-wider text-stone-500 px-3 pt-4 pb-2">Transaksi Rental</div>
                
                <a href="{{ url('/admin/pesanan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->is('admin/pesanan*') ? 'bg-[#14251e] text-white font-extrabold border-l-4 border-[#5edd5b]' : 'hover:bg-[#14251e]/60 text-stone-300' }} transition">
                    <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Daftar Pesanan</span>
                </a>

                <a href="{{ route('admin.denda.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->is('admin/denda*') || request()->is('admin/pesanan/*/pengembalian') ? 'bg-[#14251e] text-white font-extrabold border-l-4 border-[#5edd5b]' : 'hover:bg-[#14251e]/60 text-stone-300' }} transition">
                    <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Denda & Ganti Rugi</span>
                </a>

                <div class="text-[10px] font-bold uppercase tracking-wider text-stone-500 px-3 pt-4 pb-2">Katalog & Stok</div>

                <a href="{{ route('admin.alat.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->is('admin/alat*') ? 'bg-[#14251e] text-white font-extrabold border-l-4 border-[#5edd5b]' : 'hover:bg-[#14251e]/60 text-stone-300' }} transition">
                    <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <span>Data Alat Outdoor</span>
                </a>

                <a href="{{ route('admin.kategori.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->is('admin/kategori*') ? 'bg-[#14251e] text-white font-extrabold border-l-4 border-[#5edd5b]' : 'hover:bg-[#14251e]/60 text-stone-300' }} transition">
                    <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                    <span>Kategori Alat</span>
                </a>

                <a href="{{ url('/admin/perawatan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->is('admin/perawatan*') ? 'bg-[#14251e] text-white font-extrabold border-l-4 border-[#5edd5b]' : 'hover:bg-[#14251e]/60 text-stone-300' }} transition">
                    <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Perawatan Alat</span>
                </a>

                <div class="text-[10px] font-bold uppercase tracking-wider text-stone-500 px-3 pt-4 pb-2">Pelanggan & Pengguna</div>

                <a href="{{ route('admin.pelanggan.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->is('admin/pelanggan*') ? 'bg-[#14251e] text-white font-extrabold border-l-4 border-[#5edd5b]' : 'hover:bg-[#14251e]/60 text-stone-300' }} transition">
                    <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>Data Pelanggan</span>
                </a>

                <a href="{{ route('admin.user.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->is('admin/user*') ? 'bg-[#14251e] text-white font-extrabold border-l-4 border-[#5edd5b]' : 'hover:bg-[#14251e]/60 text-stone-300' }} transition">
                    <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    <span>Kelola Akun User</span>
                </a>

                <div class="text-[10px] font-bold uppercase tracking-wider text-stone-500 px-3 pt-4 pb-2">Laporan</div>

                <a href="{{ url('/admin/laporan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->is('admin/laporan*') ? 'bg-[#14251e] text-white font-extrabold border-l-4 border-[#5edd5b]' : 'hover:bg-[#14251e]/60 text-stone-300' }} transition">
                    <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Laporan & Cetak PDF</span>
                </a>
            </nav>
        </div>

        <!-- User info footer in sidebar -->
        <div class="p-4 border-t border-stone-800 bg-[#09120e]">
            <div class="text-[11px] text-stone-400">Login sebagai:</div>
            <div class="text-sm font-bold text-white truncate">{{ auth()->user()->nama }}</div>
            <div class="text-[11px] text-[#5edd5b] font-extrabold uppercase tracking-wider">Admin Toko</div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 bg-[#f4f5f6]">
        <!-- Top Navbar -->
        <header class="h-16 bg-white border-b border-stone-200 flex items-center justify-between px-6 sticky top-0 z-30 shadow-xs">
            <div class="flex items-center gap-3">
                <span class="text-sm font-extrabold text-[#14201a]">Panel Operasional Toko</span>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('beranda') }}" target="_blank" class="text-xs text-stone-600 hover:text-black font-bold flex items-center gap-1.5 transition">
                    <span>Lihat Web Utama</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-100 text-xs font-bold border border-rose-200 transition cursor-pointer">
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
