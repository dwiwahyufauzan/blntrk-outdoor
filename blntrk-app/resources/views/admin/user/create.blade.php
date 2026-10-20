<x-layouts.admin>
    <x-slot:title>Tambah Pengguna Baru</x-slot:title>

    <div class="mb-6">
        <a href="{{ route('admin.user.index') }}" class="text-xs font-bold text-[#4e7a5a] hover:text-[#1e3a2c] transition flex items-center gap-1.5 mb-2">
            <span>&larr;</span>
            <span>Kembali ke Daftar Pengguna</span>
        </a>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#14201a] tracking-tight bl-d">Tambah Pengguna Baru</h1>
        <p class="text-xs text-stone-500 mt-0.5">Buat akun untuk staf Admin Toko atau Akun Pelanggan</p>
    </div>

    <div class="max-w-xl bg-white p-6 sm:p-8 rounded-2xl border border-stone-200/90 shadow-xs">
        <form method="POST" action="{{ route('admin.user.store') }}" class="space-y-4">
            @csrf

            <div>
                <label for="nama" class="block text-xs font-bold text-[#14201a] uppercase tracking-wider mb-1.5">
                    Nama Lengkap <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required autofocus
                    placeholder="Contoh: Rian Hidayat"
                    class="w-full text-xs rounded-xl border-stone-300 focus:border-[#1e3a2c] focus:ring-[#1e3a2c] shadow-xs text-[#14201a]">
                @error('nama')
                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-xs font-bold text-[#14201a] uppercase tracking-wider mb-1.5">
                    Alamat Email <span class="text-rose-500">*</span>
                </label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                    placeholder="nama@blntrk.test"
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
                    <option value="">-- Pilih Peran --</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->nama }}" {{ old('role') === $r->nama ? 'selected' : '' }}>
                            {{ $r->label }}
                        </option>
                    @endforeach
                </select>
                <p class="text-[11px] text-stone-400 mt-1.5 font-medium">*Admin hanya berwenang menambahkan peran Admin atau Pelanggan.</p>
                @error('role')
                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-[#14201a] uppercase tracking-wider mb-1.5">
                    Password Awal <span class="text-rose-500">*</span>
                </label>
                <input type="password" id="password" name="password" required minlength="8"
                    placeholder="Minimal 8 karakter"
                    class="w-full text-xs rounded-xl border-stone-300 focus:border-[#1e3a2c] focus:ring-[#1e3a2c] shadow-xs text-[#14201a]">
                @error('password')
                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Field Tambahan Jika Role Pelanggan -->
            <div id="pelanggan-fields" class="space-y-4 pt-3 border-t border-stone-100 {{ old('role') === 'pelanggan' ? '' : 'hidden' }}">
                <span class="text-[10px] font-bold uppercase text-[#4e7a5a] tracking-wider block">Data Profil Pelanggan Tambahan</span>
                <div>
                    <label for="no_hp" class="block text-xs font-bold text-[#14201a] uppercase tracking-wider mb-1.5">
                        Nomor HP / WhatsApp
                    </label>
                    <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp') }}"
                        placeholder="Contoh: 081234567890"
                        class="w-full text-xs font-mono rounded-xl border-stone-300 focus:border-[#1e3a2c] focus:ring-[#1e3a2c] shadow-xs text-[#14201a]">
                </div>

                <div>
                    <label for="alamat" class="block text-xs font-bold text-[#14201a] uppercase tracking-wider mb-1.5">
                        Alamat Domisili
                    </label>
                    <textarea id="alamat" name="alamat" rows="2"
                        placeholder="Alamat tempat tinggal pelanggan..."
                        class="w-full text-xs rounded-xl border-stone-300 focus:border-[#1e3a2c] focus:ring-[#1e3a2c] shadow-xs text-[#14201a]">{{ old('alamat') }}</textarea>
                </div>
            </div>

            <div class="flex items-center gap-2.5 pt-2">
                <input type="checkbox" id="is_aktif" name="is_aktif" value="1" {{ old('is_aktif', true) ? 'checked' : '' }}
                    class="rounded-lg border-stone-300 text-[#0e1b15] focus:ring-[#f6c453]">
                <label for="is_aktif" class="text-xs font-semibold text-stone-700 cursor-pointer">
                    Akun langsung berstatus aktif (bisa langsung login)
                </label>
            </div>

            <div class="pt-4 border-t border-stone-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.user.index') }}" class="px-4 py-2.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#0e1b15] hover:bg-[#1e3a2c] text-[#f6c453] text-xs font-bold shadow-sm transition cursor-pointer">
                    Simpan Pengguna
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

