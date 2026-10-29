<x-guest-layout>
    <x-slot:title>Lupa Kata Sandi</x-slot:title>

    <div class="mb-6 text-center">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-[#0e1b15] text-[#5edd5b] mb-3 shadow-md">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="7.5" cy="15.5" r="5.5"></circle>
                <path d="m21 2-9.6 9.6"></path>
                <path d="m15.5 7.5 3 3L22 7l-3-3"></path>
            </svg>
        </div>
        <h1 class="text-2xl font-extrabold text-[#14201a] tracking-tight">Lupa Kata Sandi</h1>
        <p class="text-xs text-stone-500 mt-1">Masukkan email terdaftar untuk menerima tautan atur ulang kata sandi.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4 text-[#14201a] bg-emerald-50/80 p-3.5 rounded-xl border border-emerald-200 text-xs font-semibold" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Alamat Email Terdaftar" />
            <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autofocus placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black font-extrabold text-xs uppercase tracking-wider transition shadow-md hover:shadow-lg flex items-center justify-center gap-2 cursor-pointer">
                <span>Kirim Link Reset Kata Sandi</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
        </div>

        <div class="text-center pt-3 border-t border-stone-200/80">
            <a class="text-xs font-bold text-[#0e1b15] hover:text-[#5edd5b] transition inline-flex items-center gap-1.5" href="{{ route('login') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke halaman Masuk</span>
            </a>
        </div>
    </form>
</x-guest-layout>

