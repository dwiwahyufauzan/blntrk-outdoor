<x-layouts.admin>
    <x-slot:title>Tambah Pengguna Baru</x-slot:title>

    <div class="mb-6">
        <a href="{{ route('admin.user.index') }}" class="text-xs font-semibold text-stone-500 hover:text-emerald-700 transition flex items-center gap-1 mb-2">
            &larr; Kembali ke Daftar Pengguna
        </a>
        <h1 class="text-2xl font-black text-stone-900 tracking-tight">Tambah Pengguna Baru</h1>
        <p class="text-xs text-stone-500">Buat akun untuk staf Admin Toko atau Akun Pelanggan</p>
    </div>

    <div class="max-w-xl bg-white p-6 sm:p-8 rounded-xl border border-stone-200 shadow-sm">
        <form method="POST" action="{{ route('admin.user.store') }}" class="space-y-4">
            @csrf

            <div>
                <label for="nama" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">
                    Nama Lengkap <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required autofocus
                    placeholder="Contoh: Rian Hidayat"
                    class="w-full text-xs rounded-lg border-stone-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm">
                @error('nama')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">
                    Alamat Email <span class="text-rose-500">*</span>
                </label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                    placeholder="nama@blntrk.test"
                    class="w-full text-xs rounded-lg border-stone-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm">
                @error('email')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="role" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">
                    Peran Pengguna (Role) <span class="text-rose-500">*</span>
                </label>
                <select id="role" name="role" required onchange="togglePelangganFields(this.value)"
                    class="w-full text-xs rounded-lg border-stone-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm font-semibold">
                    <option value="">-- Pilih Peran --</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->nama }}" {{ old('role') === $r->nama ? 'selected' : '' }}>
                            {{ $r->label }}
                        </option>
                    @endforeach
                </select>
                <p class="text-[11px] text-stone-400 mt-1">*Admin hanya berwenang menambahkan peran Admin atau Pelanggan.</p>
                @error('role')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">
                    Password Awal <span class="text-rose-500">*</span>
                </label>
                <input type="password" id="password" name="password" required minlength="8"
                    placeholder="Minimal 8 karakter"
                    class="w-full text-xs rounded-lg border-stone-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm">
                @error('password')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Field Tambahan Jika Role Pelanggan -->
            <div id="pelanggan-fields" class="space-y-4 pt-2 border-t border-stone-100 {{ old('role') === 'pelanggan' ? '' : 'hidden' }}">
                <span class="text-[10px] font-bold uppercase text-emerald-800 tracking-wider block">Data Pelanggan Tambahan</span>
                <div>
                    <label for="no_hp" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">
                        Nomor HP / WhatsApp
                    </label>
                    <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp') }}"
                        placeholder="Contoh: 081234567890"
                        class="w-full text-xs font-mono rounded-lg border-stone-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm">
                </div>

                <div>
                    <label for="alamat" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">
                        Alamat Domisili
                    </label>
                    <textarea id="alamat" name="alamat" rows="2"
                        placeholder="Alamat tempat tinggal pelanggan..."
                        class="w-full text-xs rounded-lg border-stone-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm">{{ old('alamat') }}</textarea>
                </div>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" id="is_aktif" name="is_aktif" value="1" {{ old('is_aktif', true) ? 'checked' : '' }}
                    class="rounded border-stone-300 text-emerald-600 focus:ring-emerald-600">
                <label for="is_aktif" class="text-xs font-semibold text-stone-700">
                    Akun langsung berstatus aktif (bisa langsung login)
                </label>
            </div>

            <div class="pt-4 border-t border-stone-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.user.index') }}" class="px-4 py-2 rounded-lg bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-sm transition">
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
