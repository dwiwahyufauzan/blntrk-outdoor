<x-layouts.publik>
    <x-slot:title>Resi Pesanan #{{ $pesanan->no_resi }} - BLNTRK Outdoor</x-slot:title>

    <div x-data="{ showModalBatal: false }" class="bg-[#f4f5f6] min-h-screen py-10 sm:py-14 print:bg-white print:py-0">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 print:p-0">

            <!-- Tombol Navigasi Atas (Sembunyi saat cetak) -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden">
                <a href="{{ route('pesanan.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-stone-500 hover:text-[#0e1b15] transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Ke Riwayat Pesanan Saya</span>
                </a>

                <div class="flex items-center gap-2">
                    <a href="{{ route('pesanan.pdf', $pesanan->no_resi) }}"
                       class="px-3.5 py-2 rounded-xl bg-white hover:bg-stone-100 text-stone-700 border border-stone-200 text-xs font-bold transition inline-flex items-center gap-1.5 shadow-2xs">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Unduh PDF Resi</span>
                    </a>

                    <button type="button" onclick="window.print()"
                            class="px-3.5 py-2 rounded-xl bg-white hover:bg-stone-100 text-stone-700 border border-stone-200 text-xs font-bold transition inline-flex items-center gap-1.5 shadow-2xs cursor-pointer">
                        <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Bukti Resi</span>
                    </button>

                    @php
                        $waToko = preg_replace('/\D/', '', \App\Models\Pengaturan::ambil('no_wa_toko', '6281234567890'));
                        $pesanWa = rawurlencode("Halo BLNTRK Outdoor, saya ingin konfirmasi pesanan sewa dengan nomor resi " . $pesanan->no_resi . " atas nama " . $pesanan->pelanggan->nama_lengkap . ".");
                    @endphp
                    <a href="https://wa.me/{{ $waToko }}?text={{ $pesanWa }}" target="_blank"
                       class="px-4 py-2 rounded-xl bg-[#0e1b15] hover:bg-[#1e3a2c] text-[#5edd5b] text-xs font-extrabold transition inline-flex items-center gap-1.5 shadow-xs">
                        <svg class="w-4 h-4 text-[#5edd5b]" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.173.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.159.57 4.184 1.564 5.938l-1.564 5.714 5.861-1.537c1.7 1.006 3.684 1.585 5.807 1.585 6.627 0 12-5.373 12-12 0-6.627-5.373-12-12-12z"/></svg>
                        <span>Konfirmasi WhatsApp</span>
                    </a>
                </div>
            </div>

            @if(session('status'))
                <div class="mb-6 p-4 rounded-2xl bg-[#0e1b15] text-[#5edd5b] text-xs font-bold border border-[#1e3a2c] shadow-xs flex items-center gap-3">
                    <svg class="w-5 h-5 text-[#5edd5b] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- LEMBAR RESI PESANAN RESMI -->
            <div class="bg-white rounded-3xl border border-stone-200/90 shadow-md overflow-hidden print:border-none print:shadow-none">
                <!-- Header Resi -->
                <div class="p-6 sm:p-8 bg-[#0e1b15] text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-[#14251e] border border-[#1e3a2c] flex items-center justify-center text-[#5edd5b] shadow-inner shrink-0">
                            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M14 6l4 8h-3l2 4H7l4-7 2 3 1-2v-6zm0-4L9 11l-3-5L1 18h22L14 2z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="text-[10px] font-extrabold tracking-widest uppercase text-[#5edd5b] block">Bukti Reservasi Sewa</span>
                            <h2 class="text-xl sm:text-2xl font-black font-urbanist tracking-tight">BLNTRK Outdoor Subang</h2>
                            <span class="text-xs text-stone-400 font-mono">Buka 24 Jam • Jl. Raya Jalancagak, Subang</span>
                        </div>
                    </div>

                    <!-- Nomor Resi & Badge Status -->
                    <div class="text-left sm:text-right">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400 block mb-0.5">Nomor Resi</span>
                        <div class="text-lg sm:text-xl font-mono font-black text-white tracking-wider">
                            {{ $pesanan->no_resi }}
                        </div>
                        <div class="mt-1.5 inline-block">
                            @php
                                $statusStyle = match($pesanan->status->value) {
                                    'menunggu' => 'bg-amber-400 text-stone-950 font-black',
                                    'dibayar' => 'bg-[#5edd5b] text-stone-950 font-black',
                                    'disewa' => 'bg-sky-400 text-stone-950 font-black',
                                    'dikembalikan', 'selesai' => 'bg-emerald-500 text-white font-bold',
                                    'terlambat' => 'bg-rose-500 text-white font-black animate-pulse',
                                    'dibatalkan' => 'bg-stone-200 text-stone-700 font-bold',
                                    default => 'bg-stone-200 text-stone-700 font-bold'
                                };
                            @endphp
                            <span class="px-3 py-1 rounded-full text-[10px] uppercase tracking-wider {{ $statusStyle }}">
                                {{ $pesanan->status->label() }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Informasi Pelanggan & Jadwal Sewa -->
                <div class="p-6 sm:p-8 border-b border-stone-100 bg-stone-50/50">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
                        <!-- Data Pemesan -->
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-stone-400 block mb-2">Penyewa</span>
                            <div class="font-bold text-stone-900 text-sm font-urbanist">{{ $pesanan->pelanggan->nama_lengkap }}</div>
                            <div class="text-stone-600 font-mono mt-0.5">{{ $pesanan->pelanggan->no_hp }}</div>
                            <div class="text-stone-500 mt-0.5 leading-relaxed">{{ $pesanan->pelanggan->alamat }}</div>
                        </div>

                        <!-- Jadwal Sewa -->
                        <div class="space-y-1.5">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-stone-400 block mb-2">Jadwal & Waktu Sewa</span>
                            <div class="flex justify-between items-center py-1 border-b border-stone-200/60">
                                <span class="text-stone-500">Mulai Pengambilan:</span>
                                <span class="font-bold text-stone-900">{{ $pesanan->tanggal_mulai->translatedFormat('d F Y') }}</span>
                            </div>
                            <div class="flex justify-between items-center py-1 border-b border-stone-200/60">
                                <span class="text-stone-500">Batas Pengembalian:</span>
                                <span class="font-bold text-stone-900">{{ $pesanan->tanggal_selesai->translatedFormat('d F Y') }} (12:00 WIB)</span>
                            </div>
                            <div class="flex justify-between items-center py-1">
                                <span class="text-stone-500">Durasi Sewa:</span>
                                <span class="font-extrabold text-[#0e1b15]">{{ $pesanan->durasi_hari }} Hari (x24 Jam)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Banner Informasi Status Terkini -->
                @if($pesanan->status === \App\Enums\StatusPesanan::Dibayar)
                    <div class="p-4 sm:p-5 bg-[#5edd5b]/15 border-b border-[#5edd5b]/30 text-xs text-[#0e1b15] flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-800 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <span class="font-extrabold block">Pembayaran Telah Dikonfirmasi Lunas</span>
                            <span class="text-[11px] text-stone-700">Silakan bawa kartu identitas fisik (KTP/SIM/Kartu Pelajar) asli ke toko saat mengambil peralatan.</span>
                        </div>
                    </div>
                @elseif($pesanan->status === \App\Enums\StatusPesanan::Disewa)
                    <div class="p-4 sm:p-5 bg-sky-50 border-b border-sky-200 text-xs text-sky-950 flex items-center gap-3">
                        <svg class="w-5 h-5 text-sky-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <span class="font-extrabold block">Peralatan Sedang Disewa</span>
                            <span class="text-[11px] text-sky-800">Unit telah diserahterimakan pada {{ $pesanan->diambil_pada?->format('d/m/Y H:i') }} WIB. Batas kembali: {{ $pesanan->tanggal_selesai->format('d/m/Y') }} pukul 12:00 WIB.</span>
                        </div>
                    </div>
                @elseif($pesanan->status === \App\Enums\StatusPesanan::Dibatalkan)
                    <div class="p-4 sm:p-5 bg-rose-50 border-b border-rose-200 text-xs text-rose-950 flex items-center gap-3">
                        <svg class="w-5 h-5 text-rose-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <span class="font-extrabold block">Pesanan Dibatalkan</span>
                            <span class="text-[11px] text-rose-800">Alasan pembatalan: "{{ $pesanan->alasan_batal }}"</span>
                        </div>
                    </div>
                @endif

                <!-- Tabel Detail Alat yang Disewa -->
                <div class="p-6 sm:p-8">
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-stone-400 block mb-3">Rincian Perlengkapan</span>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="text-[10px] uppercase tracking-wider text-stone-400 font-extrabold border-b border-stone-200 pb-2">
                                <tr>
                                    <th class="py-2.5">Perlengkapan Outdoor</th>
                                    <th class="py-2.5 text-center">Unit</th>
                                    <th class="py-2.5 text-right">Tarif / Hari</th>
                                    <th class="py-2.5 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-100">
                                @foreach($pesanan->detail as $item)
                                    <tr>
                                        <td class="py-3.5 pr-4">
                                            <div class="font-bold text-stone-900 text-sm font-urbanist">{{ $item->alat->nama }}</div>
                                            <span class="text-[10px] text-stone-400 font-mono">{{ $item->alat->kode_alat }} &bull; {{ $item->alat->kategori?->nama }}</span>
                                        </td>
                                        <td class="py-3.5 text-center font-bold text-stone-800">
                                            {{ $item->jumlah }}
                                        </td>
                                        <td class="py-3.5 text-right font-mono text-stone-600">
                                            Rp{{ number_format($item->harga_per_hari, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3.5 text-right font-extrabold text-stone-900 font-mono">
                                            Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-t-2 border-stone-200">
                                <tr>
                                    <td colspan="3" class="pt-4 text-right font-bold text-stone-500 uppercase tracking-wider text-[11px]">
                                        Total Biaya Sewa ({{ $pesanan->durasi_hari }} Hari)
                                    </td>
                                    <td class="pt-4 text-right font-black text-lg sm:text-xl font-urbanist text-[#0e1b15]">
                                        Rp{{ number_format($pesanan->total_sewa, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if($pesanan->catatan)
                        <div class="mt-4 p-3 rounded-xl bg-stone-50 border border-stone-200 text-xs text-stone-600">
                            <strong>Catatan:</strong> {{ $pesanan->catatan }}
                        </div>
                    @endif
                </div>

                <!-- Informasi Pembayaran, Jaminan, Pengembalian, & Denda (F07) -->
                @if($pesanan->pembayaran || $pesanan->jaminan || $pesanan->pengembalian || $pesanan->denda->isNotEmpty())
                    <div class="p-6 sm:p-8 bg-stone-50/70 border-t border-stone-200/80 space-y-5">
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-stone-400 block mb-1">Status Transaksi & Rekam Jejak Fisik</span>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <!-- Kartu Pembayaran -->
                            @if($pesanan->pembayaran)
                                <div class="bg-white rounded-2xl p-4 border border-stone-200/90 shadow-2xs">
                                    <div class="flex items-center justify-between pb-2 mb-2 border-b border-stone-100">
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-lg bg-[#5edd5b]/20 text-stone-900 flex items-center justify-center font-bold">
                                                <svg class="w-3.5 h-3.5 text-stone-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                            </div>
                                            <span class="font-extrabold text-stone-900">Pembayaran Sewa</span>
                                        </div>
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-[#5edd5b]/20 text-emerald-900 border border-[#5edd5b]/40">
                                            LUNAS
                                        </span>
                                    </div>
                                    <div class="space-y-1 text-stone-600">
                                        <div class="flex justify-between">
                                            <span class="text-stone-400">Nominal:</span>
                                            <span class="font-bold text-stone-900 font-mono">Rp{{ number_format($pesanan->pembayaran->jumlah, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-stone-400">Metode:</span>
                                            <span class="font-semibold text-stone-800">{{ $pesanan->pembayaran->metode->label() }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-stone-400">Waktu Bayar:</span>
                                            <span class="font-mono text-stone-700">{{ $pesanan->pembayaran->dibayar_pada?->format('d/m/Y H:i') }} WIB</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-stone-400">Petugas Kasir:</span>
                                            <span class="text-stone-700">{{ $pesanan->pembayaran->pencatat?->nama ?? 'Admin Toko' }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Kartu Jaminan Identitas -->
                            @if($pesanan->jaminan)
                                <div class="bg-white rounded-2xl p-4 border border-stone-200/90 shadow-2xs">
                                    <div class="flex items-center justify-between pb-2 mb-2 border-b border-stone-100">
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-lg bg-sky-100 text-sky-800 flex items-center justify-center font-bold">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                                            </div>
                                            <span class="font-extrabold text-stone-900">Jaminan Identitas</span>
                                        </div>
                                        @if($pesanan->jaminan->dikembalikan_pada)
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                DIKEMBALIKAN
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-amber-100 text-amber-900 border border-amber-300">
                                                DITAHAN DI TOKO
                                            </span>
                                        @endif
                                    </div>
                                    <div class="space-y-1 text-stone-600">
                                        <div class="flex justify-between">
                                            <span class="text-stone-400">Jenis Kartu:</span>
                                            <span class="font-bold text-stone-900">{{ is_string($pesanan->jaminan->jenis) ? strtoupper($pesanan->jaminan->jenis) : $pesanan->jaminan->jenis->label() }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-stone-400">No. Identitas:</span>
                                            <span class="font-mono text-stone-800">{{ $pesanan->jaminan->nomor_identitas_samar ?? $pesanan->jaminan->nomor_identitas }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-stone-400">Status Jaminan:</span>
                                            <span class="font-semibold {{ $pesanan->jaminan->dikembalikan_pada ? 'text-emerald-700' : 'text-amber-800' }}">
                                                {{ $pesanan->jaminan->dikembalikan_pada ? 'Dikembalikan pada ' . $pesanan->jaminan->dikembalikan_pada->format('d/m/Y H:i') : 'Disimpan di kasir' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Rekam Pengembalian Alat & Kondisi Fisik -->
                        @if($pesanan->pengembalian)
                            <div class="bg-white rounded-2xl p-4 border border-stone-200/90 shadow-2xs">
                                <div class="flex items-center justify-between pb-2 mb-2 border-b border-stone-100">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                        </div>
                                        <span class="font-extrabold text-stone-900">Rekam Pengembalian & Kondisi Peralatan</span>
                                    </div>
                                    <span class="text-[11px] font-mono text-stone-500">
                                        {{ $pesanan->pengembalian->dikembalikan_pada?->format('d/m/Y H:i') }} WIB
                                    </span>
                                </div>

                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-xs">
                                        <thead class="text-[9px] uppercase tracking-wider text-stone-400 font-bold border-b border-stone-100">
                                            <tr>
                                                <th class="py-1.5">Nama Peralatan</th>
                                                <th class="py-1.5 text-center">Unit Kembali</th>
                                                <th class="py-1.5 text-center">Baik</th>
                                                <th class="py-1.5 text-center">Rusak Ringan</th>
                                                <th class="py-1.5 text-center">Rusak Berat</th>
                                                <th class="py-1.5 text-center">Hilang</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-stone-50">
                                            @foreach($pesanan->pengembalian->detail as $pd)
                                                <tr>
                                                    <td class="py-2 font-semibold text-stone-800">{{ $pd->pesananDetail?->alat?->nama ?? 'Alat' }}</td>
                                                    <td class="py-2 text-center font-bold text-stone-900">{{ ($pd->jml_baik ?? 0) + ($pd->jml_rusak_ringan ?? 0) + ($pd->jml_rusak_berat ?? 0) + ($pd->jml_hilang ?? 0) }}</td>
                                                    <td class="py-2 text-center text-emerald-700 font-bold">{{ $pd->jml_baik ?? 0 }}</td>
                                                    <td class="py-2 text-center text-amber-700 font-bold">{{ $pd->jml_rusak_ringan ?? 0 }}</td>
                                                    <td class="py-2 text-center text-rose-700 font-bold">{{ $pd->jml_rusak_berat ?? 0 }}</td>
                                                    <td class="py-2 text-center text-rose-900 font-black">{{ $pd->jml_hilang ?? 0 }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        <!-- Rincian Denda & Ganti Rugi -->
                        @if($pesanan->denda->isNotEmpty())
                            <div class="bg-rose-50/50 rounded-2xl p-4 border border-rose-200/80 shadow-2xs">
                                <div class="flex items-center justify-between pb-2 mb-2 border-b border-rose-200/60">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-lg bg-rose-100 text-rose-800 flex items-center justify-center font-bold">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        </div>
                                        <span class="font-extrabold text-rose-950">Rincian Denda & Ganti Rugi</span>
                                    </div>
                                    @php
                                        $semuaLunas = $pesanan->denda->every(fn($d) => $d->status_bayar === 'lunas');
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider {{ $semuaLunas ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-rose-200 text-rose-900 border border-rose-400' }}">
                                        {{ $semuaLunas ? 'Denda Lunas' : 'Belum Lunas' }}
                                    </span>
                                </div>

                                <div class="space-y-2">
                                    @foreach($pesanan->denda as $d)
                                        <div class="flex items-center justify-between py-1.5 border-b border-rose-100 last:border-none text-xs">
                                            <div>
                                                <span class="font-bold text-stone-900 block">
                                                    {{ $d->jenis === \App\Enums\JenisDenda::Keterlambatan ? 'Denda Keterlambatan' : 'Ganti Rugi Kerusakan/Kehilangan' }}
                                                </span>
                                                <span class="text-[11px] text-stone-500">{{ $d->keterangan }}</span>
                                            </div>
                                            <div class="text-right">
                                                <span class="font-mono font-black text-rose-700 block">Rp{{ number_format($d->jumlah, 0, ',', '.') }}</span>
                                                <span class="text-[10px] uppercase font-bold {{ $d->status_bayar === 'lunas' ? 'text-emerald-700' : 'text-rose-700' }}">
                                                    {{ $d->status_bayar === 'lunas' ? 'Lunas pada ' . $d->dilunasi_pada?->format('d/m/Y') : 'Belum Dilunasi' }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Ketentuan Pengambilan & Syarat -->
                <div class="p-6 sm:p-8 bg-stone-50/80 border-t border-stone-200/80 text-xs text-stone-600 space-y-2">
                    <h4 class="font-extrabold text-stone-900 uppercase tracking-wider text-[11px]">Petunjuk & Syarat Pengambilan:</h4>
                    <ol class="list-decimal list-inside space-y-1 pl-1">
                        <li>Tunjukkan <strong>Nomor Resi ({{ $pesanan->no_resi }})</strong> ini kepada kasir/staf saat mengambil alat.</li>
                        <li>Wajib meninggalkan <strong>Kartu Identitas Asli (KTP / SIM / Kartu Pelajar)</strong> yang masih berlaku sebagai jaminan penyewaan.</li>
                        <li>Pembayaran sewa dapat dilunasi tunai atau melalui transfer/QRIS di kasir toko saat penyerahan unit.</li>
                        <li>Alat wajib dikembalikan maksimal pada tanggal <strong>{{ $pesanan->tanggal_selesai->translatedFormat('d M Y') }}</strong> pukul <strong>12:00 WIB</strong> untuk menghindari denda keterlambatan.</li>
                    </ol>
                </div>

                <!-- Footer Aksi Pembatalan (Jika Masih Menunggu) -->
                @can('batal', $pesanan)
                    <div class="p-6 sm:p-8 border-t border-stone-100 flex items-center justify-between gap-4 print:hidden">
                        <div class="text-xs text-stone-500">
                            Ingin membatalkan jadwal penyewaan ini?
                        </div>
                        <button type="button" @click="showModalBatal = true"
                                class="px-4 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold transition border border-rose-200 cursor-pointer">
                            Batalkan Pesanan
                        </button>
                    </div>
                @else
                    @if($pesanan->status === \App\Enums\StatusPesanan::Dibayar)
                        <div class="p-4 sm:p-5 border-t border-stone-100 bg-stone-50/40 text-xs text-stone-500 flex items-center justify-between gap-3 print:hidden">
                            <span>Pesanan telah lunas. Untuk pembatalan atau perubahan tanggal, hubungi admin toko via WhatsApp.</span>
                            <a href="https://wa.me/{{ $waToko }}?text={{ $pesanWa }}" target="_blank"
                               class="text-xs font-bold text-emerald-700 hover:underline shrink-0">Hubungi Toko &rarr;</a>
                        </div>
                    @endif
                @endcan
            </div>
        </div>

        <!-- =================== MODAL: BATALKAN OLEH PELANGGAN =================== -->
        <div x-show="showModalBatal"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.away="showModalBatal = false"
                 class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-stone-200/90 relative">
                
                <div class="flex items-center justify-between pb-4 border-b border-stone-100 mb-5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-[#14201a] bl-d">Batalkan Pesanan</h3>
                            <span class="text-[11px] text-stone-500">Resi {{ $pesanan->no_resi }}</span>
                        </div>
                    </div>
                    <button type="button" @click="showModalBatal = false" class="text-stone-400 hover:text-stone-700 p-1 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('pesanan.batal', $pesanan->no_resi) }}" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    
                    <p class="text-xs text-stone-600 leading-relaxed">
                        Apakah Anda yakin ingin membatalkan pesanan ini? Stok alat yang telah Anda pesan akan dilepaskan kembali ke katalog publik.
                    </p>

                    <div>
                        <label for="alasan_batal_pelanggan" class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1">
                            Alasan Pembatalan <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="alasan_batal_pelanggan" name="alasan_batal" rows="3" required placeholder="Tuliskan alasan pembatalan Anda..."
                                  class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs">{{ old('alasan_batal') }}</textarea>
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-2 border-t border-stone-100">
                        <button type="button" @click="showModalBatal = false"
                                class="px-4 py-2.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold transition">
                            Kembali
                        </button>
                        <button type="submit"
                                class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-extrabold shadow-sm transition">
                            Ya, Batalkan Pesanan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.publik>
