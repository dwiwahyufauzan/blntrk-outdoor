<x-layouts.admin>
    <x-slot:title>Dashboard Operasional Toko</x-slot:title>

    <div class="mb-7">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-widest text-[#4e7a5a] block mb-1">Pusat Kendali Operasional</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-[#14201a] tracking-tight bl-d">Dashboard Toko</h1>
                <p class="text-xs text-stone-500 mt-0.5">Status transaksi rental harian, serah terima alat, dan ketersediaan stok fisik</p>
            </div>

            <!-- Tombol Aksi Cepat Admin -->
            <div class="flex items-center gap-2.5">
                <a href="{{ url('/admin/pesanan/create') }}" class="px-4 py-2.5 rounded-xl bg-[#0e1b15] hover:bg-[#1e3a2c] text-[#f6c453] text-xs font-bold shadow-sm transition inline-flex items-center gap-2 cursor-pointer">
                    <span>➕</span>
                    <span>Sewa Walk-in Baru</span>
                </a>
                <a href="{{ url('/admin/pengembalian') }}" class="px-4 py-2.5 rounded-xl bg-white hover:bg-stone-50 border border-stone-200 text-[#14201a] text-xs font-bold shadow-2xs transition inline-flex items-center gap-2 cursor-pointer">
                    <span>🔄</span>
                    <span>Pengembalian Alat</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Grid Kartu Metrik Operasional -->
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Menunggu Konfirmasi -->
        <div class="bg-white p-5 rounded-2xl border border-stone-200/90 shadow-xs flex items-center justify-between hover:border-stone-300 transition">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700 block">Menunggu Ambil/Bayar</span>
                <span class="text-3xl font-extrabold text-[#14201a] bl-d block mt-1">{{ $statistik['pesanan_menunggu'] }}</span>
                <span class="text-[11px] text-stone-400 block mt-0.5 font-medium">pesanan aktif</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-200/60 text-amber-700 flex items-center justify-center font-bold text-xl shadow-2xs">
                ⏳
            </div>
        </div>

        <!-- Sedang Disewa -->
        <div class="bg-white p-5 rounded-2xl border border-stone-200/90 shadow-xs flex items-center justify-between hover:border-stone-300 transition">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-700 block">Sedang Disewa</span>
                <span class="text-3xl font-extrabold text-[#14201a] bl-d block mt-1">{{ $statistik['pesanan_disewa'] }}</span>
                <span class="text-[11px] text-stone-400 block mt-0.5 font-medium">di tangan pendaki</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 border border-blue-200/60 text-blue-700 flex items-center justify-center font-bold text-xl shadow-2xs">
                ⛺
            </div>
        </div>

        <!-- Terlambat Kembali -->
        <div class="bg-white p-5 rounded-2xl border {{ $statistik['pesanan_terlambat'] > 0 ? 'border-rose-300 bg-rose-50/20' : 'border-stone-200/90' }} shadow-xs flex items-center justify-between hover:border-rose-400 transition">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-rose-700 block">Terlambat Kembali</span>
                <span class="text-3xl font-extrabold {{ $statistik['pesanan_terlambat'] > 0 ? 'text-rose-700' : 'text-[#14201a]' }} bl-d block mt-1">
                    {{ $statistik['pesanan_terlambat'] }}
                </span>
                <span class="text-[11px] text-stone-400 block mt-0.5 font-medium">lewat batas 12:00</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-50 border border-rose-200/60 text-rose-700 flex items-center justify-center font-bold text-xl shadow-2xs">
                ⚠️
            </div>
        </div>

        <!-- Denda Belum Lunas -->
        <div class="bg-white p-5 rounded-2xl border border-stone-200/90 shadow-xs flex items-center justify-between hover:border-stone-300 transition">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-stone-500 block">Denda Belum Lunas</span>
                <span class="text-xl font-extrabold text-[#14201a] bl-d block mt-1">
                    Rp {{ number_format($statistik['denda_belum_lunas'], 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-stone-400 block mt-0.5 font-medium">terlambat/rusak</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-stone-100 border border-stone-200 text-stone-600 flex items-center justify-center font-bold text-xl shadow-2xs">
                💸
            </div>
        </div>
    </div>

    <!-- Ringkasan Tambahan (Banner Mini) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
        <div class="bg-white border border-stone-200/90 rounded-2xl p-5 flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-[#0e1b15] text-[#f6c453] flex items-center justify-center text-xl shadow-2xs">
                    🎒
                </div>
                <div>
                    <h3 class="font-extrabold text-[#14201a] text-xs bl-d">Inventaris Alat Outdoor</h3>
                    <p class="text-xs text-stone-500 font-medium">{{ $statistik['total_alat'] }} macam perlengkapan aktif terdaftar</p>
                </div>
            </div>
            <a href="{{ route('admin.alat.index') }}" class="px-3.5 py-1.5 rounded-xl bg-stone-50 hover:bg-stone-100 border border-stone-200 text-[#14201a] text-xs font-bold transition">
                Kelola Stok &rarr;
            </a>
        </div>

        <div class="bg-white border border-stone-200/90 rounded-2xl p-5 flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-[#1e3a2c] text-white flex items-center justify-center text-xl shadow-2xs">
                    👥
                </div>
                <div>
                    <h3 class="font-extrabold text-[#14201a] text-xs bl-d">Basis Data Pelanggan</h3>
                    <p class="text-xs text-stone-500 font-medium">{{ $statistik['total_pelanggan'] }} pelanggan (Online & Walk-In)</p>
                </div>
            </div>
            <a href="{{ route('admin.pelanggan.index') }}" class="px-3.5 py-1.5 rounded-xl bg-stone-50 hover:bg-stone-100 border border-stone-200 text-[#14201a] text-xs font-bold transition">
                Lihat Data &rarr;
            </a>
        </div>
    </div>

    <!-- Tabel Pesanan Terbaru -->
    <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-stone-200/80 flex items-center justify-between bg-stone-50/50">
            <div>
                <h2 class="text-sm font-extrabold text-[#14201a] bl-d">Aktivitas Pesanan Terbaru</h2>
                <p class="text-xs text-stone-500">6 transaksi penyewaan terakhir yang dicatat ke sistem</p>
            </div>
            <a href="{{ url('/admin/pesanan') }}" class="text-xs font-bold text-[#1e3a2c] hover:text-[#0e1b15] hover:underline flex items-center gap-1 transition">
                <span>Lihat Semua Pesanan</span>
                <span>&rarr;</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-stone-700">
                <thead class="bg-stone-50/80 text-stone-500 uppercase text-[10px] font-bold border-b border-stone-200 tracking-wider">
                    <tr>
                        <th class="py-3 px-5">Kode Resi</th>
                        <th class="py-3 px-4">Pelanggan</th>
                        <th class="py-3 px-4">Jadwal Sewa</th>
                        <th class="py-3 px-4">Total Biaya</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($pesananTerbaru as $p)
                        <tr class="hover:bg-stone-50/60 transition">
                            <td class="py-3.5 px-5 font-mono font-bold text-[#14201a]">
                                {{ $p->no_resi }}
                                @if($p->pelanggan?->is_walk_in)
                                    <span class="ml-1.5 px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-[#f6c453]/25 text-[#14201a] border border-[#f6c453]/40">Walk-in</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-[#14201a]">
                                {{ $p->pelanggan?->nama_lengkap ?? '-' }}
                                <span class="block text-[10px] text-stone-400 font-normal mt-0.5">
                                    {{ $p->pelanggan?->no_hp ?? '' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-medium text-stone-800">{{ $p->tanggal_mulai?->format('d/m/Y') }} &ndash; {{ $p->tanggal_selesai?->format('d/m/Y') }}</div>
                                <span class="text-[10px] text-stone-400 font-medium">
                                    {{ $p->durasi_hari ?? ($p->tanggal_mulai && $p->tanggal_selesai ? $p->tanggal_mulai->diffInDays($p->tanggal_selesai) : 0) }} hari sewa
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-extrabold text-[#14201a] bl-d text-sm">
                                Rp {{ number_format($p->total_sewa, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4">
                                <x-badge-status :status="$p->status" />
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                <a href="{{ url('/admin/pesanan/' . $p->id) }}" class="inline-block px-3 py-1.5 rounded-xl bg-stone-100 hover:bg-[#0e1b15] hover:text-[#f6c453] text-stone-700 font-bold text-[11px] transition">
                                    Detail &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-stone-400">
                                <span class="text-2xl block mb-1">📋</span>
                                Belum ada riwayat pesanan yang tercatat di database.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>

