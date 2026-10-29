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
<body class="bg-[#fbfbf9] text-[#14201a] antialiased flex min-h-screen">

    <!-- Sidebar Pemilik (Tema Deep Forest #0e1b15 & Aksen Foresta Neon Lime #5edd5b) -->
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
                    <span class="text-[10px] text-[#5edd5b] font-extrabold tracking-wider uppercase block">Panel Pemilik Toko</span>
                </div>
            </div>

            <!-- Nav Links with Crisp SVGs -->
            <nav class="p-4 space-y-1 text-xs font-semibold">
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
        </div>

        <!-- User info footer in sidebar -->
        <div class="p-4 border-t border-stone-800 bg-[#09120e]">
            <div class="text-[11px] text-stone-400">Login sebagai:</div>
            <div class="text-sm font-bold text-white truncate">{{ auth()->user()->nama }}</div>
            <div class="text-[11px] text-[#5edd5b] font-extrabold uppercase tracking-wider">Pemilik Toko (Owner)</div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 bg-[#f4f5f6]">
        <!-- Top Navbar -->
        <header class="h-16 bg-white border-b border-stone-200 flex items-center justify-between px-6 sticky top-0 z-30 shadow-xs">
            <div class="flex items-center gap-3">
                <span class="text-sm font-extrabold text-[#14201a]">Ringkasan Eksekutif & Pemantauan Usaha</span>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('beranda') }}" target="_blank" class="text-xs text-stone-600 hover:text-black font-bold flex items-center gap-1.5 transition">
                    <span>Lihat Halaman Utama</span>
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
