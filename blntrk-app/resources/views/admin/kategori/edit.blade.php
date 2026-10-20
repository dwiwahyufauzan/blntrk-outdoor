<x-layouts.admin>
    <x-slot:title>Edit Kategori - {{ $kategori->nama }}</x-slot:title>

    <div class="mb-6">
        <a href="{{ route('admin.kategori.index') }}" class="text-xs font-bold text-[#4e7a5a] hover:text-[#1e3a2c] transition flex items-center gap-1.5 mb-2">
            <span>&larr;</span>
            <span>Kembali ke Daftar Kategori</span>
        </a>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#14201a] tracking-tight bl-d">Edit Kategori Alat</h1>
        <p class="text-xs text-stone-500 mt-0.5">Perbarui informasi kelompok perlengkapan outdoor</p>
    </div>

    <div class="max-w-xl bg-white p-6 sm:p-7 rounded-2xl border border-stone-200/90 shadow-xs">
        <form method="POST" action="{{ route('admin.kategori.update', $kategori->id) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="nama" class="block text-xs font-bold text-[#14201a] uppercase tracking-wider mb-1.5">
                    Nama Kategori <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="nama" name="nama" value="{{ old('nama', $kategori->nama) }}" required autofocus
                    class="w-full text-xs rounded-xl border-stone-300 focus:border-[#1e3a2c] focus:ring-[#1e3a2c] shadow-xs text-[#14201a]">
                @error('nama')
                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="deskripsi" class="block text-xs font-bold text-[#14201a] uppercase tracking-wider mb-1.5">
                    Deskripsi Ringkas (Opsional)
                </label>
                <textarea id="deskripsi" name="deskripsi" rows="3"
                    class="w-full text-xs rounded-xl border-stone-300 focus:border-[#1e3a2c] focus:ring-[#1e3a2c] shadow-xs text-[#14201a]">{{ old('deskripsi', $kategori->deskripsi) }}</textarea>
                @error('deskripsi')
                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 border-t border-stone-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.kategori.index') }}" class="px-4 py-2.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#0e1b15] hover:bg-[#1e3a2c] text-[#f6c453] text-xs font-bold shadow-sm transition cursor-pointer">
                    Perbarui Kategori
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>

