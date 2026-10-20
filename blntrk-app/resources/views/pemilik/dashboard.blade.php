<x-layouts.pemilik>
    <x-slot:title>Dashboard Eksekutif Pemilik Toko</x-slot:title>

    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Ringkasan Eksekutif & Finansial</h1>
                <p class="text-xs text-slate-500">Pemantauan arus pendapatan, performa rental, dan kondisi aset perlengkapan outdoor</p>
            </div>

            <!-- Tombol Aksi Pemilik -->
            <div class="flex items-center gap-2">
                <a href="{{ url('/pemilik/laporan') }}" class="px-4 py-2 rounded-lg bg-blue-700 hover:bg-blue-800 text-white text-xs font-bold shadow-sm transition flex items-center gap-1.5">
                    <span>📑</span>
                    <span>Cetak Laporan Usaha</span>
                </a>
                <a href="{{ url('/pemilik/pengaturan') }}" class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold shadow-sm transition flex items-center gap-1.5">
                    <span>⚙️</span>
                    <span>Tarif & Kebijakan Denda</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Grid Kartu Finansial & Aset -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Pendapatan Sewa -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600 block">Total Pendapatan Sewa</span>
                <span class="text-xl sm:text-2xl font-black text-slate-900 block mt-1">
                    Rp {{ number_format($ringkasan['pendapatan_sewa'], 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-slate-400 block mt-0.5">pembayaran manual tercatat</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xl">
                💰
            </div>
        </div>

        <!-- Pendapatan Denda -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-600 block">Denda Terkumpul</span>
                <span class="text-xl sm:text-2xl font-black text-slate-900 block mt-1">
                    Rp {{ number_format($ringkasan['pendapatan_denda'], 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-slate-400 block mt-0.5">keterlambatan/kerusakan</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xl">
                ⚡
            </div>
        </div>

        <!-- Total Transaksi -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-blue-600 block">Total Transaksi</span>
                <span class="text-xl sm:text-2xl font-black text-slate-900 block mt-1">
                    {{ $ringkasan['total_transaksi'] }}
                </span>
                <span class="text-[11px] text-slate-400 block mt-0.5">akumulasi pesanan sewa</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xl">
                📈
            </div>
        </div>

        <!-- Aset Alat & Perawatan -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block">Aset / Dalam Perawatan</span>
                <div class="flex items-baseline gap-2 mt-1">
                    <span class="text-xl sm:text-2xl font-black text-slate-900">{{ $ringkasan['total_alat'] }}</span>
                    <span class="text-xs font-semibold {{ $ringkasan['alat_dalam_perawatan'] > 0 ? 'text-amber-600' : 'text-slate-400' }}">
                        ({{ $ringkasan['alat_dalam_perawatan'] }} servis)
                    </span>
                </div>
                <span class="text-[11px] text-slate-400 block mt-0.5">katalog alat & status</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-xl">
                🛠️
            </div>
        </div>
    </div>

    <!-- Ringkasan Finansial Bersih -->
    <div class="bg-gradient-to-r from-slate-900 via-blue-950 to-slate-900 text-white p-6 rounded-2xl shadow-md mb-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <span class="text-[11px] font-bold text-blue-300 uppercase tracking-wider block mb-1">Total Penerimaan Kas Toko</span>
            <div class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                Rp {{ number_format($ringkasan['pendapatan_sewa'] + $ringkasan['pendapatan_denda'], 0, ',', '.') }}
            </div>
            <p class="text-xs text-slate-300 mt-1">
                Kombinasi pendapatan sewa alat murni dan denda lunas yang telah diterima oleh admin di toko fisik.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ url('/pemilik/laporan?periode=bulan_ini') }}" class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition shadow-sm">
                Lihat Laporan Bulan Ini &rarr;
            </a>
        </div>
    </div>

    <!-- Tabel Transaksi Terbaru (Perspektif Pemilik) -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Histori Transaksi Sewa Terbaru</h2>
                <p class="text-xs text-slate-500">Catatan transaksi rental beserta petugas pemroses</p>
            </div>
            <a href="{{ url('/pemilik/laporan') }}" class="text-xs font-bold text-blue-700 hover:text-blue-900">
                Laporan Selengkapnya &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold border-b border-slate-200 tracking-wider">
                    <tr>
                        <th class="py-3 px-4">Kode</th>
                        <th class="py-3 px-4">Nama Pelanggan</th>
                        <th class="py-3 px-4">Periode Sewa</th>
                        <th class="py-3 px-4">Nilai Sewa</th>
                        <th class="py-3 px-4">Status Pesanan</th>
                        <th class="py-3 px-4">Diproses Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transaksiTerbaru as $t)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3 px-4 font-mono font-bold text-slate-900">
                                {{ $t->kode }}
                            </td>
                            <td class="py-3 px-4 font-medium text-slate-900">
                                {{ $t->pelanggan?->nama ?? '-' }}
                                <span class="block text-[10px] text-slate-400 font-normal">
                                    {{ $t->pelanggan?->no_hp ?? '' }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                {{ $t->tanggal_mulai?->format('d/m/Y') }} - {{ $t->tanggal_selesai?->format('d/m/Y') }}
                            </td>
                            <td class="py-3 px-4 font-bold text-emerald-700">
                                Rp {{ number_format($t->total_harga, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4">
                                <x-badge-status :status="$t->status" />
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                {{ $t->pemroses?->nama ?? 'Admin Toko' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                Belum ada catatan transaksi sewa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.pemilik>
