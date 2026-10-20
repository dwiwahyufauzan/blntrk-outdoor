<x-guest-layout>
    <x-slot:title>Masuk ke Akun</x-slot:title>

    <div class="mb-5 text-center">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-[#0e1b15] text-[#f6c453] text-xl font-black mb-3 shadow-xs bl-d">
            ⛺
        </div>
        <h1 class="text-2xl font-black text-[#14201a] tracking-tight bl-d">Masuk ke Akun</h1>
        <p class="text-xs text-stone-500 mt-1">Kelola penyewaan alat outdoor di BLNTRK Outdoor</p>
    </div>

    <!-- Kotak Akun Demo Cepat (1-Klik Isi) -->
    <div class="mb-5 p-3.5 bg-[#fbfbf9] rounded-2xl border border-stone-200/90 shadow-2xs">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-[#f6c453] animate-pulse"></span>
                <span class="text-[10px] font-black uppercase text-[#14201a] tracking-wider bl-d">Akun Demo (Klik untuk Isi Otomatis)</span>
            </div>
            <span class="text-[10px] font-mono text-stone-500 bg-stone-200/70 px-1.5 py-0.5 rounded">Pass: password</span>
        </div>

        <div class="grid grid-cols-3 gap-2">
            <!-- Tombol Demo Admin -->
            <button type="button" onclick="fillDemo('admin@blntrk.test', 'password', this)"
                class="demo-btn text-left p-2 rounded-xl border border-stone-200 bg-white hover:border-[#1e3a2c] hover:bg-[#1e3a2c]/5 transition text-xs group cursor-pointer">
                <span class="block text-[10px] font-bold text-amber-700 uppercase tracking-tight">Admin Toko</span>
                <span class="block text-[11px] font-semibold text-[#14201a] truncate group-hover:text-[#1e3a2c]">admin@blntrk.test</span>
            </button>

            <!-- Tombol Demo Pemilik -->
            <button type="button" onclick="fillDemo('pemilik@blntrk.test', 'password', this)"
                class="demo-btn text-left p-2 rounded-xl border border-stone-200 bg-white hover:border-[#1e3a2c] hover:bg-[#1e3a2c]/5 transition text-xs group cursor-pointer">
                <span class="block text-[10px] font-bold text-[#1e3a2c] uppercase tracking-tight">Pemilik</span>
                <span class="block text-[11px] font-semibold text-[#14201a] truncate group-hover:text-[#1e3a2c]">pemilik@blntrk.test</span>
            </button>

            <!-- Tombol Demo Pelanggan -->
            <button type="button" onclick="fillDemo('budi@blntrk.test', 'password', this)"
                class="demo-btn text-left p-2 rounded-xl border border-stone-200 bg-white hover:border-[#1e3a2c] hover:bg-[#1e3a2c]/5 transition text-xs group cursor-pointer">
                <span class="block text-[10px] font-bold text-stone-500 uppercase tracking-tight">Pelanggan</span>
                <span class="block text-[11px] font-semibold text-[#14201a] truncate group-hover:text-[#1e3a2c]">budi@blntrk.test</span>
            </button>
        </div>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4 text-[#1e3a2c] bg-emerald-50/80 p-3.5 rounded-xl border border-emerald-200 text-xs font-semibold" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Alamat Email" />
            <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@blntrk.test" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex justify-between items-center mb-1">
                <x-input-label for="password" value="Kata Sandi" class="!mb-0" />
                @if (Route::has('password.request'))
                    <a class="text-[11px] font-bold text-[#4e7a5a] hover:text-[#1e3a2c] transition" href="{{ route('password.request') }}">
                        Lupa kata sandi?
                    </a>
                @endif
            </div>

            <x-text-input id="password" class="block w-full"
                            type="password"
                            name="password"
                            placeholder="••••••••"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="w-4 h-4 rounded border-stone-300 text-[#0e1b15] shadow-xs focus:ring-[#f6c453]" name="remember" checked>
                <span class="ms-2 text-xs font-medium text-stone-600">Ingat sesi saya</span>
            </label>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit" class="w-full py-3 px-4 rounded-xl bg-[#0e1b15] hover:bg-[#1e3a2c] text-[#f6c453] font-bold text-xs uppercase tracking-wider transition shadow-xs flex items-center justify-center gap-2 cursor-pointer">
                <span>Masuk Sekarang</span>
                <span>&rarr;</span>
            </button>
        </div>

        <!-- Register Link -->
        <div class="text-center pt-3 border-t border-stone-200/80">
            <p class="text-xs text-stone-600">
                Belum memiliki akun?
                <a class="font-bold text-[#1e3a2c] hover:text-[#0e1b15] hover:underline transition" href="{{ route('register') }}">
                    Daftar akun baru
                </a>
            </p>
        </div>
    </form>

    <script>
        function fillDemo(email, password, btn) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;

            // Highlight tombol terpilih
            document.querySelectorAll('.demo-btn').forEach(b => {
                b.classList.remove('ring-2', 'ring-[#1e3a2c]', 'bg-emerald-50/60');
            });
            if (btn) {
                btn.classList.add('ring-2', 'ring-[#1e3a2c]', 'bg-emerald-50/60');
            }
        }
    </script>
</x-guest-layout>
