<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Autentikasi' }} - BLNTRK Outdoor</title>

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
<body class="bg-[#0e1b15] text-[#14201a] antialiased min-h-screen flex flex-col justify-between relative overflow-x-hidden selection:bg-[#f6c453] selection:text-[#0e1b15]">
    
    <!-- Ambient Glow Background Elements -->
    <div class="fixed inset-0 pointer-events-none opacity-40 -z-10" aria-hidden="true">
        <div class="absolute -top-40 -left-40 w-96 h-96 rounded-full bg-[#14251e] blur-3xl"></div>
        <div class="absolute top-1/3 -right-20 w-80 h-80 rounded-full bg-[#5edd5b]/10 blur-3xl"></div>
        <div class="absolute -bottom-20 left-1/3 w-96 h-96 rounded-full bg-[#14251e]/60 blur-3xl"></div>
    </div>

    <!-- Top Minimal Navbar -->
    <header class="w-full px-6 py-5 flex items-center justify-between max-w-6xl mx-auto">
        <a href="{{ route('beranda') }}" class="flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-xl bg-[#5edd5b] flex items-center justify-center text-black shadow-md group-hover:scale-105 transition">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M8 3l4 8 5-5 5 15H2L8 3z"/>
                </svg>
            </div>
            <div>
                <span class="text-lg font-extrabold tracking-tight text-white block leading-none">BLNTRK</span>
                <span class="text-[9px] tracking-widest uppercase font-extrabold text-[#5edd5b] block mt-1 leading-none">Outdoor Subang</span>
            </div>
        </a>

        <a href="{{ route('beranda') }}" class="text-xs text-stone-300 hover:text-[#5edd5b] flex items-center gap-2 transition font-bold">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Beranda</span>
        </a>
    </header>

    <!-- Main Auth Card Container -->
    <main class="flex-1 flex flex-col justify-center items-center px-4 py-8 sm:px-6">
        <div class="w-full sm:max-w-md bg-white p-7 sm:p-8 rounded-3xl shadow-2xl border border-stone-200/90 relative">
            {{ $slot }}
        </div>
    </main>

    <!-- Footer Copyright -->
    <footer class="w-full text-center py-5 text-xs text-stone-400">
        <p>&copy; {{ date('Y') }} BLNTRK Outdoor Jalancagak, Subang. Buka 24 Jam.</p>
    </footer>

</body>
</html>
