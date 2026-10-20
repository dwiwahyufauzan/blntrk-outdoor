<x-layouts.admin>
    <x-slot:title>Detail Alat - {{ $alat->nama }}</x-slot:title>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('admin.alat.index') }}" class="text-xs font-semibold text-stone-500 hover:text-emerald-700 transition flex items-center gap-1 mb-2">
                &larr; Kembali ke Daftar Alat
            </a>
            <h1 class="text-2xl font-black text-stone-900 tracking-tight">{{ $alat->nama }}</h1>
            <p class="text-xs text-stone-500">Kode Alat: <span class="font-mono font-bold text-emerald-800">{{ $alat->kode_alat }}</span> • Kategori: {{ $alat->kategori?->nama ?? 'Umum' }}</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.alat.edit', $alat->id) }}" class="px-4 py-2 rounded-lg bg-stone-900 hover:bg-stone-800 text-white text-xs font-bold transition shadow-sm">
                Edit Alat ✏️
            </a>
        </div>
    </div>

    <!-- Ringkasan Profil Alat -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Foto Alat -->
        <div class="bg-white p-4 rounded-xl border border-stone-200 shadow-sm flex flex-col items-center justify-center">
            <div class="w-full h-64 rounded-xl bg-stone-100 overflow-hidden flex items-center justify-center border border-stone-200">
                @if($alat->foto)
                    <img src="{{ asset('storage/' . $alat->foto) }}" alt="{{ $alat->nama }}" class="w-full h-full object-cover">
                @else
                    <div class="text-center p-6 text-stone-300">
                        <span class="text-6xl block mb-2">⛺</span>
                        <span class="text-xs text-stone-400">Belum ada foto yang diunggah</span>
                    </div>
                @endif
            </div>

            <div class="mt-4 w-full flex items-center justify-between text-xs">
                <span class="text-stone-500">Status Visibilitas:</span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $alat->is_aktif ? 'bg-emerald-100 text-emerald-800' : 'bg-stone-100 text-stone-600' }}">
                    {{ $alat->is_aktif ? 'Aktif di Katalog' : 'Nonaktif' }}
                </span>
            </div>
        </div>

        <!-- Spesifikasi & Stok -->
        <div class="md:col-span-2 bg-white p-6 rounded-xl border border-stone-200 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-stone-500 mb-4 pb-2 border-b border-stone-100">
                    Spesifikasi & Informasi Tarif
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                    <div class="p-3 rounded-lg bg-emerald-50 border border-emerald-100">
                        <span class="text-[10px] uppercase font-bold text-emerald-700 block">Tarif Sewa Harian</span>
                        <span class="text-lg font-black text-emerald-950">
                            Rp {{ number_format($alat->harga_per_hari, 0, ',', '.') }}
                        </span>
                        <span class="text-[10px] text-emerald-700 block">per 24 jam</span>
                    </div>

                    <div class="p-3 rounded-lg bg-stone-50 border border-stone-200">
                        <span class="text-[10px] uppercase font-bold text-stone-600 block">Total Stok Fisik</span>
                        <span class="text-lg font-black text-stone-900">
                            {{ $alat->stok_total }}
                        </span>
                        <span class="text-[10px] text-stone-500 block">unit terdaftar di toko</span>
                    </div>

                    <div class="p-3 rounded-lg {{ $alat->stok_perawatan > 0 ? 'bg-amber-50 border border-amber-200' : 'bg-stone-50 border border-stone-200' }}">
                        <span class="text-[10px] uppercase font-bold {{ $alat->stok_perawatan > 0 ? 'text-amber-700' : 'text-stone-600' }} block">
                            Dalam Perawatan/Servis
                        </span>
                        <span class="text-lg font-black {{ $alat->stok_perawatan > 0 ? 'text-amber-800' : 'text-stone-900' }}">
                            {{ $alat->stok_perawatan }}
                        </span>
                        <span class="text-[10px] text-stone-500 block">unit sedang diperbaiki</span>
                    </div>
                </div>

                <div class="mb-4">
                    <h4 class="text-xs font-bold text-stone-700 uppercase mb-1">Deskripsi Alat</h4>
                    <p class="text-xs text-stone-600 leading-relaxed bg-stone-50 p-3 rounded-lg border border-stone-100">
                        {{ $alat->deskripsi ?? 'Belum ada deskripsi atau spesifikasi rinci untuk alat outdoor ini.' }}
                    </p>
                </div>
            </div>

            <div class="pt-4 border-t border-stone-100 text-[11px] text-stone-400 flex justify-between items-center">
                <span>Didaftarkan: {{ $alat->created_at?->format('d M Y, H:i') }}</span>
                <span>Terakhir diperbarui: {{ $alat->updated_at?->format('d M Y, H:i') }}</span>
            </div>
        </div>
    </div>

    <!-- Riwayat Penyewaan Alat Ini -->
    <div class="bg-white rounded-xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-stone-200">
            <h2 class="text-sm font-bold text-stone-900">Histori Penggunaan & Penyewaan Alat</h2>
            <p class="text-xs text-stone-500">Catatan transaksi rental yang melibatkan unit perlengkapan ini</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-stone-700">
                <thead class="bg-stone-50 text-stone-500 uppercase text-[10px] font-bold border-b border-stone-200 tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Kode Sewa</th>
                        <th class="py-3 px-4">Pelanggan</th>
                        <th class="py-3 px-4">Jumlah Disewa</th>
                        <th class="py-3 px-4">Harga Snapshot</th>
                        <th class="py-3 px-4">Subtotal</th>
                        <th class="py-3 px-4">Status Pesanan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($alat->pesananDetail as $detail)
                        <tr class="hover:bg-stone-50/60 transition">
                            <td class="py-3 px-4 font-mono font-bold text-stone-900">
                                {{ $detail->pesanan?->no_resi }}
                            </td>
                            <td class="py-3 px-4 font-medium text-stone-900">
                                {{ $detail->pesanan?->pelanggan?->nama_lengkap ?? '-' }}
                            </td>
                            <td class="py-3 px-4 font-semibold text-stone-900">
                                {{ $detail->jumlah }} unit
                            </td>
                            <td class="py-3 px-4">
                                Rp {{ number_format($detail->harga_per_hari, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 font-bold text-emerald-800">
                                Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4">
                                <x-badge-status :status="$detail->pesanan?->status" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-stone-400">
                                Belum ada riwayat penyewaan untuk alat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
