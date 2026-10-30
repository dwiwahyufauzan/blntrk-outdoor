@php
    $user = auth()->user();
    $isAdmin = $user?->punyaRole('admin');
    $isPemilik = $user?->punyaRole('pemilik');
    $layoutComponent = $isAdmin ? 'layouts.admin' : ($isPemilik ? 'layouts.pemilik' : 'layouts.publik');
    $backRoute = $isAdmin ? route('admin.dashboard') : ($isPemilik ? route('pemilik.dashboard') : route('beranda'));
    $backLabel = $isAdmin ? 'Dashboard Admin' : ($isPemilik ? 'Dashboard Pemilik' : 'Beranda');
@endphp

<x-dynamic-component :component="$layoutComponent">
    <x-slot:title>Pengaturan Profil & Akun - {{ $isAdmin ? 'Panel Admin' : ($isPemilik ? 'Panel Pemilik' : 'BLNTRK Outdoor') }}</x-slot:title>

    <div class="{{ $isAdmin || $isPemilik ? 'max-w-4xl mx-auto space-y-6 pb-12' : 'max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-6' }}">
        
        <!-- Header Halaman Profil -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-stone-200/90">
            <div>
                <a href="{{ $backRoute }}" class="text-xs font-bold text-stone-500 hover:text-[#0e1b15] transition inline-flex items-center gap-1.5 mb-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali ke {{ $backLabel }}</span>
                </a>
                <h1 class="font-black text-2xl sm:text-3xl text-[#14201a] tracking-tight bl-d">
                    {{ __('Pengaturan Profil & Akun') }}
                </h1>
                <p class="text-xs text-stone-500 mt-0.5">Kelola informasi kredensial, keamanan kata sandi, dan privasi akun Anda</p>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('sesi.index') }}"
                   class="px-4 py-2 rounded-xl bg-white hover:bg-stone-50 text-stone-800 border border-stone-200 text-xs font-bold transition inline-flex items-center gap-2 shadow-2xs">
                    <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span>Sesi Login & Keamanan</span>
                </a>
            </div>
        </div>

        <!-- Kartu Ringkasan Akun -->
        <div class="p-6 bg-white border border-stone-200/90 shadow-xs rounded-2xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-[#0e1b15] text-[#5edd5b] flex items-center justify-center font-black text-xl shadow-xs shrink-0">
                    {{ strtoupper(substr($user->nama, 0, 1)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-base sm:text-lg font-black text-[#14201a] bl-d">{{ $user->nama }}</h2>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-[#5edd5b]/25 text-stone-900 border border-[#5edd5b]/40">
                            {{ $user->role?->label ?? 'User' }}
                        </span>
                    </div>
                    <span class="text-xs text-stone-500 font-medium block mt-0.5">{{ $user->email }}</span>
                    <span class="text-[11px] text-stone-400 block mt-0.5 font-mono">Terdaftar sejak {{ $user->created_at?->translatedFormat('d F Y') ?? '-' }}</span>
                </div>
            </div>
            <div class="text-xs text-stone-500">
                <span class="px-3 py-1.5 rounded-xl bg-stone-50 border border-stone-200 font-mono text-[11px] block">
                    ID Akun: #{{ $user->id }}
                </span>
            </div>
        </div>

        <!-- Form Update Informasi Profil -->
        <div id="informasi-profil" class="p-6 sm:p-8 bg-white border border-stone-200/90 shadow-xs rounded-2xl scroll-mt-24">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- Form Update Password -->
        <div id="pengaturan-profil" class="p-6 sm:p-8 bg-white border border-stone-200/90 shadow-xs rounded-2xl scroll-mt-24">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- Form Hapus Akun -->
        <div id="hapus-akun" class="p-6 sm:p-8 bg-white border border-rose-200/60 shadow-xs rounded-2xl scroll-mt-24">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-dynamic-component>
