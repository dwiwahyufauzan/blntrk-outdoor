<x-guest-layout>
    <x-slot:title>Daftar Akun Pelanggan</x-slot:title>

    <div class="mb-6 text-center">
        <img src="{{ asset('images/logo.png') }}" alt="BLNTRK Outdoor Logo" class="inline-block w-16 h-16 rounded-full object-cover shadow-md mb-3 ring-2 ring-[#0e1b15]/10">
        <h1 class="text-2xl font-extrabold text-[#14201a] tracking-tight">Daftar Akun Baru</h1>
        <p class="text-xs text-stone-500 mt-1">Sewa alat outdoor lebih cepat dengan akun terdaftar</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Nama Lengkap -->
        <div>
            <x-input-label for="nama" value="Nama Lengkap (Sesuai KTP/SIM)" />
            <x-text-input id="nama" class="block w-full" type="text" name="nama" :value="old('nama')" required autofocus autocomplete="name" placeholder="Contoh: Muhammad Rafly" />
            <x-input-error :messages="$errors->get('nama')" class="mt-1.5" />
        </div>

        <!-- Alamat Email -->
        <div>
            <x-input-label for="email" value="Alamat Email Aktif" />
            <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <!-- Nomor WhatsApp / HP -->
        <div>
            <x-input-label for="no_hp" value="Nomor WhatsApp / HP Aktif" />
            <x-text-input id="no_hp" class="block w-full" type="text" name="no_hp" :value="old('no_hp')" required placeholder="081234567890" />
            <x-input-error :messages="$errors->get('no_hp')" class="mt-1.5" />
        </div>

        <!-- Alamat Domisili -->
        <div>
            <x-input-label for="alamat" value="Alamat Tempat Tinggal" />
            <textarea id="alamat" name="alamat" rows="2" class="block w-full border-stone-300 focus:border-[#5edd5b] focus:ring-[#5edd5b] rounded-xl shadow-xs text-sm text-[#14201a] placeholder:text-stone-400" placeholder="Contoh: Jl. Raya Jalancagak RT 01/02, Subang">{{ old('alamat') }}</textarea>
            <x-input-error :messages="$errors->get('alamat')" class="mt-1.5" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" value="Kata Sandi (Minimal 8 Karakter)" />
            <x-text-input id="password" class="block w-full"
                            type="password"
                            name="password"
                            placeholder="••••••••"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" value="Ulangi Kata Sandi" />
            <x-text-input id="password_confirmation" class="block w-full"
                            type="password"
                            name="password_confirmation"
                            placeholder="••••••••"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
        </div>

        <!-- Submit Button (Foresta Neon Lime) -->
        <div class="pt-2">
            <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black font-extrabold text-xs uppercase tracking-wider transition shadow-md hover:shadow-lg flex items-center justify-center gap-2 cursor-pointer">
                <span>Daftar Akun Sekarang</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
        </div>

        <!-- Login Link -->
        <div class="text-center pt-3 border-t border-stone-200/80">
            <p class="text-xs text-stone-600">
                Sudah memiliki akun?
                <a class="font-bold text-[#0e1b15] hover:text-[#5edd5b] hover:underline transition" href="{{ route('login') }}">
                    Masuk ke akun
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>

