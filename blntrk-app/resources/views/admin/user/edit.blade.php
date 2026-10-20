<x-layouts.admin>
    <x-slot:title>Edit Pengguna - {{ $user->nama }}</x-slot:title>

    <div class="mb-6">
        <a href="{{ route('admin.user.index') }}" class="text-xs font-semibold text-stone-500 hover:text-emerald-700 transition flex items-center gap-1 mb-2">
            &larr; Kembali ke Daftar Pengguna
        </a>
        <h1 class="text-2xl font-black text-stone-900 tracking-tight">Edit Data Pengguna</h1>
        <p class="text-xs text-stone-500">Perbarui profil, hak akses peran, atau setel ulang kata sandi</p>
    </div>

    <div class="max-w-xl bg-white p-6 sm:p-8 rounded-xl border border-stone-200 shadow-sm">
        <form method="POST" action="{{ route('admin.user.update', $user->id) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="nama" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">
                    Nama Lengkap <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="nama" name="nama" value="{{ old('nama', $user->nama) }}" required autofocus
                    class="w-full text-xs rounded-lg border-stone-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm">
                @error('nama')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">
                    Alamat Email <span class="text-rose-500">*</span>
                </label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
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
                    @foreach($roles as $r)
                        <option value="{{ $r->nama }}" {{ old('role', $user->role?->nama) === $r->nama ? 'selected' : '' }}>
                            {{ $r->label }}
                        </option>
                    @endforeach
                </select>
                @error('role')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">
                    Ganti Password Baru (Kosongkan jika tidak diubah)
                </label>
                <input type="password" id="password" name="password" minlength="8"
                    placeholder="Minimal 8 karakter jika ingin diubah"
                    class="w-full text-xs rounded-lg border-stone-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm">
                @error('password')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Field Tambahan Jika Role Pelanggan -->
            <div id="pelanggan-fields" class="space-y-4 pt-2 border-t border-stone-100 {{ old('role', $user->role?->nama) === 'pelanggan' ? '' : 'hidden' }}">
                <span class="text-[10px] font-bold uppercase text-emerald-800 tracking-wider block">Data Pelanggan Terkait</span>
                <div>
                    <label for="no_hp" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">
                        Nomor HP / WhatsApp
                    </label>
                    <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp', $user->pelanggan?->no_hp) }}"
                        class="w-full text-xs font-mono rounded-lg border-stone-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm">
                </div>

                <div>
                    <label for="alamat" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">
                        Alamat Domisili
                    </label>
                    <textarea id="alamat" name="alamat" rows="2"
                        class="w-full text-xs rounded-lg border-stone-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm">{{ old('alamat', $user->pelanggan?->alamat) }}</textarea>
                </div>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" id="is_aktif" name="is_aktif" value="1" {{ old('is_aktif', $user->is_aktif) ? 'checked' : '' }}
                    class="rounded border-stone-300 text-emerald-600 focus:ring-emerald-600">
                <label for="is_aktif" class="text-xs font-semibold text-stone-700">
                    Status akun aktif
                </label>
            </div>

            <div class="pt-4 border-t border-stone-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.user.index') }}" class="px-4 py-2 rounded-lg bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-sm transition">
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
