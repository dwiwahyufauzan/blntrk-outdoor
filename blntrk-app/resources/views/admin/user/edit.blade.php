<x-layouts.admin>
    <x-slot:title>Edit Pengguna - {{ $user->nama }}</x-slot:title>

    <div class="mb-6">
        <a href="{{ route('admin.user.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-stone-500 hover:text-[#0e1b15] transition mb-3">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar Pengguna
        </a>
        <h1 class="text-2xl font-black text-[#14201a] tracking-tight bl-d">Edit Data Pengguna</h1>
        <p class="text-xs text-stone-500 mt-0.5">Perbarui profil pengguna, hak akses peran, atau setel kata sandi baru</p>
    </div>

    <div class="max-w-xl bg-white p-6 sm:p-8 rounded-2xl border border-stone-200/90 shadow-xs">
        <form method="POST" action="{{ route('admin.user.update', $user->id) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="nama" class="block text-xs font-bold text-[#14201a] uppercase tracking-wider mb-1.5">
                    Nama Lengkap <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="nama" name="nama" value="{{ old('nama', $user->nama) }}" required autofocus
                    class="w-full text-xs rounded-xl border-stone-300 focus:border-[#1e3a2c] focus:ring-[#1e3a2c] shadow-xs text-[#14201a]">
                @error('nama')
                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-xs font-bold text-[#14201a] uppercase tracking-wider mb-1.5">
                    Alamat Email <span class="text-rose-500">*</span>
                </label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                    class="w-full text-xs rounded-xl border-stone-300 focus:border-[#1e3a2c] focus:ring-[#1e3a2c] shadow-xs text-[#14201a]">
                @error('email')
                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="role" class="block text-xs font-bold text-[#14201a] uppercase tracking-wider mb-1.5">
                    Peran Pengguna (Role) <span class="text-rose-500">*</span>
                </label>
                <select id="role" name="role" required onchange="togglePelangganFields(this.value)"
                    class="w-full text-xs rounded-xl border-stone-300 focus:border-[#1e3a2c] focus:ring-[#1e3a2c] shadow-xs font-semibold text-[#14201a]">
                    @foreach($roles as $r)
                        <option value="{{ $r->nama }}" {{ old('role', $user->role?->nama) === $r->nama ? 'selected' : '' }}>
                            {{ $r->label }}
                        </option>
                    @endforeach
                </select>
                @error('role')
                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-[#14201a] uppercase tracking-wider mb-1.5">
                    Ganti Password Baru (Kosongkan jika tidak diubah)
                </label>
                <input type="password" id="password" name="password" minlength="8"
                    placeholder="Minimal 8 karakter jika ingin diubah"
                    class="w-full text-xs rounded-xl border-stone-300 focus:border-[#1e3a2c] focus:ring-[#1e3a2c] shadow-xs text-[#14201a]">
                @error('password')
                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Field Tambahan Jika Role Pelanggan -->
            <div id="pelanggan-fields" class="space-y-4 pt-3 border-t border-stone-100 {{ old('role', $user->role?->nama) === 'pelanggan' ? '' : 'hidden' }}">
                <div class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#f6c453]"></span>
                    <span class="text-[11px] font-bold uppercase text-[#1e3a2c] tracking-wider">Data Pelanggan Terkait</span>
                </div>
                <div>
                    <label for="no_hp" class="block text-xs font-bold text-[#14201a] uppercase tracking-wider mb-1.5">
                        Nomor HP / WhatsApp
                    </label>
                    <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp', $user->pelanggan?->no_hp) }}"
                        class="w-full text-xs font-mono rounded-xl border-stone-300 focus:border-[#1e3a2c] focus:ring-[#1e3a2c] shadow-xs text-[#14201a]">
                </div>

                <div>
                    <label for="alamat" class="block text-xs font-bold text-[#14201a] uppercase tracking-wider mb-1.5">
                        Alamat Domisili
                    </label>
                    <textarea id="alamat" name="alamat" rows="2"
                        class="w-full text-xs rounded-xl border-stone-300 focus:border-[#1e3a2c] focus:ring-[#1e3a2c] shadow-xs text-[#14201a]">{{ old('alamat', $user->pelanggan?->alamat) }}</textarea>
                </div>
            </div>

            <div class="flex items-center gap-2.5 pt-2">
                <input type="checkbox" id="is_aktif" name="is_aktif" value="1" {{ old('is_aktif', $user->is_aktif) ? 'checked' : '' }}
                    class="w-4 h-4 rounded border-stone-300 text-[#1e3a2c] focus:ring-[#1e3a2c]">
                <label for="is_aktif" class="text-xs font-bold text-[#14201a] cursor-pointer">
                    Status akun aktif (dapat login ke sistem)
                </label>
            </div>

            <div class="pt-4 border-t border-stone-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.user.index') }}" class="px-4 py-2.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#0e1b15] hover:bg-[#1e3a2c] text-[#f6c453] text-xs font-bold shadow-xs transition inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Perbarui Pengguna
                </button>
            </div>
        </form>
    </div>

    <script>
        function togglePelangganFields(role) {
            const container = document.getElementById('pelanggan-fields');
            if (role === 'pelanggan') {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        }
    </script>
</x-layouts.admin>
