<x-layouts.admin>
    <x-slot:title>Detail Alat - {{ $alat->nama }}</x-slot:title>

    <div class="mb-7 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('admin.alat.index') }}" class="text-xs font-bold text-stone-500 hover:text-[#0e1b15] transition inline-flex items-center gap-1.5 mb-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Daftar Alat</span>
            </a>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#14201a] tracking-tight bl-d">{{ $alat->nama }}</h1>
            <p class="text-xs text-stone-500 mt-0.5">Kode Alat: <span class="font-mono font-bold text-[#4e7a5a]">{{ $alat->kode_alat }}</span> • Kategori: {{ $alat->kategori?->nama ?? 'Umum' }}</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.alat.edit', $alat->id) }}" class="px-4 py-2.5 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-extrabold transition shadow-sm cursor-pointer inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                <span>Edit Alat</span>
            </a>
        </div>
    </div>

    <!-- Ringkasan Profil Alat -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Foto Alat -->
        <div class="bg-white p-5 rounded-2xl border border-stone-200/90 shadow-xs flex flex-col items-center justify-center">
            <div class="w-full h-64 rounded-2xl bg-stone-100 overflow-hidden flex items-center justify-center border border-stone-200">
                @if($alat->foto)
                    <img src="{{ asset('storage/' . $alat->foto) }}" alt="{{ $alat->nama }}" class="w-full h-full object-cover">
                @else
                    <div class="text-center p-6 text-stone-300">
                        <svg class="w-16 h-16 mx-auto mb-2 text-stone-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3l4 8 5-5 5 15H2L8 3z"/></svg>
                        <span class="text-xs text-stone-400 font-medium">Belum ada foto yang diunggah</span>
                    </div>
                @endif
            </div>

            <div class="mt-4 w-full flex items-center justify-between text-xs">
                <span class="text-stone-500 font-medium">Status Visibilitas:</span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $alat->is_aktif ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-stone-100 text-stone-600' }}">
                    {{ $alat->is_aktif ? 'Aktif di Katalog' : 'Nonaktif' }}
                </span>
            </div>
        </div>

        <!-- Spesifikasi & Stok -->
        <div class="md:col-span-2 bg-white p-6 sm:p-7 rounded-2xl border border-stone-200/90 shadow-xs flex flex-col justify-between">
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-stone-500 mb-4 pb-2 border-b border-stone-100">
                    Spesifikasi & Informasi Tarif
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                    <div class="p-4 rounded-xl bg-emerald-50/70 border border-emerald-200/60">
                        <span class="text-[10px] uppercase font-bold text-emerald-800 block">Tarif Sewa Harian</span>
                        <span class="text-xl font-extrabold text-[#14201a] block mt-0.5">
                            Rp {{ number_format($alat->harga_per_hari, 0, ',', '.') }}
                        </span>
                        <span class="text-[10px] text-emerald-700 block font-medium">per 24 jam</span>
                    </div>

                    <div class="p-4 rounded-xl bg-stone-50 border border-stone-200">
                        <span class="text-[10px] uppercase font-bold text-stone-600 block">Total Stok Fisik</span>
                        <span class="text-xl font-extrabold text-stone-900 block mt-0.5">
                            {{ $alat->stok_total }} unit
                        </span>
                        <span class="text-[10px] text-stone-500 block font-medium">terdaftar di toko</span>
                    </div>

                    <div class="p-4 rounded-xl {{ $alat->stok_perawatan > 0 ? 'bg-amber-50/70 border border-amber-200' : 'bg-stone-50 border border-stone-200' }}">
                        <span class="text-[10px] uppercase font-bold {{ $alat->stok_perawatan > 0 ? 'text-amber-800' : 'text-stone-600' }} block">
                            Dalam Servis
                        </span>
                        <span class="text-xl font-extrabold {{ $alat->stok_perawatan > 0 ? 'text-amber-900' : 'text-stone-900' }} block mt-0.5">
                            {{ $alat->stok_perawatan }} unit
                        </span>
                        <span class="text-[10px] text-stone-500 block font-medium">sedang perbaikan</span>
                    </div>
                </div>

                <div class="mb-4">
                    <h4 class="text-xs font-bold text-[#14201a] uppercase mb-1.5">Deskripsi & Kelengkapan</h4>
                    <p class="text-xs text-stone-600 leading-relaxed bg-stone-50 p-4 rounded-xl border border-stone-100">
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
    <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-stone-200/80 bg-stone-50/50">
            <h2 class="text-sm font-extrabold text-[#14201a]">Histori Penggunaan & Penyewaan Alat</h2>
            <p class="text-xs text-stone-500">Catatan transaksi rental yang melibatkan unit perlengkapan ini</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-stone-700">
                <thead class="bg-stone-50/80 text-stone-500 uppercase text-[10px] font-bold border-b border-stone-200 tracking-wider">
                    <tr>
                        <th class="py-3 px-5">Kode Resi</th>
                        <th class="py-3 px-4">Pelanggan</th>
                        <th class="py-3 px-4">Jumlah Disewa</th>
                        <th class="py-3 px-4">Harga Snapshot</th>
                        <th class="py-3 px-4">Subtotal</th>
                        <th class="py-3 px-5">Status Pesanan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($alat->pesananDetail as $detail)
                        <tr class="hover:bg-stone-50/60 transition">
                            <td class="py-3.5 px-5 font-mono font-bold text-[#14201a]">
                                {{ $detail->pesanan?->no_resi }}
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-[#14201a]">
                                {{ $detail->pesanan?->pelanggan?->nama_lengkap ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 font-bold text-stone-800">
                                {{ $detail->jumlah }} unit
                            </td>
                            <td class="py-3.5 px-4 text-stone-600">
                                Rp {{ number_format($detail->harga_per_hari, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 font-extrabold text-[#14201a]">
                                Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-5">
                                <x-badge-status :status="$detail->pesanan?->status" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-stone-400">
                                <div class="w-10 h-10 rounded-2xl bg-stone-100 text-stone-400 mx-auto flex items-center justify-center mb-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                Belum ada riwayat penyewaan untuk alat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>

