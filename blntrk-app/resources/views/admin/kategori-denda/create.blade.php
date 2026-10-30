<x-layouts.admin>
    <x-slot:title>Tambah Kategori Denda - BLNTRK Outdoor</x-slot:title>

    <div class="max-w-2xl mx-auto pb-12">
        <div class="mb-6">
            <a href="{{ route('admin.kategori-denda.index') }}"
               class="inline-flex items-center gap-1.5 text-xs font-bold text-stone-500 hover:text-[#0e1b15] transition mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Kategori Denda</span>
            </a>
            <h1 class="text-2xl font-extrabold text-[#14201a] tracking-tight bl-d">Tambah Kategori Denda Baru</h1>
            <p class="text-xs text-stone-500 mt-0.5">Tentukan kode, klasifikasi, tarif acuan, dan deskripsi kategori denda</p>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                <strong class="block font-bold mb-1">Periksa kembali isian formulir:</strong>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.kategori-denda.store') }}" class="bg-white rounded-2xl border border-stone-200/90 shadow-xs p-6 sm:p-7 space-y-5">
            @csrf

            <!-- Kode & Nama -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="kode" class="block text-[11px] font-bold text-stone-600 uppercase tracking-wider mb-1">
                        Kode Kategori <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="kode" name="kode" value="{{ old('kode') }}" required placeholder="MISAL: HILANG"
                           class="w-full text-xs font-mono uppercase rounded-xl border-stone-200 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-2xs">
                    <span class="text-[10px] text-stone-400 mt-1 block">Huruf kapital & garis bawah</span>
                </div>

                <div class="sm:col-span-2">
                    <label for="nama" class="block text-[11px] font-bold text-stone-600 uppercase tracking-wider mb-1">
                        Nama Kategori Denda <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required placeholder="Contoh: Kehilangan Pasak Tenda"
                           class="w-full text-xs rounded-xl border-stone-200 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-2xs">
                </div>
            </div>

            <!-- Jenis & Satuan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="jenis" class="block text-[11px] font-bold text-stone-600 uppercase tracking-wider mb-1">
                        Klasifikasi Jenis Denda <span class="text-rose-500">*</span>
                    </label>
                    <select id="jenis" name="jenis" required
                            class="w-full text-xs rounded-xl border-stone-200 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-2xs">
                        <option value="kehilangan" {{ old('jenis') === 'kehilangan' ? 'selected' : '' }}>Kehilangan Barang / Komponen</option>
                        <option value="kerusakan" {{ old('jenis', 'kerusakan') === 'kerusakan' ? 'selected' : '' }}>Kerusakan Fisik Barang</option>
                        <option value="keterlambatan" {{ old('jenis') === 'keterlambatan' ? 'selected' : '' }}>Keterlambatan Pengembalian</option>
                        <option value="lainnya" {{ old('jenis') === 'lainnya' ? 'selected' : '' }}>Lainnya / Treatment Khusus</option>
                    </select>
                </div>

                <div>
                    <label for="satuan" class="block text-[11px] font-bold text-stone-600 uppercase tracking-wider mb-1">
                        Satuan Pengenaan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="satuan" name="satuan" value="{{ old('satuan', 'per unit') }}" required placeholder="per unit / per hari / per item"
                           class="w-full text-xs rounded-xl border-stone-200 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-2xs">
                </div>
            </div>

            <!-- Tarif Standar Acuan -->
            <div>
                <label for="tarif_standar" class="block text-[11px] font-bold text-stone-600 uppercase tracking-wider mb-1">
                    Tarif Acuan / Dasar (Rp) <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs font-bold text-stone-400">Rp</span>
                    <input type="number" id="tarif_standar" name="tarif_standar" min="0" step="1000" value="{{ old('tarif_standar', 0) }}" required
                           class="w-full pl-10 pr-4 py-2 text-xs font-mono font-bold rounded-xl border-stone-200 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-2xs">
                </div>
                <span class="text-[10px] text-stone-400 mt-1 block">Isi 0 jika tarif dihitung otomatis atau proporsional sewa harian.</span>
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="deskripsi" class="block text-[11px] font-bold text-stone-600 uppercase tracking-wider mb-1">
                    Deskripsi / Aturan Pengenaan (Opsional)
                </label>
                <textarea id="deskripsi" name="deskripsi" rows="3" placeholder="Jelaskan kriteria dan konsekuensi denda ini secara transparan..."
                          class="w-full text-xs rounded-xl border-stone-200 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-2xs">{{ old('deskripsi') }}</textarea>
            </div>

            <!-- Checkbox Aktif -->
            <div class="pt-2">
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="is_aktif" value="1" {{ old('is_aktif', true) ? 'checked' : '' }}
                           class="rounded text-[#0e1b15] focus:ring-[#5edd5b]">
                    <span class="text-xs font-bold text-stone-800">Status Aktif (dapat dipilih admin saat sesi cek pengembalian)</span>
                </label>
            </div>

            <!-- Tombol Aksi -->
            <div class="pt-4 border-t border-stone-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.kategori-denda.index') }}"
                   class="px-4 py-2.5 rounded-xl border border-stone-200 text-stone-600 text-xs font-bold hover:bg-stone-50 transition">
                    Batal
                </a>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-extrabold shadow-sm transition inline-flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Kategori</span>
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
