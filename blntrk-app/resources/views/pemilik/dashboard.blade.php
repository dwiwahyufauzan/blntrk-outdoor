<x-layouts.pemilik>
    <x-slot:title>Dashboard Eksekutif Pemilik Toko</x-slot:title>

    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-widest text-[#4e7a5a] block mb-1">Pengawasan Pemilik (Read-Only)</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-[#14201a] tracking-tight bl-d">Ringkasan Eksekutif & Finansial</h1>
                <p class="text-xs text-stone-500 mt-0.5">Pemantauan arus kas riil, performa sewa, kondisi aset, dan peringatan inventaris kritis</p>
            </div>

            <!-- Tombol Aksi Pemilik -->
            <div class="flex items-center gap-2">
                <a href="{{ route('pemilik.laporan.index') }}" class="px-4 py-2.5 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-extrabold shadow-sm transition inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Laporan Usaha & PDF</span>
                </a>
                <a href="{{ url('/pemilik/pengaturan') }}" class="px-4 py-2.5 rounded-xl bg-white hover:bg-stone-50 text-[#14201a] border border-stone-200/90 text-xs font-bold shadow-xs transition inline-flex items-center gap-2">
                    <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Tarif & Denda</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Grid 4 Kartu Finansial & Operasional Utama (T10) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- 1. Pendapatan Hari Ini -->
        <div class="bg-white p-5 rounded-2xl border border-stone-200/90 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-800 block">Pendapatan Hari Ini</span>
                <span class="text-2xl font-black text-[#14201a] font-mono block mt-1">
                    Rp{{ number_format($ringkasan['pendapatan_hari_ini'], 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-stone-400 block mt-0.5">kas masuk sewa + denda</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-xl shadow-2xs">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <!-- 2. Pendapatan Bulan Ini -->
        <div class="bg-white p-5 rounded-2xl border border-stone-200/90 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-[#4e7a5a] block">Pendapatan Bulan Ini</span>
                <span class="text-2xl font-black text-[#14201a] font-mono block mt-1">
                    Rp{{ number_format($ringkasan['pendapatan_bulan_ini'], 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-stone-400 block mt-0.5">akumulasi kas {{ now()->translatedFormat('F Y') }}</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-[#0e1b15] text-[#5edd5b] flex items-center justify-center font-bold text-xl shadow-2xs">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </div>

        <!-- 3. Total Transaksi Sewa -->
        <div class="bg-white p-5 rounded-2xl border border-stone-200/90 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-stone-500 block">Total Transaksi Sewa</span>
                <span class="text-2xl font-black text-[#14201a] block mt-1">
                    {{ $ringkasan['total_transaksi'] }}
                </span>
                <span class="text-[11px] text-stone-400 block mt-0.5">pesanan aktif / selesai</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-700 flex items-center justify-center font-bold text-xl shadow-2xs">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
        </div>

        <!-- 4. Terlambat Kembali -->
        <div class="bg-white p-5 rounded-2xl border {{ $ringkasan['jumlah_terlambat'] > 0 ? 'border-rose-300 bg-rose-50/20' : 'border-stone-200/90' }} shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-rose-700 block">Terlambat Kembali</span>
                <span class="text-2xl font-black {{ $ringkasan['jumlah_terlambat'] > 0 ? 'text-rose-700' : 'text-[#14201a]' }} block mt-1">
                    {{ $ringkasan['jumlah_terlambat'] }}
                </span>
                <span class="text-[11px] text-stone-400 block mt-0.5">berpotensi denda</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-xl shadow-2xs">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
        </div>
    </div>

    <!-- Grafik Tren Pendapatan 6 Bulan Terakhir (Chart.js) -->
    <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs p-5 sm:p-6 mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-stone-100 gap-2 mb-4">
            <div>
                <h2 class="text-base font-black text-[#14201a] bl-d">Grafik Pendapatan Kas Masuk (6 Bulan Terakhir)</h2>
                <p class="text-xs text-stone-500">Pertumbuhan penerimaan kas sewa alat murni ditambah denda lunas per bulan</p>
            </div>
            <a href="{{ route('pemilik.laporan.index', ['jenis' => 'pendapatan']) }}" class="text-xs font-bold text-[#0e1b15] hover:text-[#5edd5b] flex items-center gap-1">
                <span>Lihat Buku Kas Laporan</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>

        <div class="relative w-full aspect-16/9 sm:aspect-21/9 h-64 sm:h-72">
            <canvas id="chartPendapatan6Bulan"></canvas>
        </div>
    </div>

    <!-- Grid 2 Kolom: Alat Terlaris & Stok Kritis -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        
        <!-- Kolom Kiri: Top 5 Alat Terlaris Disewa -->
        <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs p-5">
            <div class="flex items-center justify-between pb-3 border-b border-stone-100 mb-4">
                <div>
                    <h3 class="text-sm font-extrabold text-[#14201a]">Top 5 Alat Terlaris Disewa</h3>
                    <p class="text-[11px] text-stone-400">Peralatan paling diminati pelanggan dalam 6 bulan</p>
                </div>
                <a href="{{ route('pemilik.laporan.index', ['jenis' => 'terlaris']) }}" class="text-[11px] font-bold text-[#4e7a5a] hover:underline">
                    Semua &rarr;
                </a>
            </div>

            <div class="space-y-3">
                @forelse($alatTerlaris as $idx => $alat)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-stone-50/70 border border-stone-200/70 text-xs">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-lg bg-[#0e1b15] text-[#5edd5b] font-black text-xs flex items-center justify-center shrink-0">
                                {{ $idx + 1 }}
                            </span>
                            <div>
                                <div class="font-extrabold text-[#14201a]">{{ $alat->alat?->nama ?? 'Alat' }}</div>
                                <span class="text-[10px] text-stone-400 font-mono">{{ $alat->alat?->kategori?->nama }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="font-black text-stone-900 block">{{ $alat->total_disewa }} unit</span>
                            <span class="text-[10px] text-stone-500 font-mono">Rp{{ number_format($alat->total_pendapatan, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-stone-400 text-xs">
                        Belum ada data penyewaan alat pada periode ini.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Kolom Kanan: Peringatan Stok Kritis (Stok <= 2) -->
        <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs p-5">
            <div class="flex items-center justify-between pb-3 border-b border-stone-100 mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full {{ $stokKritis->isNotEmpty() ? 'bg-amber-500' : 'bg-emerald-500' }}"></div>
                    <h3 class="text-sm font-extrabold text-[#14201a]">Monitoring Stok Kritis</h3>
                </div>
                <span class="text-[11px] text-stone-400">Tersedia &le; 2 unit</span>
            </div>

            <div class="space-y-3">
                @forelse($stokKritis as $kritis)
                    <div class="flex items-center justify-between p-3 rounded-xl {{ $kritis->stok_tersedia === 0 ? 'bg-rose-50/60 border-rose-200/80' : 'bg-amber-50/40 border-amber-200/70' }} border text-xs">
                        <div>
                            <div class="font-bold text-[#14201a]">{{ $kritis->nama }}</div>
                            <span class="text-[10px] text-stone-500 font-mono">Kode: {{ $kritis->kode_alat }} &bull; Total: {{ $kritis->stok_total }} unit</span>
                        </div>
                        <div class="text-right">
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase {{ $kritis->stok_tersedia === 0 ? 'bg-rose-600 text-white' : 'bg-amber-200 text-amber-900' }}">
                                {{ $kritis->stok_tersedia === 0 ? 'HABIS' : 'Sisa ' . $kritis->stok_tersedia . ' Unit' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-stone-400 text-xs">
                        Seluruh stok alat dalam kondisi aman (&gt; 2 unit tersedia).
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Tabel Histori Transaksi Terbaru -->
    <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-stone-200/80 flex items-center justify-between">
            <div>
                <h2 class="text-base font-black text-[#14201a] bl-d">Histori Transaksi Sewa Terbaru</h2>
                <p class="text-xs text-stone-500 mt-0.5">Catatan pesanan sewa terkini beserta petugas kasir yang memproses</p>
            </div>
            <a href="{{ route('pemilik.laporan.index') }}" class="text-xs font-bold text-[#1e3a2c] hover:text-[#0e1b15] inline-flex items-center gap-1">
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
                                {{ $t->tanggal_mulai?->format('d/m/Y') }} &ndash; {{ $t->tanggal_selesai?->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-4 font-black font-mono text-[#0e1b15]">
                                Rp{{ number_format($t->total_sewa, 0, ',', '.') }}
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

    <!-- Script Chart.js Pendapatan 6 Bulan -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('chartPendapatan6Bulan');
            if (ctx) {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($chartLabels) !!},
                        datasets: [{
                            label: 'Pendapatan Kas (Rp)',
                            data: {!! json_encode($chartData) !!},
                            backgroundColor: 'rgba(94, 221, 91, 0.75)',
                            borderColor: '#0e1b15',
                            borderWidth: 2,
                            borderRadius: 8,
                            hoverBackgroundColor: '#5edd5b',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#0e1b15',
                                titleFont: { size: 12, weight: 'bold' },
                                bodyFont: { size: 12 },
                                padding: 12,
                                cornerRadius: 8,
                                displayColors: false,
                                callbacks: {
                                    label: function(context) {
                                        return 'Kas Masuk: Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    font: { size: 10 },
                                    callback: function(value) {
                                        return 'Rp ' + (value >= 1000000 ? (value / 1000000).toFixed(1) + ' jt' : (value / 1000).toFixed(0) + ' rb');
                                    }
                                },
                                grid: { color: '#f1f5f9' }
                            },
                            x: {
                                ticks: { font: { size: 11, weight: 'bold' } },
                                grid: { display: false }
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-layouts.pemilik>
