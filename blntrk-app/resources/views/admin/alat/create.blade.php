<x-layouts.admin>
    <x-slot:title>Tambah Alat Outdoor Baru</x-slot:title>

    <div class="mb-6">
        <a href="{{ route('admin.alat.index') }}" class="text-xs font-bold text-[#4e7a5a] hover:text-[#1e3a2c] transition flex items-center gap-1.5 mb-2">
            <span>&larr;</span>
            <span>Kembali ke Daftar Alat</span>
        </a>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#14201a] tracking-tight bl-d">Tambah Alat Outdoor</h1>
        <p class="text-xs text-stone-500 mt-0.5">Daftarkan inventaris perlengkapan sewa baru ke dalam sistem BLNTRK Outdoor</p>
    </div>

    <div class="max-w-3xl bg-white p-6 sm:p-8 rounded-2xl border border-stone-200/90 shadow-xs">
        <form method="POST" action="{{ route('admin.alat.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Kode Alat -->
                <div>
                    <label for="kode_alat" class="block text-xs font-bold text-[#14201a] uppercase tracking-wider mb-1.5">
                        Kode Alat <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="kode_alat" name="kode_alat" value="{{ old('kode_alat') }}" required autofocus
                        placeholder="Contoh: TND-005, CAR-010"
                        class="w-full text-xs font-mono uppercase rounded-xl border-stone-300 focus:border-[#1e3a2c] focus:ring-[#1e3a2c] shadow-xs text-[#14201a]">
                    @error('kode_alat')
                        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kategori -->
                <div>
                    <label for="kategori_id" class="block text-xs font-bold text-[#14201a] uppercase tracking-wider mb-1.5">
                        Kategori <span class="text-rose-500">*</span>
                    </label>
                    <select id="kategori_id" name="kategori_id" required
                        class="w-full text-xs rounded-xl border-stone-300 focus:border-[#1e3a2c] focus:ring-[#1e3a2c] shadow-xs text-[#14201a]">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategoriList as $k)
                            <option value="{{ $k->id }}" {{ old('kategori_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('kategori_id')
                        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Nama Alat -->
            <div>
                <label for="nama" class="block text-xs font-bold text-[#14201a] uppercase tracking-wider mb-1.5">
                    Nama Alat Outdoor <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required
                    placeholder="Contoh: Tenda Dome Kapasitas 4 Orang Waterproof"
                    class="w-full text-xs rounded-xl border-stone-300 focus:border-[#1e3a2c] focus:ring-[#1e3a2c] shadow-xs text-[#14201a]">
                @error('nama')
                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Tarif Sewa -->
                <div>
                    <label for="harga_per_hari" class="block text-xs font-bold text-[#14201a] uppercase tracking-wider mb-1.5">
                        Tarif Sewa/Hari (Rp) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" id="harga_per_hari" name="harga_per_hari" value="{{ old('harga_per_hari', 0) }}" min="0" required
                        class="w-full text-xs rounded-xl border-stone-300 focus:border-[#1e3a2c] focus:ring-[#1e3a2c] shadow-xs font-bold text-[#14201a]">
                    @error('harga_per_hari')
                        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Total Stok Fisik -->
                <div>
                    <label for="stok_total" class="block text-xs font-bold text-[#14201a] uppercase tracking-wider mb-1.5">
                        Total Stok Fisik <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" id="stok_total" name="stok_total" value="{{ old('stok_total', 1) }}" min="0" required
                        class="w-full text-xs rounded-xl border-stone-300 focus:border-[#1e3a2c] focus:ring-[#1e3a2c] shadow-xs font-bold text-[#14201a]">
                    @error('stok_total')
                        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Stok Perawatan -->
                <div>
                    <label for="stok_perawatan" class="block text-xs font-bold text-[#14201a] uppercase tracking-wider mb-1.5">
                        Stok Perawatan/Rusak <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" id="stok_perawatan" name="stok_perawatan" value="{{ old('stok_perawatan', 0) }}" min="0" required
                        class="w-full text-xs rounded-xl border-stone-300 focus:border-[#1e3a2c] focus:ring-[#1e3a2c] shadow-xs font-bold text-[#14201a]">
                    @error('stok_perawatan')
                        <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Upload Foto Alat & Pratinjau -->
            <div>
                <label class="block text-xs font-bold text-[#14201a] uppercase tracking-wider mb-1.5">
                    Foto Alat (JPG, PNG, WEBP, Maks 2MB)
                </label>
                <div class="flex items-start gap-4">
                    <div class="w-24 h-24 rounded-2xl bg-stone-100 border border-stone-200 overflow-hidden flex items-center justify-center shrink-0">
                        <img id="preview-img" src="" alt="Pratinjau Foto" class="w-full h-full object-cover hidden">
                        <span id="preview-placeholder" class="text-3xl text-stone-300">⛺</span>
                    </div>
                    <div class="flex-1">
                        <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp"
                            onchange="previewFoto(this)"
                            class="block w-full text-xs text-stone-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#0e1b15] file:text-[#f6c453] hover:file:bg-[#1e3a2c] cursor-pointer">
                        <p class="text-[11px] text-stone-400 mt-1.5">Disarankan foto dengan rasio 1:1 atau 4:3 latar bersih agar katalog terlihat profesional.</p>
                        @error('foto')
                            <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="deskripsi" class="block text-xs font-bold text-[#14201a] uppercase tracking-wider mb-1.5">
                    Deskripsi & Spesifikasi Alat (Opsional)
                </label>
                <textarea id="deskripsi" name="deskripsi" rows="3"
                    placeholder="Tuliskan spesifikasi bahan, ukuran, kapasitas, atau kelengkapan alat outdoor..."
                    class="w-full text-xs rounded-xl border-stone-300 focus:border-[#1e3a2c] focus:ring-[#1e3a2c] shadow-xs text-[#14201a]">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status Aktif -->
            <div class="flex items-center gap-2.5 p-3 rounded-xl bg-stone-50 border border-stone-200/80">
                <input type="checkbox" id="is_aktif" name="is_aktif" value="1" {{ old('is_aktif', true) ? 'checked' : '' }}
                    class="rounded-lg border-stone-300 text-[#0e1b15] focus:ring-[#f6c453]">
                <label for="is_aktif" class="text-xs font-semibold text-stone-700 cursor-pointer">
                    Aktifkan alat ini agar dapat langsung dilihat dan disewa oleh pelanggan di katalog publik
                </label>
            </div>

            <div class="pt-4 border-t border-stone-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.alat.index') }}" class="px-4 py-2.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#0e1b15] hover:bg-[#1e3a2c] text-[#f6c453] text-xs font-bold shadow-sm transition cursor-pointer">
                    Simpan Alat Outdoor
                </button>
            </div>
        </form>
    </div>

    <script>
        function previewFoto(input) {
            const preview = document.getElementById('preview-img');
            const placeholder = document.getElementById('preview-placeholder');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-layouts.admin>

