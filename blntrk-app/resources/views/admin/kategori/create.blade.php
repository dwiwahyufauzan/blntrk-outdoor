<x-layouts.admin>
    <x-slot:title>Tambah Kategori Alat</x-slot:title>

    <div class="mb-6">
        <a href="{{ route('admin.kategori.index') }}" class="text-xs font-bold text-stone-600 hover:text-black transition flex items-center gap-1.5 mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Daftar Kategori</span>
        </a>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#14201a] tracking-tight">Tambah Kategori Baru</h1>
        <p class="text-xs text-stone-500 mt-0.5">Kelompokkan jenis perlengkapan outdoor untuk memudahkan pencarian pelanggan</p>
    </div>

    <div class="max-w-xl bg-white p-6 sm:p-7 rounded-2xl border border-stone-200/90 shadow-xs">
        <form method="POST" action="{{ route('admin.kategori.store') }}" class="space-y-4">
            @csrf

            <div>
                <label for="nama" class="block text-xs font-bold text-[#14201a] uppercase tracking-wider mb-1.5">
                    Nama Kategori <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required autofocus
                    placeholder="Contoh: Tenda Camping, Tas Carrier, dll."
                    class="w-full text-xs rounded-xl border-stone-300 focus:border-[#5edd5b] focus:ring-[#5edd5b] shadow-xs text-[#14201a]">
                @error('nama')
                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="deskripsi" class="block text-xs font-bold text-[#14201a] uppercase tracking-wider mb-1.5">
                    Deskripsi Ringkas (Opsional)
                </label>
                <textarea id="deskripsi" name="deskripsi" rows="3"
                    placeholder="Penjelasan singkat mengenai kategori perlengkapan ini..."
                    class="w-full text-xs rounded-xl border-stone-300 focus:border-[#5edd5b] focus:ring-[#5edd5b] shadow-xs text-[#14201a]">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 border-t border-stone-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.kategori.index') }}" class="px-4 py-2.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-extrabold shadow-sm transition cursor-pointer">
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>

