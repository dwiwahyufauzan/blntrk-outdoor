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
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .bl-d { font-family: 'Bricolage Grotesque', sans-serif; letter-spacing: -0.02em; }
    </style>
</head>
<body class="bg-[#fbfbf9] text-[#14201a] antialiased flex min-h-screen">

    <!-- Sidebar Pemilik (Tema Deep Forest #0e1b15 & Aksen Lantern #f6c453) -->
    <aside class="w-64 bg-[#0e1b15] text-stone-300 flex-shrink-0 flex flex-col justify-between hidden md:flex border-r border-[#1e3a2c]">
        <div>
            <!-- Header Brand -->
            <div class="h-16 flex items-center px-6 bg-[#0a140f] border-b border-[#1e3a2c] gap-3">
                <div class="w-8 h-8 rounded-lg bg-[#f6c453] flex items-center justify-center text-[#0e1b15] font-extrabold text-sm bl-d shadow-sm">
                    B
                </div>
                <div>
                    <span class="font-extrabold text-sm text-white tracking-wide block leading-tight bl-d">BLNTRK OUTDOOR</span>
                    <span class="text-[10px] text-[#f6c453] font-bold tracking-wider uppercase block">Panel Pemilik Toko</span>
                </div>
            </div>

            <!-- Nav Links -->
            <nav class="p-4 space-y-1 text-xs font-semibold">
                <div class="text-[10px] font-bold uppercase tracking-wider text-stone-500 px-3 py-2">Monitoring</div>
                
                <a href="{{ route('pemilik.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('pemilik.dashboard') ? 'bg-[#1e3a2c] text-white font-bold border-l-4 border-[#f6c453]' : 'hover:bg-[#14201a] text-stone-300' }} transition">
                    <span class="text-base">📈</span>
                    <span>Dashboard Metrik</span>
                </a>

                <a href="{{ url('/pemilik/laporan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->is('pemilik/laporan*') ? 'bg-[#1e3a2c] text-white font-bold border-l-4 border-[#f6c453]' : 'hover:bg-[#14201a] text-stone-300' }} transition">
                    <span class="text-base">📑</span>
                    <span>Laporan Usaha & PDF</span>
                </a>

                <div class="text-[10px] font-bold uppercase tracking-wider text-stone-500 px-3 pt-4 pb-2">Kontrol & Kebijakan</div>

                <a href="{{ url('/pemilik/admin') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->is('pemilik/admin*') ? 'bg-[#1e3a2c] text-white font-bold border-l-4 border-[#f6c453]' : 'hover:bg-[#14201a] text-stone-300' }} transition">
                    <span class="text-base">👤</span>
                    <span>Kelola Akun Admin</span>
                </a>

                <a href="{{ url('/pemilik/pengaturan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->is('pemilik/pengaturan*') ? 'bg-[#1e3a2c] text-white font-bold border-l-4 border-[#f6c453]' : 'hover:bg-[#14201a] text-stone-300' }} transition">
                    <span class="text-base">⚙️</span>
                    <span>Pengaturan Toko & Denda</span>
                </a>

                <a href="{{ url('/pemilik/log') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->is('pemilik/log*') ? 'bg-[#1e3a2c] text-white font-bold border-l-4 border-[#f6c453]' : 'hover:bg-[#14201a] text-stone-300' }} transition">
                    <span class="text-base">📜</span>
                    <span>Log Aktivitas Sistem</span>
                </a>
            </nav>
        </div>

        <!-- User info footer in sidebar -->
        <div class="p-4 border-t border-[#1e3a2c] bg-[#0a140f]">
            <div class="text-[11px] text-stone-400">Login sebagai:</div>
            <div class="text-sm font-bold text-white truncate">{{ auth()->user()->nama }}</div>
            <div class="text-[11px] text-[#f6c453] font-bold">Pemilik Toko (Owner)</div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Top Navbar -->
        <header class="h-16 bg-white border-b border-stone-200 flex items-center justify-between px-6 sticky top-0 z-30 shadow-xs">
            <div class="flex items-center gap-3">
                <span class="text-sm font-bold text-[#14201a] bl-d">Ringkasan Eksekutif & Pemantauan Usaha</span>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('beranda') }}" target="_blank" class="text-xs text-stone-600 hover:text-[#1e3a2c] font-semibold flex items-center gap-1 transition">
                    <span>Lihat Halaman Utama ↗</span>
                </a>

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-100 text-xs font-bold border border-rose-200 transition">
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
