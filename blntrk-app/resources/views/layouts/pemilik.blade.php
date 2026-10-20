<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Panel Pemilik' }} - BLNTRK Outdoor</title>

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
<body class="bg-slate-100 text-slate-800 antialiased flex min-h-screen">

    <!-- Sidebar Pemilik -->
    <aside class="w-64 bg-slate-900 text-slate-300 flex-shrink-0 flex flex-col justify-between hidden md:flex border-r border-slate-800">
        <div>
            <!-- Header Brand -->
            <div class="h-16 flex items-center px-6 bg-slate-950 border-b border-slate-800 gap-3">
                <div class="w-8 h-8 rounded bg-blue-600 flex items-center justify-center text-white font-black text-sm">B</div>
                <div>
                    <span class="font-bold text-sm text-white tracking-wide block leading-tight">BLNTRK OUTDOOR</span>
                    <span class="text-[10px] text-blue-400 font-semibold tracking-wider uppercase block">Panel Pemilik Toko</span>
                </div>
            </div>

            <!-- Nav Links -->
            <nav class="p-4 space-y-1 text-sm font-medium">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-3 py-2">Monitoring</div>
                
                <a href="{{ route('pemilik.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('pemilik.dashboard') ? 'bg-blue-700 text-white font-semibold' : 'hover:bg-slate-800 text-slate-300' }} transition">
                    <span>📈</span>
                    <span>Dashboard Metrik</span>
                </a>

                <a href="{{ url('/pemilik/laporan') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->is('pemilik/laporan*') ? 'bg-blue-700 text-white font-semibold' : 'hover:bg-slate-800 text-slate-300' }} transition">
                    <span>📑</span>
                    <span>Laporan Usaha & PDF</span>
                </a>

                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-3 pt-4 pb-2">Kontrol & Kebijakan</div>

                <a href="{{ url('/pemilik/admin') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->is('pemilik/admin*') ? 'bg-blue-700 text-white font-semibold' : 'hover:bg-slate-800 text-slate-300' }} transition">
                    <span>👤</span>
                    <span>Kelola Akun Admin</span>
                </a>

                <a href="{{ url('/pemilik/pengaturan') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->is('pemilik/pengaturan*') ? 'bg-blue-700 text-white font-semibold' : 'hover:bg-slate-800 text-slate-300' }} transition">
                    <span>⚙️</span>
                    <span>Pengaturan Toko & Denda</span>
                </a>

                <a href="{{ url('/pemilik/log') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->is('pemilik/log*') ? 'bg-blue-700 text-white font-semibold' : 'hover:bg-slate-800 text-slate-300' }} transition">
                    <span>📜</span>
                    <span>Log Aktivitas Sistem</span>
                </a>
            </nav>
        </div>

        <!-- User info footer in sidebar -->
        <div class="p-4 border-t border-slate-800 bg-slate-950/60">
            <div class="text-xs text-slate-400">Login sebagai:</div>
            <div class="text-sm font-bold text-white truncate">{{ auth()->user()->nama }}</div>
            <div class="text-[11px] text-blue-400 font-medium">Pemilik Toko (Owner)</div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Top Navbar -->
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 sticky top-0 z-30 shadow-sm">
            <div class="flex items-center gap-3">
                <span class="text-sm font-semibold text-slate-700">Ringkasan Eksekutif & Pemantauan Usaha</span>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('beranda') }}" target="_blank" class="text-xs text-slate-600 hover:text-blue-700 font-medium flex items-center gap-1">
                    <span>Lihat Halaman Utama ↗</span>
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
