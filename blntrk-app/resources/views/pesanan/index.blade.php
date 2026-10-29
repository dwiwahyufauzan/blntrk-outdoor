<x-layouts.publik>
    <x-slot:title>Riwayat Pesanan Saya - BLNTRK Outdoor</x-slot:title>

    <div class="bg-[#f4f5f6] min-h-screen py-10 sm:py-14">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-widest bg-[#0e1b15] text-[#5edd5b] mb-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#5edd5b]"></span>
                        Area Pelanggan
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-black text-[#0e1b15] font-urbanist tracking-tight">
                        Riwayat Pemesanan & Sewa
                    </h1>
                    <p class="text-xs sm:text-sm text-stone-500 mt-1">
                        Daftar seluruh reservasi dan peminjaman alat outdoor atas nama Anda
                    </p>
                </div>

                <a href="{{ route('pesanan.create') }}"
                   class="px-5 py-3 rounded-2xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-black transition shadow-sm inline-flex items-center justify-center gap-2 cursor-pointer font-urbanist shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>Buat Pesanan Baru</span>
                </a>
            </div>

            <!-- Filter Status Tab (Foresta Style) -->
            <div class="flex items-center gap-2 overflow-x-auto pb-3 mb-6 scrollbar-none">
                @php
                    $tabs = [
                        '' => 'Semua Pesanan',
                        'menunggu' => 'Menunggu',
                        'dibayar' => 'Dibayar',
                        'disewa' => 'Sedang Disewa',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                    ];
                    $currentStatus = request('status', '');
                @endphp
                @foreach($tabs as $val => $lbl)
                    <a href="{{ route('pesanan.index', array_filter(['status' => $val])) }}"
                       class="px-4 py-2 rounded-xl text-xs font-bold shrink-0 transition {{ $currentStatus === $val ? 'bg-[#0e1b15] text-[#5edd5b] shadow-xs' : 'bg-white text-stone-600 hover:bg-stone-50 border border-stone-200/90' }}">
                        {{ $lbl }}
                    </a>
                @endforeach
            </div>

            <!-- List Card Pesanan -->
            <div class="space-y-4">
                @forelse($pesananList as $pesanan)
                    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-stone-200/90 shadow-xs hover:border-[#5edd5b] hover:shadow-md transition">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-stone-100">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-[#0e1b15] text-[#5edd5b] flex items-center justify-center font-bold shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400 block">Nomor Resi</span>
                                    <span class="text-sm sm:text-base font-mono font-black text-[#0e1b15]">{{ $pesanan->no_resi }}</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                @php
                                    $badge = match($pesanan->status->value) {
                                        'menunggu' => 'bg-amber-100 text-amber-900 border border-amber-300 font-extrabold',
                                        'dibayar' => 'bg-[#5edd5b]/20 text-stone-900 border border-[#5edd5b]/40 font-extrabold',
                                        'disewa' => 'bg-sky-100 text-sky-900 border border-sky-300 font-extrabold',
                                        'selesai' => 'bg-emerald-100 text-emerald-900 border border-emerald-300 font-bold',
                                        'terlambat' => 'bg-rose-100 text-rose-900 border border-rose-300 font-extrabold animate-pulse',
                                        'dibatalkan' => 'bg-stone-100 text-stone-600 border border-stone-200 font-bold',
                                        default => 'bg-stone-100 text-stone-600 font-bold'
                                    };
                                @endphp
                                <span class="px-3 py-1 rounded-full text-[10px] uppercase tracking-wider {{ $badge }}">
                                    {{ $pesanan->status->label() }}
                                </span>

                                <a href="{{ route('pesanan.show', $pesanan->no_resi) }}"
                                   class="px-4 py-2 rounded-xl bg-stone-100 hover:bg-[#0e1b15] hover:text-[#5edd5b] text-stone-800 text-xs font-bold transition inline-flex items-center gap-1.5 shadow-2xs">
                                    <span>Lihat Resi</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            </div>
                        </div>

                        <!-- Info Jadwal & Item -->
                        <div class="pt-4 grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                            <div>
                                <span class="text-stone-400 text-[10px] uppercase tracking-wider block font-bold mb-1">Rentang Sewa</span>
                                <div class="font-bold text-stone-800">
                                    {{ $pesanan->tanggal_mulai->translatedFormat('d M') }} &ndash; {{ $pesanan->tanggal_selesai->translatedFormat('d M Y') }}
                                </div>
                                <span class="text-stone-400 text-[11px] font-medium">Durasi: {{ $pesanan->durasi_hari }} Hari</span>
                            </div>

                            <div>
                                <span class="text-stone-400 text-[10px] uppercase tracking-wider block font-bold mb-1">Perlengkapan</span>
                                <div class="font-medium text-stone-700 truncate">
                                    {{ $pesanan->detail->pluck('alat.nama')->join(', ') }}
                                </div>
                                <span class="text-stone-400 text-[11px] font-medium">{{ $pesanan->detail->sum('jumlah') }} Unit Alat</span>
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
                    <div class="bg-white rounded-3xl p-12 text-center border border-stone-200/90 shadow-xs">
                        <svg class="w-12 h-12 text-stone-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <h3 class="text-base font-bold text-stone-800 font-urbanist">Belum ada riwayat pemesanan</h3>
                        <p class="text-xs text-stone-500 mt-1 max-w-sm mx-auto">
                            Anda belum memiliki riwayat pemesanan alat outdoor. Pilih peralatan pendakian Anda sekarang!
                        </p>
                        <div class="mt-5">
                            <a href="{{ route('katalog.index') }}"
                               class="px-5 py-2.5 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-extrabold transition shadow-xs inline-flex items-center gap-2">
                                <span>Jelajahi Katalog Alat</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
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
