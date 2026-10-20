<x-layouts.admin>
    <x-slot:title>Tambah Kategori Alat</x-slot:title>

    <div class="mb-6">
        <a href="{{ route('admin.kategori.index') }}" class="text-xs font-semibold text-stone-500 hover:text-emerald-700 transition flex items-center gap-1 mb-2">
            &larr; Kembali ke Daftar Kategori
        </a>
        <h1 class="text-2xl font-black text-stone-900 tracking-tight">Tambah Kategori Baru</h1>
        <p class="text-xs text-stone-500">Kelompokkan jenis perlengkapan outdoor untuk memudahkan pencarian pelanggan</p>
    </div>

    <div class="max-w-xl bg-white p-6 rounded-xl border border-stone-200 shadow-sm">
        <form method="POST" action="{{ route('admin.kategori.store') }}" class="space-y-4">
            @csrf

            <div>
                <label for="nama" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">
                    Nama Kategori <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required autofocus
                    placeholder="Contoh: Tenda Camping, Tas Carrier, dll."
                    class="w-full text-xs rounded-lg border-stone-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm">
                @error('nama')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="deskripsi" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">
                    Deskripsi Ringkas (Opsional)
                </label>
                <textarea id="deskripsi" name="deskripsi" rows="3"
                    placeholder="Penjelasan singkat mengenai kategori perlengkapan ini..."
                    class="w-full text-xs rounded-lg border-stone-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 border-t border-stone-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.kategori.index') }}" class="px-4 py-2 rounded-lg bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-sm transition">
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
