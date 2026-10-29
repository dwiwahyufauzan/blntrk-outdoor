<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="font-black text-2xl text-[#14201a] tracking-tight bl-d">
                    {{ __('Pengaturan Profil & Akun') }}
                </h1>
                <p class="text-xs text-stone-500 mt-0.5">Kelola informasi kredensial, keamanan kata sandi, dan privasi akun Anda</p>
            </div>
            <a href="{{ url()->previous(route('beranda')) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-stone-200/90 text-xs font-bold text-stone-700 hover:bg-stone-50 shadow-2xs transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="p-6 sm:p-8 bg-white border border-stone-200/90 shadow-xs rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-6 sm:p-8 bg-white border border-stone-200/90 shadow-xs rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-6 sm:p-8 bg-white border border-rose-200/60 shadow-xs rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
