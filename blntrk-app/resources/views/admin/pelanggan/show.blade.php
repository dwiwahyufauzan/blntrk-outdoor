<x-layouts.admin>
    <x-slot:title>Detail Pelanggan - {{ $pelanggan->nama_lengkap }}</x-slot:title>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('admin.pelanggan.index') }}" class="text-xs font-semibold text-stone-500 hover:text-emerald-700 transition flex items-center gap-1 mb-2">
                &larr; Kembali ke Daftar Pelanggan
            </a>
            <h1 class="text-2xl font-black text-stone-900 tracking-tight">{{ $pelanggan->nama_lengkap }}</h1>
            <p class="text-xs text-stone-500">ID Pelanggan: #{{ $pelanggan->id }} • Terdaftar sejak {{ $pelanggan->created_at?->format('d M Y') }}</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.pelanggan.edit', $pelanggan->id) }}" class="px-4 py-2 rounded-lg bg-stone-900 hover:bg-stone-800 text-white text-xs font-bold transition shadow-sm">
                Edit Data ✏️
            </a>
        </div>
    </div>

    <!-- Informasi Profil Pelanggan -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl border border-stone-200 shadow-sm flex flex-col justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400 block mb-1">Tipe Pelanggan</span>
                <div class="mb-4">
                    @if($pelanggan->is_walk_in)
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                            Penyewa Walk-In (Langsung di Toko)
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
                            Pelanggan Terdaftar (Akun Daring)
                        </span>
                    @endif
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-stone-400 block text-[11px]">Nomor WhatsApp:</span>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $pelanggan->no_hp) }}" target="_blank" class="font-mono font-bold text-emerald-700 hover:underline">
                            {{ $pelanggan->no_hp }} ↗
                        </a>
                    </div>

                    <div>
                        <span class="text-stone-400 block text-[11px]">Akun Email Login:</span>
                        <span class="font-medium text-stone-800">{{ $pelanggan->user?->email ?? '(Tidak memiliki akun login)' }}</span>
                    </div>

                    <div>
                        <span class="text-stone-400 block text-[11px]">Alamat:</span>
                        <p class="text-stone-700 leading-relaxed">{{ $pelanggan->alamat }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metrik Singkat Pelanggan -->
        <div class="md:col-span-2 bg-white p-6 rounded-xl border border-stone-200 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-stone-500 mb-4 pb-2 border-b border-stone-100">
                    Statistik Riwayat Rental
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-100">
                        <span class="text-[10px] font-bold text-emerald-700 uppercase block">Total Transaksi</span>
                        <span class="text-2xl font-black text-emerald-950">{{ $pelanggan->pesanan->count() }}</span>
                        <span class="text-[11px] text-emerald-700 block mt-0.5">pesanan sewa</span>
                    </div>

                    <div class="p-4 rounded-xl bg-blue-50 border border-blue-100">
                        <span class="text-[10px] font-bold text-blue-700 uppercase block">Total Belanja Sewa</span>
                        <span class="text-xl font-black text-blue-950">
                            Rp {{ number_format($pelanggan->pesanan->sum('total_harga'), 0, ',', '.') }}
                        </span>
                        <span class="text-[11px] text-blue-700 block mt-0.5">akumulasi rental</span>
                    </div>

                    <div class="p-4 rounded-xl bg-stone-50 border border-stone-200">
                        <span class="text-[10px] font-bold text-stone-600 uppercase block">Status Akun</span>
                        <span class="text-sm font-bold text-stone-900 block mt-2">
                            {{ $pelanggan->user?->is_aktif ? 'Aktif' : ($pelanggan->user ? 'Nonaktif' : 'Walk-In') }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-stone-100 text-right">
                <a href="{{ url('/admin/pesanan?pelanggan_id=' . $pelanggan->id) }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-900">
                    Buka Riwayat Lengkap &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Tabel Riwayat Pesanan -->
    <div class="bg-white rounded-xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-stone-200">
            <h2 class="text-sm font-bold text-stone-900">Riwayat Pesanan Rental Terakhir</h2>
            <p class="text-xs text-stone-500">Maksimal 10 transaksi penyewaan terakhir oleh pelanggan ini</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-stone-700">
                <thead class="bg-stone-50 text-stone-500 uppercase text-[10px] font-bold border-b border-stone-200 tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Kode Sewa</th>
                        <th class="py-3 px-4">Tgl Sewa - Kembali</th>
                        <th class="py-3 px-4">Total Biaya</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($pelanggan->pesanan as $pesanan)
                        <tr class="hover:bg-stone-50/60 transition">
                            <td class="py-3 px-4 font-mono font-bold text-stone-900">
                                {{ $pesanan->no_resi }}
                            </td>
                            <td class="py-3 px-4">
                                {{ $pesanan->tanggal_mulai?->format('d/m/Y') }} s/d {{ $pesanan->tanggal_selesai?->format('d/m/Y') }}
                            </td>
                            <td class="py-3 px-4 font-bold text-stone-900">
                                Rp {{ number_format($pesanan->total_sewa, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4">
                                <x-badge-status :status="$pesanan->status" />
                            </td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ url('/admin/pesanan/' . $pesanan->id) }}" class="inline-block px-2.5 py-1 rounded bg-stone-100 hover:bg-stone-200 text-stone-800 font-semibold text-[11px] transition">
                                    Lihat Pesanan
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-stone-400">
                                Pelanggan ini belum memiliki riwayat pesanan sewa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
