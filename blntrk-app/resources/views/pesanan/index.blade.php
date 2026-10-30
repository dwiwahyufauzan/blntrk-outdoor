<x-layouts.publik>
    <x-slot:title>{{ $tab === 'aktif' ? 'Pesanan Aktif' : 'Riwayat Pesanan' }} - BLNTRK Outdoor</x-slot:title>

    <div class="bg-[#f4f5f6] min-h-screen py-10 sm:py-14">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Alert Notifikasi Flash -->
            @if(session('status'))
                <div class="mb-6 p-4 rounded-2xl bg-[#0e1b15] text-[#5edd5b] text-xs font-bold border border-[#1e3a2c] shadow-xs flex items-center gap-3">
                    <svg class="w-5 h-5 text-[#5edd5b] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-widest bg-[#0e1b15] text-[#5edd5b] mb-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#5edd5b]"></span>
                        Area Pelanggan
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-black text-[#0e1b15] font-urbanist tracking-tight">
                        Pesanan & Penyewaan Saya
                    </h1>
                    <p class="text-xs sm:text-sm text-stone-500 mt-1">
                        Pantau pesanan yang sedang aktif dan telusuri riwayat sewa alat outdoor Anda
                    </p>
                </div>

                <a href="{{ route('pesanan.create') }}"
                   class="px-5 py-3 rounded-2xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-black transition shadow-sm inline-flex items-center justify-center gap-2 cursor-pointer font-urbanist shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>Buat Pesanan Baru</span>
                </a>
            </div>

            <!-- Tab Switcher Utama: Pesanan Aktif vs Riwayat Pesanan (Foresta Style) -->
            <div class="bg-white p-2 rounded-2xl border border-stone-200/90 shadow-2xs mb-6 grid grid-cols-2 gap-2">
                <!-- Tab: Pesanan Aktif -->
                <a href="{{ route('pesanan.index', ['tab' => 'aktif']) }}"
                   class="flex items-center justify-center gap-2.5 py-3 px-4 rounded-xl text-xs sm:text-sm font-extrabold transition {{ $tab === 'aktif' ? 'bg-[#0e1b15] text-[#5edd5b] shadow-xs' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-50' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <span>Pesanan Aktif</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $tab === 'aktif' ? 'bg-[#5edd5b] text-black' : 'bg-stone-100 text-stone-700' }}">
                        {{ $jumlahAktif }}
                    </span>
                </a>

                <!-- Tab: Riwayat Pesanan -->
                <a href="{{ route('pesanan.index', ['tab' => 'riwayat']) }}"
                   class="flex items-center justify-center gap-2.5 py-3 px-4 rounded-xl text-xs sm:text-sm font-extrabold transition {{ $tab === 'riwayat' ? 'bg-[#0e1b15] text-[#5edd5b] shadow-xs' : 'text-stone-600 hover:text-stone-900' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Riwayat Selesai</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $tab === 'riwayat' ? 'bg-[#5edd5b] text-black' : 'bg-stone-100 text-stone-700' }}">
                        {{ $jumlahRiwayat }}
                    </span>
                </a>
            </div>

            <!-- Sub-filter Status Pills Sesuai Tab Terpilih -->
            <div class="flex items-center gap-2 overflow-x-auto pb-3 mb-6 scrollbar-none">
                @php
                    $currentStatus = request('status', '');
                    if ($tab === 'aktif') {
                        $subFilters = [
                            '' => 'Semua Aktif (' . $jumlahAktif . ')',
                            'menunggu' => 'Menunggu Pembayaran (' . ($statusCounts['menunggu'] ?? 0) . ')',
                            'dibayar' => 'Siap Diambil (' . ($statusCounts['dibayar'] ?? 0) . ')',
                            'disewa' => 'Sedang Disewa (' . ($statusCounts['disewa'] ?? 0) . ')',
                            'terlambat' => 'Terlambat (' . ($statusCounts['terlambat'] ?? 0) . ')',
                        ];
                    } else {
                        $subFilters = [
                            '' => 'Semua Riwayat (' . $jumlahRiwayat . ')',
                            'dikembalikan' => 'Dikembalikan / Selesai (' . ($statusCounts['dikembalikan'] ?? 0) . ')',
                            'dibatalkan' => 'Dibatalkan (' . ($statusCounts['dibatalkan'] ?? 0) . ')',
                        ];
                    }
                @endphp

                @foreach($subFilters as $val => $lbl)
                    <a href="{{ route('pesanan.index', array_filter(['tab' => $tab, 'status' => $val])) }}"
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold shrink-0 transition {{ $currentStatus === $val ? 'bg-[#5edd5b] text-black shadow-2xs font-extrabold' : 'bg-white text-stone-600 hover:bg-stone-50 border border-stone-200/90' }}">
                        {{ $lbl }}
                    </a>
                @endforeach
            </div>

            <!-- List Kartu Pesanan -->
            <div class="space-y-4">
                @forelse($pesananList as $pesanan)
                    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-stone-200/90 shadow-xs hover:border-[#5edd5b] hover:shadow-md transition">
                        
                        <!-- Header Kartu -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-stone-100">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-[#0e1b15] text-[#5edd5b] flex items-center justify-center font-bold shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400 block">Nomor Resi</span>
                                    <span class="text-sm sm:text-base font-mono font-black text-[#0e1b15]">{{ $pesanan->no_resi }}</span>
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                @php
                                    $badge = match($pesanan->status->value) {
                                        'menunggu' => 'bg-amber-100 text-amber-900 border border-amber-300 font-extrabold',
                                        'dibayar' => 'bg-[#5edd5b]/20 text-stone-900 border border-[#5edd5b]/40 font-extrabold',
                                        'disewa' => 'bg-sky-100 text-sky-900 border border-sky-300 font-extrabold',
                                        'dikembalikan', 'selesai' => 'bg-emerald-100 text-emerald-900 border border-emerald-300 font-bold',
                                        'terlambat' => 'bg-rose-100 text-rose-900 border border-rose-300 font-extrabold animate-pulse',
                                        'dibatalkan' => 'bg-stone-100 text-stone-600 border border-stone-200 font-bold',
                                        default => 'bg-stone-100 text-stone-600 font-bold'
                                    };
                                @endphp
                                <span class="px-3 py-1 rounded-full text-[10px] uppercase tracking-wider {{ $badge }}">
                                    {{ $pesanan->status->label() }}
                                </span>

                                <!-- Tombol Unduh PDF Resi -->
                                <a href="{{ route('pesanan.pdf', $pesanan->no_resi) }}" title="Unduh PDF Resi"
                                   class="p-2 rounded-xl bg-stone-50 hover:bg-stone-100 text-stone-600 border border-stone-200 transition">
                                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </a>

                                <!-- Tombol Lihat Resi -->
                                <a href="{{ route('pesanan.resi', $pesanan->no_resi) }}"
                                   class="px-3.5 py-2 rounded-xl bg-white hover:bg-stone-50 text-stone-700 border border-stone-200 text-xs font-bold transition inline-flex items-center gap-1.5 shadow-2xs">
                                    <svg class="w-3.5 h-3.5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span>Lihat Resi</span>
                                </a>

                                <!-- Tombol Detail Pesanan -->
                                <a href="{{ route('pesanan.show', $pesanan->no_resi) }}"
                                   class="px-4 py-2 rounded-xl bg-[#0e1b15] hover:bg-[#1e3a2c] text-[#5edd5b] text-xs font-extrabold transition inline-flex items-center gap-1.5 shadow-2xs">
                                    <span>Detail Pesanan</span>
                                    <svg class="w-3.5 h-3.5 text-[#5edd5b]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            </div>
                        </div>

                        <!-- Pesan Status Kontekstual -->
                        @if($pesanan->status === \App\Enums\StatusPesanan::Menunggu)
                            <div class="mt-3 p-3 rounded-2xl bg-amber-50 border border-amber-200/80 text-[11px] text-amber-900 flex items-center justify-between gap-3">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    <span>Menunggu pembayaran di toko. Batas pengambilan: <strong>{{ $pesanan->batas_ambil?->translatedFormat('d M Y H:i') ?? 'Hari pertama sewa' }} WIB</strong>.</span>
                                </span>
                            </div>
                        @elseif($pesanan->status === \App\Enums\StatusPesanan::Dibayar)
                            <div class="mt-3 p-3 rounded-2xl bg-[#5edd5b]/15 border border-[#5edd5b]/30 text-[11px] text-[#0e1b15] flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-800 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Pembayaran lunas. Silakan bawa kartu identitas (KTP/SIM/Kartu Pelajar) ke toko saat mengambil peralatan.</span>
                            </div>
                        @elseif($pesanan->status === \App\Enums\StatusPesanan::Disewa)
                            <div class="mt-3 p-3 rounded-2xl bg-sky-50 border border-sky-200 text-[11px] text-sky-950 flex items-center gap-2">
                                <svg class="w-4 h-4 text-sky-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Unit sedang disewa. Batas pengembalian: <strong>{{ $pesanan->tanggal_selesai->translatedFormat('d M Y') }} pukul 12:00 WIB</strong>.</span>
                            </div>
                        @elseif($pesanan->status === \App\Enums\StatusPesanan::Terlambat)
                            <div class="mt-3 p-3 rounded-2xl bg-rose-50 border border-rose-200 text-[11px] text-rose-950 flex items-center gap-2">
                                <svg class="w-4 h-4 text-rose-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span>Melewati batas pengembalian. Harap segera kembalikan peralatan ke toko untuk menghindari akumulasi denda.</span>
                            </div>
                        @elseif($pesanan->status === \App\Enums\StatusPesanan::Dibatalkan)
                            <div class="mt-3 p-3 rounded-2xl bg-stone-50 border border-stone-200 text-[11px] text-stone-600 flex items-center gap-2">
                                <span class="font-bold">Alasan Batal:</span>
                                <span>{{ $pesanan->alasan_batal ?? 'Dibatalkan oleh pelanggan/sistem' }}</span>
                            </div>
                        @endif

                        <!-- Rincian Jadwal & Alat -->
                        <div class="pt-4 grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                            <div>
                                <span class="text-stone-400 text-[10px] uppercase tracking-wider block font-bold mb-1">Rentang Sewa</span>
                                <div class="font-bold text-stone-800">
                                    {{ $pesanan->tanggal_mulai->translatedFormat('d M') }} &ndash; {{ $pesanan->tanggal_selesai->translatedFormat('d M Y') }}
                                </div>
                                <span class="text-stone-400 text-[11px] font-medium">Durasi: {{ $pesanan->durasi_hari }} Hari (x24 Jam)</span>
                            </div>

                            <div>
                                <span class="text-stone-400 text-[10px] uppercase tracking-wider block font-bold mb-1">Perlengkapan Outdoor</span>
                                <div class="font-medium text-stone-700 truncate">
                                    {{ $pesanan->detail->pluck('alat.nama')->join(', ') }}
                                </div>
                                <span class="text-stone-400 text-[11px] font-medium">{{ $pesanan->detail->sum('jumlah') }} Unit Perlengkapan</span>
                            </div>

                            <div class="sm:text-right">
                                <span class="text-stone-400 text-[10px] uppercase tracking-wider block font-bold mb-1">Total Biaya Sewa</span>
                                <div class="text-base sm:text-lg font-black font-urbanist text-[#0e1b15]">
                                    Rp{{ number_format($pesanan->total_sewa, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- Empty State -->
                    <div class="bg-white rounded-3xl p-12 text-center border border-stone-200/90 shadow-xs">
                        <div class="w-16 h-16 rounded-3xl bg-stone-100 text-stone-400 flex items-center justify-center mx-auto mb-4">
                            @if($tab === 'aktif')
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            @else
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @endif
                        </div>

                        @if($tab === 'aktif')
                            @if($jumlahAktif === 0 && $jumlahRiwayat === 0)
                                <h3 class="text-base font-bold text-stone-800 font-urbanist">Belum ada riwayat pemesanan</h3>
                                <p class="text-xs text-stone-500 mt-1 max-w-sm mx-auto">
                                    Anda belum memiliki riwayat pemesanan alat outdoor. Pilih perlengkapan pendakian Anda sekarang!
                                </p>
                            @else
                                <h3 class="text-base font-bold text-stone-800 font-urbanist">Tidak Ada Pesanan Aktif Saat Ini</h3>
                                <p class="text-xs text-stone-500 mt-1 max-w-sm mx-auto">
                                    Anda tidak memiliki pesanan yang sedang berjalan. Rencanakan pendakian atau camping Anda sekarang!
                                </p>
                            @endif
                            <div class="mt-5 flex flex-wrap items-center justify-center gap-3">
                                <a href="{{ route('katalog.index') }}"
                                   class="px-5 py-2.5 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-extrabold transition shadow-xs inline-flex items-center gap-2">
                                    <span>Jelajahi Katalog Alat</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                                @if($jumlahRiwayat > 0)
                                    <a href="{{ route('pesanan.index', ['tab' => 'riwayat']) }}"
                                       class="px-5 py-2.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold transition inline-flex items-center gap-2">
                                        <span>Lihat Riwayat Selesai ({{ $jumlahRiwayat }})</span>
                                    </a>
                                @endif
                            </div>
                        @else
                            <h3 class="text-base font-bold text-stone-800 font-urbanist">Belum Ada Riwayat Pesanan</h3>
                            <p class="text-xs text-stone-500 mt-1 max-w-sm mx-auto">
                                Belum ada riwayat pesanan yang telah dikembalikan atau dibatalkan.
                            </p>
                            @if($jumlahAktif > 0)
                                <div class="mt-5">
                                    <a href="{{ route('pesanan.index', ['tab' => 'aktif']) }}"
                                       class="px-5 py-2.5 rounded-xl bg-[#0e1b15] hover:bg-[#1e3a2c] text-[#5edd5b] text-xs font-extrabold transition shadow-xs inline-flex items-center gap-2">
                                        <span>Lihat Pesanan Aktif Anda ({{ $jumlahAktif }})</span>
                                    </a>
                                </div>
                            @endif
                        @endif
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($pesananList->hasPages())
                <div class="mt-6">
                    {{ $pesananList->links() }}
                </div>
            @endif

        </div>
    </div>
</x-layouts.publik>
