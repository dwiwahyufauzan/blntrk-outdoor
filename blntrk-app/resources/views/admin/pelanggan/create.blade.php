<x-layouts.admin>
    <x-slot:title>Tambah Pelanggan Walk-In</x-slot:title>

    <div class="mb-6">
        <a href="{{ route('admin.pelanggan.index') }}" class="text-xs font-bold text-[#4e7a5a] hover:text-[#1e3a2c] transition flex items-center gap-1.5 mb-2">
            <span>&larr;</span>
            <span>Kembali ke Daftar Pelanggan</span>
        </a>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#14201a] tracking-tight bl-d">Tambah Pelanggan Walk-In</h1>
        <p class="text-xs text-stone-500 mt-0.5">Form pencatatan data peminjam langsung di toko fisik tanpa perlu akun aplikasi</p>
    </div>

    <div class="max-w-xl bg-white p-6 sm:p-8 rounded-2xl border border-stone-200/90 shadow-xs">
        <form method="POST" action="{{ route('admin.pelanggan.store') }}" class="space-y-4">
            @csrf

            <div>
                <label for="nama_lengkap" class="block text-xs font-bold text-[#14201a] uppercase tracking-wider mb-1.5">
                    Nama Lengkap Pelanggan <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required autofocus
                    placeholder="Sesuai kartu identitas KTP/SIM asli"
                    class="w-full text-xs rounded-xl border-stone-300 focus:border-[#1e3a2c] focus:ring-[#1e3a2c] shadow-xs text-[#14201a]">
                @error('nama_lengkap')
                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="no_hp" class="block text-xs font-bold text-[#14201a] uppercase tracking-wider mb-1.5">
                    Nomor WhatsApp / HP Aktif <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp') }}" required
                    placeholder="Contoh: 081234567890"
                    class="w-full text-xs font-mono rounded-xl border-stone-300 focus:border-[#1e3a2c] focus:ring-[#1e3a2c] shadow-xs text-[#14201a]">
                @error('no_hp')
                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="alamat" class="block text-xs font-bold text-[#14201a] uppercase tracking-wider mb-1.5">
                    Alamat Domisili <span class="text-rose-500">*</span>
                </label>
                <textarea id="alamat" name="alamat" rows="3" required
                    placeholder="Alamat tempat tinggal atau kota asal penyewa..."
                    class="w-full text-xs rounded-xl border-stone-300 focus:border-[#1e3a2c] focus:ring-[#1e3a2c] shadow-xs text-[#14201a]">{{ old('alamat') }}</textarea>
                @error('alamat')
                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 border-t border-stone-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.pelanggan.index') }}" class="px-4 py-2.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#0e1b15] hover:bg-[#1e3a2c] text-[#f6c453] text-xs font-bold shadow-sm transition cursor-pointer">
                    Simpan Pelanggan Walk-In
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>

