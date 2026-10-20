<x-layouts.admin>
    <x-slot:title>Dashboard Operasional Toko</x-slot:title>

    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-stone-900 tracking-tight">Dashboard Operasional</h1>
                <p class="text-xs text-stone-500">Ringkasan status transaksi penyewaan dan inventaris alat outdoor hari ini</p>
            </div>

            <!-- Tombol Aksi Cepat Admin -->
            <div class="flex items-center gap-2">
                <a href="{{ url('/admin/pesanan/create') }}" class="px-4 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-sm transition flex items-center gap-1.5">
                    <span>➕</span>
                    <span>Sewa Walk-in Baru</span>
                </a>
                <a href="{{ url('/admin/pengembalian') }}" class="px-4 py-2 rounded-lg bg-stone-900 hover:bg-stone-800 text-white text-xs font-bold shadow-sm transition flex items-center gap-1.5">
                    <span>🔄</span>
                    <span>Pengembalian Alat</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Grid Kartu Metrik Operasional -->
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Menunggu Konfirmasi -->
        <div class="bg-white p-4 rounded-xl border border-stone-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-600 block">Menunggu Bayar/Ambil</span>
                <span class="text-2xl font-black text-stone-900">{{ $statistik['pesanan_menunggu'] }}</span>
                <span class="text-[11px] text-stone-400 block mt-0.5">pesanan aktif</span>
            </div>
            <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-lg">
                ⏳
            </div>
        </div>

        <!-- Sedang Disewa -->
        <div class="bg-white p-4 rounded-xl border border-stone-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-blue-600 block">Sedang Disewa</span>
                <span class="text-2xl font-black text-stone-900">{{ $statistik['pesanan_disewa'] }}</span>
                <span class="text-[11px] text-stone-400 block mt-0.5">di tangan pelanggan</span>
            </div>
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg">
                ⛺
            </div>
        </div>

        <!-- Terlambat Kembali -->
        <div class="bg-white p-4 rounded-xl border {{ $statistik['pesanan_terlambat'] > 0 ? 'border-rose-300 bg-rose-50/30' : 'border-stone-200' }} shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-rose-600 block">Terlambat Kembali</span>
                <span class="text-2xl font-black {{ $statistik['pesanan_terlambat'] > 0 ? 'text-rose-700' : 'text-stone-900' }}">
                    {{ $statistik['pesanan_terlambat'] }}
                </span>
                <span class="text-[11px] text-stone-400 block mt-0.5">perlu penagihan</span>
            </div>
            <div class="w-10 h-10 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-lg">
                ⚠️
            </div>
        </div>

        <!-- Denda Belum Lunas -->
        <div class="bg-white p-4 rounded-xl border border-stone-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-stone-500 block">Denda Belum Lunas</span>
                <span class="text-lg font-black text-stone-900">
                    Rp {{ number_format($statistik['denda_belum_lunas'], 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-stone-400 block mt-0.5">keterlambatan/rusak</span>
            </div>
            <div class="w-10 h-10 rounded-lg bg-stone-100 text-stone-600 flex items-center justify-center font-bold text-lg">
                💸
            </div>
        </div>
    </div>

    <!-- Ringkasan Tambahan -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-2xl">🎒</span>
                <div>
                    <h3 class="font-bold text-emerald-950 text-xs">Total Inventaris Alat</h3>
                    <p class="text-xs text-emerald-800">{{ $statistik['total_alat'] }} jenis perlengkapan terdaftar</p>
                </div>
            </div>
            <a href="{{ url('/admin/alat') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-900 underline">
                Kelola Stok &rarr;
            </a>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-2xl">👥</span>
                <div>
                    <h3 class="font-bold text-blue-950 text-xs">Basis Data Pelanggan</h3>
                    <p class="text-xs text-blue-800">{{ $statistik['total_pelanggan'] }} pelanggan (Online & Walk-In)</p>
                </div>
            </div>
            <a href="{{ url('/admin/pelanggan') }}" class="text-xs font-bold text-blue-700 hover:text-blue-900 underline">
                Lihat Data &rarr;
            </a>
        </div>
    </div>

    <!-- Tabel Pesanan Terbaru -->
    <div class="bg-white rounded-xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-stone-200 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-stone-900">Aktivitas Pesanan Terbaru</h2>
                <p class="text-xs text-stone-500">6 transaksi penyewaan terakhir yang dicatat ke sistem</p>
            </div>
            <a href="{{ url('/admin/pesanan') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-900">
                Lihat Semua Pesanan &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-stone-700">
                <thead class="bg-stone-50 text-stone-500 uppercase text-[10px] font-bold border-b border-stone-200 tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Kode Sewa</th>
                        <th class="py-3 px-4">Pelanggan</th>
                        <th class="py-3 px-4">Tgl Sewa - Kembali</th>
                        <th class="py-3 px-4">Total Biaya</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($pesananTerbaru as $p)
                        <tr class="hover:bg-stone-50/60 transition">
                            <td class="py-3 px-4 font-mono font-bold text-stone-900">
                                {{ $p->kode }}
                                @if($p->tipe === 'walk_in')
                                    <span class="ml-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-800">Walk-in</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-medium text-stone-900">
                                {{ $p->pelanggan?->nama ?? '-' }}
                                <span class="block text-[10px] text-stone-400 font-normal">
                                    {{ $p->pelanggan?->no_hp ?? '' }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div>{{ $p->tanggal_mulai?->format('d/m/Y') }} s/d {{ $p->tanggal_selesai?->format('d/m/Y') }}</div>
                                <span class="text-[10px] text-stone-400">
                                    {{ $p->tanggal_mulai && $p->tanggal_selesai ? $p->tanggal_mulai->diffInDays($p->tanggal_selesai) : 0 }} hari sewa
                                </span>
                            </td>
                            <td class="py-3 px-4 font-bold text-stone-900">
                                Rp {{ number_format($p->total_harga, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4">
                                <x-badge-status :status="$p->status" />
                            </td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ url('/admin/pesanan/' . $p->id) }}" class="inline-block px-2.5 py-1 rounded bg-stone-100 hover:bg-stone-200 text-stone-800 font-semibold text-[11px] transition">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-stone-400">
                                Belum ada riwayat pesanan yang tercatat di database.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
