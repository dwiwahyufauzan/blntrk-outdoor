<x-layouts.admin>
    <x-slot:title>Dashboard Operasional Toko</x-slot:title>

    <div class="mb-7">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#4e7a5a] block mb-1">Pusat Kendali Operasional</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-[#14201a] tracking-tight bl-d">Dashboard Operasional Toko</h1>
                <p class="text-xs text-stone-500 mt-0.5">Status transaksi rental harian, serah terima alat, jaminan fisik, dan pengembalian</p>
            </div>

            <!-- Tombol Aksi Cepat Admin -->
            <div class="flex items-center gap-2.5">
                <a href="{{ route('admin.pesanan.create') }}" class="px-4 py-2.5 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-extrabold shadow-sm transition inline-flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>Sewa Walk-in Baru</span>
                </a>
                <a href="{{ route('admin.laporan.index') }}" class="px-4 py-2.5 rounded-xl bg-white hover:bg-stone-50 border border-stone-200 text-[#14201a] text-xs font-bold shadow-2xs transition inline-flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4 text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Laporan Usaha</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Grid 5 Kartu Metrik Operasional Utama (T09) -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5 mb-8">
        <!-- 1. Menunggu Konfirmasi / Bayar -->
        <div class="bg-white p-4.5 rounded-2xl border border-stone-200/90 shadow-xs flex flex-col justify-between hover:border-amber-300 transition">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700">Menunggu Bayar</span>
                <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div>
                <span class="text-2xl font-black text-[#14201a] block">{{ $statistik['pesanan_menunggu'] }}</span>
                <span class="text-[11px] text-stone-400 block mt-0.5 font-medium">reservasi aktif</span>
            </div>
        </div>

        <!-- 2. Perlu Diambil Hari Ini -->
        <div class="bg-white p-4.5 rounded-2xl border border-stone-200/90 shadow-xs flex flex-col justify-between hover:border-emerald-300 transition">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Ambil Hari Ini</span>
                <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <div>
                <span class="text-2xl font-black text-[#14201a] block">{{ $statistik['perlu_diambil_hari_ini'] }}</span>
                <span class="text-[11px] text-stone-400 block mt-0.5 font-medium">siap serah terima</span>
            </div>
        </div>

        <!-- 3. Sedang Disewa -->
        <div class="bg-white p-4.5 rounded-2xl border border-stone-200/90 shadow-xs flex flex-col justify-between hover:border-blue-300 transition">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-700">Sedang Disewa</span>
                <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 3l4 8 5-5 5 15H2L8 3z"/></svg>
                </div>
            </div>
            <div>
                <span class="text-2xl font-black text-[#14201a] block">{{ $statistik['pesanan_disewa'] }}</span>
                <span class="text-[11px] text-stone-400 block mt-0.5 font-medium">di tangan penyewa</span>
            </div>
        </div>

        <!-- 4. Terlambat Kembali -->
        <div class="bg-white p-4.5 rounded-2xl border {{ $statistik['pesanan_terlambat'] > 0 ? 'border-rose-300 bg-rose-50/20' : 'border-stone-200/90' }} shadow-xs flex flex-col justify-between hover:border-rose-400 transition">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-rose-700">Terlambat</span>
                <div class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <div>
                <span class="text-2xl font-black {{ $statistik['pesanan_terlambat'] > 0 ? 'text-rose-700' : 'text-[#14201a]' }} block">
                    {{ $statistik['pesanan_terlambat'] }}
                </span>
                <span class="text-[11px] text-stone-400 block mt-0.5 font-medium">lewat batas 12:00</span>
            </div>
        </div>

        <!-- 5. Denda Belum Lunas -->
        <div class="bg-white p-4.5 rounded-2xl border border-stone-200/90 shadow-xs flex flex-col justify-between hover:border-stone-300 transition col-span-2 sm:col-span-1">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-stone-500">Denda Tertunggak</span>
                <div class="w-7 h-7 rounded-lg bg-stone-100 text-stone-600 flex items-center justify-center font-bold text-xs">
                    Rp
                </div>
            </div>
            <div>
                <span class="text-lg font-black text-[#14201a] font-mono block">
                    Rp{{ number_format($statistik['denda_belum_lunas'], 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-stone-400 block mt-0.5 font-medium">terlambat/rusak</span>
            </div>
        </div>
    </div>

    <!-- Section Perlu Tindakan & Grafik Tren Sewa (Grid 2 Kolom) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8">
        
        <!-- Kolom Kiri: Panel "Perlu Tindakan Segera" (7 Kolom) -->
        <div class="lg:col-span-7 space-y-4">
            <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs p-5">
                <div class="flex items-center justify-between pb-3 border-b border-stone-100 mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-ping"></div>
                        <h2 class="text-sm font-extrabold text-[#14201a] uppercase tracking-wider">Perlu Tindakan Operasional</h2>
                    </div>
                    <span class="text-[11px] text-stone-400">Prioritas hari ini</span>
                </div>

                <!-- Bagian 1: Pesanan Terlambat -->
                <div class="mb-5">
                    <div class="flex items-center justify-between text-xs font-bold text-rose-800 mb-2">
                        <span>Penyewa Terlambat Pengembalian ({{ $pesananTerlambat->count() }})</span>
                        @if($pesananTerlambat->isNotEmpty())
                            <span class="text-[10px] text-rose-600 font-normal">Hubungi penyewa segera</span>
                        @endif
                    </div>
                    <div class="space-y-2">
                        @forelse($pesananTerlambat as $pt)
                            <div class="p-3 rounded-xl bg-rose-50/60 border border-rose-200/80 flex items-center justify-between gap-3 text-xs">
                                <div>
                                    <div class="font-bold text-stone-900 font-mono">{{ $pt->no_resi }} &bull; {{ $pt->pelanggan->nama_lengkap }}</div>
                                    <span class="text-[11px] text-rose-800 block">
                                        Batas kembali: {{ $pt->tanggal_selesai->format('d/m/Y') }} 12:00 WIB
                                    </span>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <a href="{{ route('admin.pesanan.show', $pt->id) }}"
                                       class="px-2.5 py-1.5 rounded-lg bg-white hover:bg-stone-50 border border-rose-200 text-rose-800 font-bold text-[11px] transition">
                                        Periksa
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="p-3 text-center rounded-xl bg-stone-50 text-stone-400 text-xs">
                                Tidak ada pengembalian alat yang terlambat saat ini.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Bagian 2: Batas Ambil Hari Ini -->
                <div>
                    <div class="flex items-center justify-between text-xs font-bold text-emerald-800 mb-2">
                        <span>Jadwal Pengambilan Hari Ini ({{ $pesananAmbilHariIni->count() }})</span>
                        <span class="text-[10px] text-stone-400 font-normal">Siapkan perlengkapan</span>
                    </div>
                    <div class="space-y-2">
                        @forelse($pesananAmbilHariIni as $pa)
                            <div class="p-3 rounded-xl bg-emerald-50/40 border border-emerald-200/80 flex items-center justify-between gap-3 text-xs">
                                <div>
                                    <div class="font-bold text-stone-900 font-mono">{{ $pa->no_resi }} &bull; {{ $pa->pelanggan->nama_lengkap }}</div>
                                    <span class="text-[11px] text-stone-500 block">
                                        {{ $pa->detail->sum('jumlah') }} unit alat &bull; Status: Lunas (Menunggu Serah Terima)
                                    </span>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <a href="{{ route('admin.pesanan.show', $pa->id) }}"
                                       class="px-2.5 py-1.5 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black font-extrabold text-[11px] transition">
                                        Serahkan
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="p-3 text-center rounded-xl bg-stone-50 text-stone-400 text-xs">
                                Tidak ada jadwal pengambilan baru hari ini.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Grafik Tren Penyewaan 7 Hari (5 Kolom) -->
        <div class="lg:col-span-5">
            <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs p-5 h-full flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-stone-100 mb-3">
                        <div>
                            <h2 class="text-sm font-extrabold text-[#14201a]">Tren Penyewaan 7 Hari</h2>
                            <p class="text-[11px] text-stone-400">Volume transaksi sewa harian berjalan</p>
                        </div>
                        <div class="w-8 h-8 rounded-xl bg-[#5edd5b]/20 text-stone-900 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4 text-emerald-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </div>
                    </div>

                    <div class="relative w-full aspect-4/3 sm:aspect-16/9 lg:aspect-auto h-52 sm:h-56">
                        <canvas id="chartSewa7Hari"></canvas>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-stone-100 flex items-center justify-between text-[11px] text-stone-500">
                    <span>Total sewa 7 hari: <strong>{{ array_sum($chartData) }} pesanan</strong></span>
                    <a href="{{ route('admin.laporan.index', ['jenis' => 'penyewaan']) }}" class="text-[#0e1b15] font-bold hover:underline">
                        Lihat Laporan Lengkap &rarr;
                    </a>
                </div>
            </div>
        </div>

    </div>

    <!-- Tabel Pesanan Terbaru -->
    <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-stone-200/80 flex items-center justify-between bg-stone-50/50">
            <div>
                <h2 class="text-sm font-extrabold text-[#14201a]">Aktivitas Pesanan Terbaru</h2>
                <p class="text-xs text-stone-500">6 transaksi penyewaan terakhir yang dicatat ke sistem</p>
            </div>
            <a href="{{ route('admin.pesanan.index') }}" class="text-xs font-bold text-[#0e1b15] hover:text-[#5edd5b] flex items-center gap-1.5 transition">
                <span>Lihat Semua Pesanan</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
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
                                    <span class="ml-1.5 px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-[#5edd5b]/20 text-emerald-900 border border-[#5edd5b]/40">Walk-in</span>
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
                                    {{ $p->durasi_hari }} hari sewa
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-extrabold text-[#14201a] text-sm font-mono">
                                Rp{{ number_format($p->total_sewa, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4">
                                <x-badge-status :status="$p->status" />
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                <a href="{{ route('admin.pesanan.show', $p->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-stone-100 hover:bg-[#5edd5b] hover:text-black text-stone-700 font-bold text-[11px] transition">
                                    <span>Detail</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-stone-400">
                                Belum ada riwayat pesanan yang tercatat di database.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Script Inisialisasi Chart.js untuk Tren Sewa 7 Hari -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('chartSewa7Hari');
            if (ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($chartLabels) !!},
                        datasets: [{
                            label: 'Jumlah Pesanan',
                            data: {!! json_encode($chartData) !!},
                            borderColor: '#0e1b15',
                            backgroundColor: 'rgba(94, 221, 91, 0.25)',
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.35,
                            pointBackgroundColor: '#5edd5b',
                            pointBorderColor: '#0e1b15',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#0e1b15',
                                titleFont: { size: 11, weight: 'bold' },
                                bodyFont: { size: 11 },
                                padding: 10,
                                cornerRadius: 8,
                                displayColors: false,
                                callbacks: {
                                    label: function(context) {
                                        return context.parsed.y + ' Pesanan Sewa';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                    font: { size: 10 }
                                },
                                grid: {
                                    color: '#f1f5f9'
                                }
                            },
                            x: {
                                ticks: { font: { size: 10 } },
                                grid: { display: false }
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-layouts.admin>
