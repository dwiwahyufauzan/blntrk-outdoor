<x-layouts.pemilik>
    <x-slot:title>Dashboard Eksekutif Pemilik Toko</x-slot:title>

    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-[#14201a] tracking-tight bl-d">Ringkasan Eksekutif & Finansial</h1>
                <p class="text-xs text-stone-500 mt-0.5">Pemantauan arus pendapatan, performa rental, dan kondisi aset perlengkapan outdoor</p>
            </div>

            <!-- Tombol Aksi Pemilik -->
            <div class="flex items-center gap-2">
                <a href="{{ url('/pemilik/laporan') }}" class="px-4 py-2.5 rounded-xl bg-[#0e1b15] hover:bg-[#1e3a2c] text-[#f6c453] text-xs font-bold shadow-xs transition inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Laporan Usaha</span>
                </a>
                <a href="{{ url('/pemilik/pengaturan') }}" class="px-4 py-2.5 rounded-xl bg-white hover:bg-stone-50 text-[#14201a] border border-stone-200/90 text-xs font-bold shadow-xs transition inline-flex items-center gap-2">
                    <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Tarif & Denda</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Grid Kartu Finansial & Aset -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Pendapatan Sewa -->
        <div class="bg-white p-5 rounded-2xl border border-stone-200/90 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-[#1e3a2c] block">Total Pendapatan Sewa</span>
                <span class="text-xl sm:text-2xl font-black text-[#14201a] block mt-1 bl-d">
                    Rp {{ number_format($ringkasan['pendapatan_sewa'], 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-stone-400 block mt-0.5">pembayaran manual kasir</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-[#0e1b15] text-[#f6c453] flex items-center justify-center font-bold text-xl shadow-xs">
                Rp
            </div>
        </div>

        <!-- Pendapatan Denda -->
        <div class="bg-white p-5 rounded-2xl border border-stone-200/90 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-700 block">Denda Terkumpul</span>
                <span class="text-xl sm:text-2xl font-black text-[#14201a] block mt-1 bl-d">
                    Rp {{ number_format($ringkasan['pendapatan_denda'], 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-stone-400 block mt-0.5">keterlambatan & kerusakan</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xl">
                ⚡
            </div>
        </div>

        <!-- Total Transaksi -->
        <div class="bg-white p-5 rounded-2xl border border-stone-200/90 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-[#4e7a5a] block">Total Transaksi</span>
                <span class="text-xl sm:text-2xl font-black text-[#14201a] block mt-1 bl-d">
                    {{ $ringkasan['total_transaksi'] }}
                </span>
                <span class="text-[11px] text-stone-400 block mt-0.5">akumulasi pesanan sewa</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-xl">
                📈
            </div>
        </div>

        <!-- Aset Alat & Perawatan -->
        <div class="bg-white p-5 rounded-2xl border border-stone-200/90 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-stone-500 block">Aset / Dalam Servis</span>
                <div class="flex items-baseline gap-2 mt-1">
                    <span class="text-xl sm:text-2xl font-black text-[#14201a] bl-d">{{ $ringkasan['total_alat'] }}</span>
                    <span class="text-xs font-semibold {{ $ringkasan['alat_dalam_perawatan'] > 0 ? 'text-amber-600' : 'text-stone-400' }}">
                        ({{ $ringkasan['alat_dalam_perawatan'] }} servis)
                    </span>
                </div>
                <span class="text-[11px] text-stone-400 block mt-0.5">katalog alat & inventaris</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-stone-100 text-stone-600 flex items-center justify-center font-bold text-xl">
                🛠️
            </div>
        </div>
    </div>

    <!-- Ringkasan Finansial Bersih Banner Forest -->
    <div class="bg-gradient-to-r from-[#0e1b15] via-[#14201a] to-[#1e3a2c] text-white p-6 sm:p-7 rounded-2xl shadow-sm mb-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border border-[#1e3a2c]">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2 h-2 rounded-full bg-[#f6c453]"></span>
                <span class="text-[11px] font-bold text-[#f6c453] uppercase tracking-wider">Total Penerimaan Kas Masuk</span>
            </div>
            <div class="text-2xl sm:text-3xl font-black tracking-tight text-white bl-d">
                Rp {{ number_format($ringkasan['pendapatan_sewa'] + $ringkasan['pendapatan_denda'], 0, ',', '.') }}
            </div>
            <p class="text-xs text-stone-300 mt-1 max-w-xl">
                Akumulasi pendapatan sewa alat murni ditambah denda keterlambatan/kerusakan yang telah dilunasi di toko fisik.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ url('/pemilik/laporan?periode=bulan_ini') }}" class="px-5 py-2.5 rounded-xl bg-[#f6c453] hover:bg-[#ffd36e] text-[#0e1b15] font-black text-xs transition shadow-xs inline-flex items-center gap-2">
                <span>Lihat Laporan Periode Ini</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>
    </div>

    <!-- Tabel Transaksi Terbaru (Perspektif Pemilik) -->
    <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-stone-200/80 flex items-center justify-between">
            <div>
                <h2 class="text-base font-black text-[#14201a] bl-d">Histori Transaksi Sewa Terbaru</h2>
                <p class="text-xs text-stone-500 mt-0.5">Catatan pesanan sewa terkini beserta petugas admin pemroses</p>
            </div>
            <a href="{{ url('/pemilik/laporan') }}" class="text-xs font-bold text-[#1e3a2c] hover:text-[#0e1b15] inline-flex items-center gap-1">
                <span>Laporan Selengkapnya</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-[#14201a]">
                <thead class="bg-[#fbfbf9] text-stone-500 uppercase text-[10px] font-bold border-b border-stone-200/80 tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">No. Resi</th>
                        <th class="py-3.5 px-4">Nama Pelanggan</th>
                        <th class="py-3.5 px-4">Periode Sewa</th>
                        <th class="py-3.5 px-4">Nilai Sewa</th>
                        <th class="py-3.5 px-4">Status Pesanan</th>
                        <th class="py-3.5 px-4">Diproses Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($transaksiTerbaru as $t)
                        <tr class="hover:bg-stone-50/60 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-[#14201a]">
                                {{ $t->no_resi }}
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-[#14201a]">
                                {{ $t->pelanggan?->nama_lengkap ?? '-' }}
                                <span class="block text-[10px] text-stone-400 font-normal font-mono">
                                    {{ $t->pelanggan?->no_hp ?? '' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-stone-600">
                                {{ $t->tanggal_mulai?->format('d/m/Y') }} - {{ $t->tanggal_selesai?->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-4 font-black text-[#1e3a2c]">
                                Rp {{ number_format($t->total_sewa, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4">
                                <x-badge-status :status="$t->status" />
                            </td>
                            <td class="py-3.5 px-4 text-stone-600">
                                {{ $t->pemroses?->nama ?? 'Admin Toko' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-stone-400 text-xs">
                                Belum ada catatan transaksi sewa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.pemilik>
