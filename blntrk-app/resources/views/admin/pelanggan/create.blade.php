<x-layouts.admin>
    <x-slot:title>Tambah Pelanggan Walk-In</x-slot:title>

    <div class="mb-6">
        <a href="{{ route('admin.pelanggan.index') }}" class="text-xs font-semibold text-stone-500 hover:text-emerald-700 transition flex items-center gap-1 mb-2">
            &larr; Kembali ke Daftar Pelanggan
        </a>
        <h1 class="text-2xl font-black text-stone-900 tracking-tight">Tambah Pelanggan Walk-In</h1>
        <p class="text-xs text-stone-500">Form pencatatan data penyewa langsung di toko fisik tanpa akun aplikasi</p>
    </div>

    <div class="max-w-xl bg-white p-6 sm:p-8 rounded-xl border border-stone-200 shadow-sm">
        <form method="POST" action="{{ route('admin.pelanggan.store') }}" class="space-y-4">
            @csrf

            <div>
                <label for="nama_lengkap" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">
                    Nama Lengkap Pelanggan <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required autofocus
                    placeholder="Sesuai kartu identitas KTP/SIM"
                    class="w-full text-xs rounded-lg border-stone-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm">
                @error('nama_lengkap')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="no_hp" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">
                    Nomor WhatsApp / HP Aktif <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp') }}" required
                    placeholder="Contoh: 081234567890"
                    class="w-full text-xs font-mono rounded-lg border-stone-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm">
                @error('no_hp')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="alamat" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">
                    Alamat Domisili <span class="text-rose-500">*</span>
                </label>
                <textarea id="alamat" name="alamat" rows="3" required
                    placeholder="Alamat tempat tinggal atau kota asal penyewa..."
                    class="w-full text-xs rounded-lg border-stone-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm">{{ old('alamat') }}</textarea>
                @error('alamat')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 border-t border-stone-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.pelanggan.index') }}" class="px-4 py-2 rounded-lg bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-sm transition">
                    Simpan Pelanggan Walk-In
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
