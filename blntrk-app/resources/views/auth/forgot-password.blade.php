<x-guest-layout>
    <x-slot:title>Lupa Kata Sandi</x-slot:title>

    <div class="mb-6 text-center">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-[#0e1b15] text-[#f6c453] text-xl font-black mb-3 shadow-sm bl-d">
            🔑
        </div>
        <h1 class="text-2xl font-extrabold text-[#14201a] tracking-tight bl-d">Lupa Kata Sandi</h1>
        <p class="text-xs text-stone-500 mt-1">Masukkan email terdaftar untuk menerima tautan atur ulang kata sandi.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4 text-[#1e3a2c] bg-emerald-50/80 p-3.5 rounded-xl border border-emerald-200 text-xs font-semibold" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Alamat Email Terdaftar" />
            <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autofocus placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full py-3 px-4 rounded-xl bg-[#0e1b15] hover:bg-[#1e3a2c] text-[#f6c453] font-bold text-xs uppercase tracking-wider transition shadow-md flex items-center justify-center gap-2 cursor-pointer">
                <span>Kirim Link Reset Kata Sandi</span>
                <span>&rarr;</span>
            </button>
        </div>

        <div class="text-center pt-3 border-t border-stone-200/80">
            <a class="text-xs font-bold text-[#1e3a2c] hover:underline" href="{{ route('login') }}">
                &larr; Kembali ke halaman Masuk
            </a>
        </div>
    </form>
</x-guest-layout>

