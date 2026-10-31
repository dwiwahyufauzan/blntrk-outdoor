<x-layouts.publik>
    <x-slot:title>Detail Pesanan #{{ $pesanan->no_resi }} - BLNTRK Outdoor</x-slot:title>

    <div x-data="{ showModalBatal: false }" class="bg-[#f4f5f6] min-h-screen py-10 sm:py-14">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Navigasi Balik & Aksi Utama Atas -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <a href="{{ route('pesanan.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-stone-500 hover:text-[#0e1b15] transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali ke Daftar Pesanan</span>
                </a>

                <div class="flex flex-wrap items-center gap-2">
                    <!-- Tombol Lihat Lembar Resi -->
                    <a href="{{ route('pesanan.resi', $pesanan->no_resi) }}"
                       class="px-4 py-2.5 rounded-xl bg-white hover:bg-stone-50 text-stone-800 border border-stone-200 text-xs font-extrabold transition inline-flex items-center gap-2 shadow-2xs">
                        <svg class="w-4 h-4 text-[#0e1b15]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Lihat Lembar Resi</span>
                    </a>

                    <!-- Tombol Unduh PDF Resi -->
                    <a href="{{ route('pesanan.pdf', $pesanan->no_resi) }}"
                       class="px-3.5 py-2.5 rounded-xl bg-white hover:bg-stone-50 text-stone-700 border border-stone-200 text-xs font-bold transition inline-flex items-center gap-1.5 shadow-2xs">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span class="hidden sm:inline">Unduh PDF</span>
                    </a>

                    <!-- Tombol Batalkan Pesanan (Khusus Status Menunggu) -->
                    @can('batal', $pesanan)
                        <button type="button" @click="showModalBatal = true"
                                class="px-4 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-extrabold transition inline-flex items-center gap-1.5 shadow-2xs cursor-pointer">
                            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span>Batalkan Pesanan</span>
                        </button>
                    @endcan

                    <!-- WhatsApp Toko -->
                    @php
                        $waToko = preg_replace('/\D/', '', \App\Models\Pengaturan::ambil('no_wa_toko', '6281234567890'));
                        $pesanWa = rawurlencode("Halo BLNTRK Outdoor, saya ingin konfirmasi pesanan sewa dengan nomor resi " . $pesanan->no_resi . " atas nama " . $pesanan->pelanggan->nama_lengkap . ".");
                    @endphp
                    <a href="https://wa.me/{{ $waToko }}?text={{ $pesanWa }}" target="_blank"
                       class="px-4 py-2.5 rounded-xl bg-[#0e1b15] hover:bg-[#1e3a2c] text-[#5edd5b] text-xs font-extrabold transition inline-flex items-center gap-1.5 shadow-xs">
                        <svg class="w-4 h-4 text-[#5edd5b]" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.173.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.159.57 4.184 1.564 5.938l-1.564 5.714 5.861-1.537c1.7 1.006 3.684 1.585 5.807 1.585 6.627 0 12-5.373 12-12 0-6.627-5.373-12-12-12z"/></svg>
                        <span>WhatsApp Toko</span>
                    </a>
                </div>
            </div>

            <!-- Flash Alert -->
            @if(session('status'))
                <div class="mb-6 p-4 rounded-2xl bg-[#0e1b15] text-[#5edd5b] text-xs font-bold border border-[#1e3a2c] shadow-xs flex items-center gap-3">
                    <svg class="w-5 h-5 text-[#5edd5b] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @php
                $dendaBelumLunasList = $pesanan->denda->where('status_bayar', 'belum_lunas');
                $totalDendaBelumLunas = $dendaBelumLunasList->sum('jumlah');
                $estimasiDendaBerjalan = $infoKeterlambatan ? (float) $infoKeterlambatan['jumlah'] : 0.0;
                $grandTotalDenda = $totalDendaBelumLunas + $estimasiDendaBerjalan;
            @endphp

            <!-- Banner Peringatan Jaminan Ditahan (Jika Ada Denda Belum Lunas atau Sedang Terlambat) -->
            @if($grandTotalDenda > 0)
                <div class="mb-6 p-5 rounded-3xl bg-rose-50 border-2 border-rose-300 text-rose-950 shadow-sm">
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-2xl bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div class="flex-1 text-xs">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 mb-1.5">
                                <h3 class="text-sm font-black font-urbanist text-rose-900 tracking-tight">
                                    Pemberitahuan: Kartu Identitas Jaminan Anda Ditahan Sementara
                                </h3>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-200 text-rose-900 self-start sm:self-auto font-mono">
                                    Total Denda: Rp{{ number_format($grandTotalDenda, 0, ',', '.') }}
                                </span>
                            </div>
                            <p class="text-stone-700 leading-relaxed">
                                Sesuai SOP ketentuan toko sewa BLNTRK Outdoor, kartu identitas fisik (KTP/SIM) yang Anda titipkan sebagai jaminan saat ini <strong>ditahan sementara di kasir toko</strong>
                                @if($estimasiDendaBerjalan > 0 && $totalDendaBelumLunas > 0)
                                    karena masa sewa melewati batas waktu (denda berjalan Rp{{ number_format($estimasiDendaBerjalan, 0, ',', '.') }}) serta terdapat denda fisik tercatat sebesar Rp{{ number_format($totalDendaBelumLunas, 0, ',', '.') }} yang belum dilunasi.
                                @elseif($estimasiDendaBerjalan > 0)
                                    karena masa sewa telah melewati batas waktu pengembalian (terlambat {{ $infoKeterlambatan['hari'] }} hari) dengan estimasi denda berjalan Rp{{ number_format($estimasiDendaBerjalan, 0, ',', '.') }}.
                                @else
                                    karena masih terdapat kewajiban denda / biaya ganti rugi perlengkapan sebesar Rp{{ number_format($totalDendaBelumLunas, 0, ',', '.') }} yang belum dilunasi.
                                @endif
                            </p>
                            <div class="mt-2.5 pt-2 border-t border-rose-200/80 flex flex-wrap items-center gap-2 text-[11px] text-stone-600">
                                <span class="font-bold text-rose-800">Petunjuk Pelunasan:</span>
                                <span>Silakan selesaikan pembayaran denda di kasir toko saat serah terima pengembalian agar kartu identitas jaminan Anda dapat segera diserahkan kembali.</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Header Kartu Detail Pesanan -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-stone-200/90 shadow-sm mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-stone-100">
                    <div>
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="text-xs font-bold text-stone-400 uppercase tracking-widest">Detail Reservasi Sewa</span>
                            <span class="text-stone-300">•</span>
                            <span class="text-xs font-mono text-stone-500">{{ $pesanan->created_at->translatedFormat('d F Y, H:i') }} WIB</span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-black font-urbanist text-[#0e1b15] tracking-tight">
                            Pesanan #{{ $pesanan->no_resi }}
                        </h1>
                    </div>

                    <div>
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
                        <span class="px-4 py-1.5 rounded-full text-xs uppercase tracking-wider {{ $badge }}">
                            {{ $pesanan->status->label() }}
                        </span>
                    </div>
                </div>

                <!-- Progres Siklus Pesanan (Foresta Timeline) -->
                <div class="pt-6">
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-stone-400 block mb-3">Status Siklus Pemesanan</span>
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
                        <!-- Step 1: Dibuat -->
                        <div class="p-3.5 rounded-2xl border border-stone-200 bg-stone-50/70">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="w-5 h-5 rounded-full bg-[#0e1b15] text-[#5edd5b] flex items-center justify-center text-[10px] font-black">1</span>
                                <span class="font-bold text-stone-900">Reservasi Dibuat</span>
                            </div>
                            <span class="text-[11px] text-stone-500 font-mono block">{{ $pesanan->created_at->format('d/m/Y H:i') }} WIB</span>
                        </div>

                        <!-- Step 2: Pembayaran -->
                        @php
                            $adaPembayaran = $pesanan->pembayaran && $pesanan->pembayaran->isNotEmpty();
                            $pembayaranUtama = $adaPembayaran ? $pesanan->pembayaran->first() : null;
                            $totalBayar = $adaPembayaran ? $pesanan->pembayaran->sum('jumlah') : 0;
                            $isPaid = $adaPembayaran || in_array($pesanan->status->value, ['dibayar', 'disewa', 'dikembalikan', 'selesai']);
                        @endphp
                        <div class="p-3.5 rounded-2xl border {{ $isPaid ? 'border-[#5edd5b]/60 bg-[#5edd5b]/10' : 'border-stone-200 bg-stone-50/40 text-stone-400' }}">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="w-5 h-5 rounded-full {{ $isPaid ? 'bg-[#5edd5b] text-black font-black' : 'bg-stone-200 text-stone-600' }} flex items-center justify-center text-[10px]">2</span>
                                <span class="font-bold {{ $isPaid ? 'text-stone-900' : 'text-stone-500' }}">Pembayaran Toko</span>
                            </div>
                            @if($pembayaranUtama)
                                <span class="text-[11px] text-emerald-900 font-bold block">Rp{{ number_format($totalBayar, 0, ',', '.') }} (Lunas)</span>
                            @else
                                <span class="text-[11px] text-stone-400 italic block">Menunggu kasir toko</span>
                            @endif
                        </div>

                        <!-- Step 3: Diambil & Jaminan -->
                        @php
                            $isDiambil = $pesanan->diambil_pada || in_array($pesanan->status->value, ['disewa', 'dikembalikan', 'selesai', 'terlambat']);
                        @endphp
                        <div class="p-3.5 rounded-2xl border {{ $isDiambil ? 'border-sky-300 bg-sky-50' : 'border-stone-200 bg-stone-50/40 text-stone-400' }}">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="w-5 h-5 rounded-full {{ $isDiambil ? 'bg-sky-600 text-white font-black' : 'bg-stone-200 text-stone-600' }} flex items-center justify-center text-[10px]">3</span>
                                <span class="font-bold {{ $isDiambil ? 'text-sky-950' : 'text-stone-500' }}">Serah Terima Alat</span>
                            </div>
                            @if($pesanan->diambil_pada)
                                <span class="text-[11px] text-sky-900 font-medium block">Diambil: {{ $pesanan->diambil_pada->format('d/m/Y H:i') }}</span>
                            @else
                                <span class="text-[11px] text-stone-400 italic block">Unit belum diambil</span>
                            @endif
                        </div>

                        <!-- Step 4: Selesai / Status Akhir -->
                        @php
                            $isSelesai = $pesanan->status->value === 'dikembalikan';
                            $isBatal = $pesanan->status->value === 'dibatalkan';
                        @endphp
                        <div class="p-3.5 rounded-2xl border {{ $isBatal ? 'border-rose-300 bg-rose-50' : ($isSelesai ? 'border-emerald-300 bg-emerald-50' : 'border-stone-200 bg-stone-50/40') }}">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="w-5 h-5 rounded-full {{ $isBatal ? 'bg-rose-600 text-white' : ($isSelesai ? 'bg-emerald-600 text-white' : 'bg-stone-200 text-stone-600') }} flex items-center justify-center text-[10px] font-black">4</span>
                                <span class="font-bold text-stone-900">
                                    {{ $isBatal ? 'Dibatalkan' : ($isSelesai ? 'Dikembalikan' : 'Selesai Kembali') }}
                                </span>
                            </div>
                            @if($isBatal)
                                <span class="text-[11px] text-rose-700 block truncate">{{ $pesanan->alasan_batal }}</span>
                            @elseif($isSelesai)
                                <span class="text-[11px] text-emerald-800 font-bold block">Tuntas dikembalikan</span>
                            @else
                                <span class="text-[11px] text-stone-400 italic block">Maks {{ $pesanan->tanggal_selesai->format('d/m/Y') }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Banner Informasi Status Terkini -->
                <div class="mt-6">
                    @if($pesanan->status === \App\Enums\StatusPesanan::Menunggu)
                        <div class="p-4 sm:p-5 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-950 flex items-start sm:items-center gap-3">
                            <svg class="w-5 h-5 text-amber-700 shrink-0 mt-0.5 sm:mt-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <div class="flex-grow">
                                <span class="font-extrabold block">Menunggu Pembayaran di Toko</span>
                                <span class="text-amber-800 text-[11px]">Silakan datang ke toko BLNTRK Outdoor sebelum <strong>{{ $pesanan->batas_ambil?->translatedFormat('d F Y, H:i') ?? 'Hari pertama sewa' }} WIB</strong> untuk melunasi sewa dan mengambil perlengkapan. Jika melewati batas waktu, pesanan akan dibatalkan otomatis.</span>
                            </div>
                        </div>
                    @elseif($pesanan->status === \App\Enums\StatusPesanan::Dibayar)
                        <div class="p-4 sm:p-5 rounded-2xl bg-[#5edd5b]/15 border border-[#5edd5b]/30 text-xs text-[#0e1b15] flex items-center gap-3">
                            <svg class="w-5 h-5 text-emerald-800 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <span class="font-extrabold block">Pembayaran Dikonfirmasi Lunas</span>
                                <span class="text-stone-700 text-[11px]">Peralatan Anda telah disiapkan oleh staf toko. Mohon bawa <strong>Kartu Identitas Asli (KTP / SIM / Kartu Pelajar)</strong> saat mengambil perlengkapan.</span>
                            </div>
                        </div>
                    @elseif($pesanan->status === \App\Enums\StatusPesanan::Disewa)
                        <div class="p-4 sm:p-5 rounded-2xl bg-sky-50 border border-sky-200 text-xs text-sky-950 flex items-center gap-3">
                            <svg class="w-5 h-5 text-sky-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <span class="font-extrabold block">Peralatan Sedang Disewa</span>
                                <span class="text-sky-800 text-[11px]">Unit telah diserahterimakan pada {{ $pesanan->diambil_pada?->format('d/m/Y H:i') }} WIB. Wajib dikembalikan maksimal <strong>{{ $pesanan->tanggal_selesai->translatedFormat('d F Y') }} pukul 12:00 WIB</strong>.</span>
                            </div>
                        </div>
                    @elseif($pesanan->status === \App\Enums\StatusPesanan::Terlambat || ($infoKeterlambatan && $infoKeterlambatan['hari'] > 0))
                        @php
                            $hariTelat = $infoKeterlambatan['hari'] ?? 1;
                            $tarifPerHari = $infoKeterlambatan['tarif_harian'] ?? $pesanan->detail->sum(fn($d) => $d->harga_per_hari * $d->jumlah);
                            $nominalDendaTelat = $infoKeterlambatan['jumlah'] ?? ($hariTelat * $tarifPerHari);
                        @endphp
                        <div class="p-5 sm:p-6 rounded-3xl bg-rose-50 border-2 border-rose-300 text-xs text-rose-950 shadow-sm">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-2xl bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-xs mt-0.5">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </div>
                                <div class="flex-1">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 mb-2">
                                        <h3 class="text-sm sm:text-base font-black font-urbanist text-rose-900 tracking-tight">
                                            Peringatan: Masa Sewa Melewati Batas Waktu (Terlambat {{ $hariTelat }} Hari)
                                        </h3>
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-200 text-rose-900 border border-rose-300 font-mono self-start sm:self-auto animate-pulse">
                                            Denda Berjalan: Rp{{ number_format($nominalDendaTelat, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <p class="text-stone-700 leading-relaxed mb-3">
                                        Batas waktu pengembalian sewa Anda telah jatuh tempo pada <strong>{{ $pesanan->tanggal_selesai->translatedFormat('d F Y') }} pukul 12:00 WIB</strong>. Unit perlengkapan sewa saat ini tercatat belum diserahkan kembali ke toko BLNTRK Outdoor.
                                    </p>

                                    <!-- Grid Kalkulasi Denda Berjalan -->
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 mb-3 bg-white p-3.5 rounded-2xl border border-rose-200/80">
                                        <div>
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400 block">Durasi Keterlambatan</span>
                                            <span class="font-extrabold text-rose-900 text-sm">Terlambat {{ $hariTelat }} Hari</span>
                                            <span class="text-[10px] text-stone-500 block">lewat batas 12:00 WIB</span>
                                        </div>
                                        <div>
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400 block">Tarif Sewa Harian</span>
                                            <span class="font-mono font-bold text-stone-800 text-sm">Rp{{ number_format($tarifPerHari, 0, ',', '.') }}/hari</span>
                                            <span class="text-[10px] text-stone-500 block">total semua alat sewa</span>
                                        </div>
                                        <div>
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400 block">Estimasi Denda Berjalan</span>
                                            <span class="font-mono font-black text-rose-600 text-sm">Rp{{ number_format($nominalDendaTelat, 0, ',', '.') }}</span>
                                            <span class="text-[10px] text-rose-700 font-medium block">otomatis per 24 jam</span>
                                        </div>
                                    </div>

                                    <!-- SOP Transparan Ketentuan Denda -->
                                    <div class="p-3 bg-rose-100/60 rounded-xl border border-rose-200 text-[11px] text-rose-950 space-y-1">
                                        <div class="font-bold flex items-center gap-1.5 text-rose-900">
                                            <svg class="w-3.5 h-3.5 text-rose-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Informasi Ketentuan Denda & Jaminan Toko BLNTRK Outdoor:</span>
                                        </div>
                                        <ul class="list-disc list-inside space-y-0.5 text-stone-700 pl-1 leading-relaxed">
                                            <li><strong>Denda Keterlambatan:</strong> Terhitung otomatis sesuai tarif harian per 24 jam keterlambatan sampai barang diserahkan ke kasir toko.</li>
                                            <li><strong>Denda Kerusakan & Kehilangan:</strong> Diperiksa kelengkapannya secara fisik saat pengembalian di toko dan diinput manual oleh admin.</li>
                                            <li><strong>Jaminan Identitas (KTP/SIM):</strong> Ditahan sementara di toko dan baru dapat diserahkan kembali setelah seluruh denda dilunasi.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($pesanan->status === \App\Enums\StatusPesanan::Dibatalkan)
                        <div class="p-4 sm:p-5 rounded-2xl bg-stone-100 border border-stone-200 text-xs text-stone-700 flex items-center gap-3">
                            <svg class="w-5 h-5 text-stone-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <span class="font-extrabold block text-stone-900">Pesanan Dibatalkan</span>
                                <span class="text-[11px] text-stone-600">Alasan: "{{ $pesanan->alasan_batal ?? 'Dibatalkan' }}"</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Konten Utama: 2 Kolom (Rincian Alat & Jadwal/Transaksi) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Kolom Kiri: Rincian Barang yang Disewa (2 Kolom Lebar) -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Rincian Perlengkapan -->
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-stone-200/90 shadow-sm">
                        <div class="flex items-center justify-between pb-4 mb-4 border-b border-stone-100">
                            <h2 class="text-base font-extrabold text-[#0e1b15] font-urbanist">
                                Rincian Perlengkapan yang Dipesan
                            </h2>
                            <span class="text-xs text-stone-400 font-medium">
                                {{ $pesanan->detail->sum('jumlah') }} Unit Total
                            </span>
                        </div>

                        <div class="divide-y divide-stone-100">
                            @foreach($pesanan->detail as $item)
                                <div class="py-4 first:pt-0 last:pb-0 flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3.5">
                                        <div class="w-12 h-12 rounded-2xl bg-stone-100 border border-stone-200/80 flex items-center justify-center text-stone-500 shrink-0">
                                            @if($item->alat->foto)
                                                <img src="{{ asset('storage/' . $item->alat->foto) }}" alt="{{ $item->alat->nama }}" class="w-full h-full object-cover rounded-2xl">
                                            @else
                                                <svg class="w-6 h-6 text-stone-400" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M14 6l4 8h-3l2 4H7l4-7 2 3 1-2v-6zm0-4L9 11l-3-5L1 18h22L14 2z"/>
                                                </svg>
                                            @endif
                                        </div>
                                        <div>
                                            <h4 class="font-extrabold text-stone-900 text-sm font-urbanist">{{ $item->alat->nama }}</h4>
                                            <span class="text-[11px] text-stone-400 font-mono">
                                                {{ $item->alat->kode_alat }} &bull; {{ $item->alat->kategori?->nama }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <div class="text-sm font-black text-stone-900 font-mono">
                                            Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                                        </div>
                                        <span class="text-[11px] text-stone-500">
                                            {{ $item->jumlah }} unit &times; Rp{{ number_format($item->harga_per_hari, 0, ',', '.') }}/hari
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Subtotal & Total -->
                        <div class="mt-6 pt-4 border-t-2 border-stone-100 space-y-2 text-xs">
                            <div class="flex justify-between items-center text-stone-500">
                                <span>Durasi Sewa:</span>
                                <span class="font-bold text-stone-800">{{ $pesanan->durasi_hari }} Hari (24 Jam &times; {{ $pesanan->durasi_hari }})</span>
                            </div>
@if($pesanan->diskon_paket > 0)
                            <div class="flex justify-between items-center text-[#2f8f2c] font-bold">
                                <span>Diskon {{ $pesanan->paketBundling?->nama ?? 'Paket Bundling' }}{{ $pesanan->jumlah_paket > 1 ? ' x'.$pesanan->jumlah_paket : '' }}:</span>
                                <span>-Rp{{ number_format($pesanan->diskon_paket, 0, ',', '.') }}</span>
                            </div>
                            @endif
                                                        <div class="flex justify-between items-center pt-2 border-t border-stone-100">
                                <span class="font-extrabold text-stone-900 text-sm font-urbanist">Total Biaya Sewa:</span>
                                <span class="text-xl sm:text-2xl font-black font-urbanist text-[#0e1b15]">
                                    Rp{{ number_format($pesanan->total_sewa, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        @if($pesanan->catatan)
                            <div class="mt-5 p-3.5 rounded-2xl bg-stone-50 border border-stone-200 text-xs text-stone-600">
                                <strong class="text-stone-800 block mb-0.5">Catatan Tambahan:</strong>
                                <span>{{ $pesanan->catatan }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Rekam Jejak Transaksi Fisik (Pembayaran, Jaminan, Pengembalian, Denda) -->
                    @if($adaPembayaran || $pesanan->jaminan || $pesanan->pengembalian || $pesanan->denda->isNotEmpty() || ($infoKeterlambatan && $infoKeterlambatan['hari'] > 0))
                        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-stone-200/90 shadow-sm space-y-5">
                            <h2 class="text-base font-extrabold text-[#0e1b15] font-urbanist">
                                Rekam Jejak Transaksi & Fisik
                            </h2>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                                <!-- Pembayaran -->
                                @if($adaPembayaran && $pembayaranUtama)
                                    <div class="bg-stone-50 rounded-2xl p-4 border border-stone-200/80">
                                        <div class="flex items-center justify-between pb-2 mb-2 border-b border-stone-200/60">
                                            <span class="font-bold text-stone-900">Pembayaran Sewa</span>
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-[#5edd5b] text-black">
                                                LUNAS
                                            </span>
                                        </div>
                                        <div class="space-y-1 text-stone-600">
                                            <div class="flex justify-between">
                                                <span class="text-stone-400">Nominal:</span>
                                                <span class="font-bold text-stone-900 font-mono">Rp{{ number_format($totalBayar, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-stone-400">Metode:</span>
                                                <span class="font-semibold text-stone-800">{{ $pembayaranUtama->metode->label() }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-stone-400">Waktu Bayar:</span>
                                                <span class="font-mono text-stone-700">{{ $pembayaranUtama->dibayar_pada?->format('d/m/Y H:i') }} WIB</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Jaminan -->
                                @if($pesanan->jaminan)
                                    <div class="bg-stone-50 rounded-2xl p-4 border border-stone-200/80">
                                        <div class="flex items-center justify-between pb-2 mb-2 border-b border-stone-200/60">
                                            <span class="font-bold text-stone-900">Jaminan Identitas</span>
                                            @if($pesanan->jaminan->dikembalikan_pada)
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-emerald-100 text-emerald-800">
                                                    DIKEMBALIKAN
                                                </span>
                                            @elseif($grandTotalDenda > 0)
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-rose-100 text-rose-800 border border-rose-300 animate-pulse">
                                                    DITAHAN (DENDA)
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-amber-100 text-amber-900">
                                                    DITITIPKAN
                                                </span>
                                            @endif
                                        </div>
                                        <div class="space-y-1 text-stone-600">
                                            <div class="flex justify-between">
                                                <span class="text-stone-400">Jenis Kartu:</span>
                                                <span class="font-bold text-stone-900">{{ is_string($pesanan->jaminan->jenis) ? strtoupper($pesanan->jaminan->jenis) : $pesanan->jaminan->jenis->label() }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-stone-400">No. Samar:</span>
                                                <span class="font-mono text-stone-800">{{ $pesanan->jaminan->nomor_identitas_samar ?? $pesanan->jaminan->nomor_identitas }}</span>
                                            </div>
                                            @if($pesanan->jaminan->dikembalikan_pada)
                                                <div class="flex justify-between text-[11px] text-emerald-700 font-medium pt-1">
                                                    <span>Waktu Kembali:</span>
                                                    <span>{{ $pesanan->jaminan->dikembalikan_pada->format('d/m/Y H:i') }}</span>
                                                </div>
                                            @elseif($grandTotalDenda > 0)
                                                <div class="text-[10px] text-rose-700 font-bold pt-1">
                                                    *Ditahan di kasir toko hingga denda (Rp{{ number_format($grandTotalDenda, 0, ',', '.') }}) dilunasi.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <!-- Kartu Denda Keterlambatan Berjalan (Jika sedang terlambat & belum ada form pengembalian) -->
                                @if($infoKeterlambatan && $infoKeterlambatan['hari'] > 0 && !$pesanan->pengembalian)
                                    <div class="bg-rose-50/90 rounded-2xl p-4 border border-rose-200 sm:col-span-2">
                                        <div class="flex items-center justify-between pb-2 mb-2 border-b border-rose-200/80">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <span class="font-bold text-rose-950 font-urbanist">Denda Keterlambatan Berjalan (Otomatis)</span>
                                            </div>
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-rose-200 text-rose-900 border border-rose-300 animate-pulse">
                                                TERLAMBAT {{ $infoKeterlambatan['hari'] }} HARI
                                            </span>
                                        </div>
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-stone-600">
                                            <div>
                                                <span class="text-stone-500 text-[11px] block">Tarif Sewa Harian:</span>
                                                <span class="font-mono font-bold text-stone-800">Rp{{ number_format($infoKeterlambatan['tarif_harian'], 0, ',', '.') }}/hari &times; {{ $infoKeterlambatan['hari'] }} hari</span>
                                            </div>
                                            <div class="text-left sm:text-right">
                                                <span class="text-stone-500 text-[11px] block">Estimasi Total Denda:</span>
                                                <span class="font-mono font-black text-rose-600 text-base">Rp{{ number_format($infoKeterlambatan['jumlah'], 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                        <p class="text-[10px] text-stone-500 mt-2 pt-2 border-t border-rose-200/60">
                                            *Nominal dihitung per 24 jam keterlambatan. Denda kerusakan/kehilangan kelengkapan akan diperiksa fisik saat kembali di toko dan diinput manual oleh admin.
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <!-- Rekam Pengembalian Alat & Kondisi -->
                            @if($pesanan->pengembalian)
                                <div class="bg-stone-50 rounded-2xl p-4 border border-stone-200/80">
                                    <div class="flex items-center justify-between pb-2 mb-2 border-b border-stone-200/60">
                                        <span class="font-bold text-stone-900">Kondisi Alat Saat Pengembalian</span>
                                        <span class="text-[11px] font-mono text-stone-500">
                                            {{ $pesanan->pengembalian->dikembalikan_pada?->format('d/m/Y H:i') }} WIB
                                        </span>
                                    </div>
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-left text-xs">
                                            <thead class="text-[9px] uppercase tracking-wider text-stone-400 font-bold border-b border-stone-200">
                                                <tr>
                                                    <th class="py-1">Alat</th>
                                                    <th class="py-1 text-center">Baik</th>
                                                    <th class="py-1 text-center">Rusak Ringan</th>
                                                    <th class="py-1 text-center">Rusak Berat</th>
                                                    <th class="py-1 text-center">Hilang</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-stone-100">
                                                @foreach($pesanan->pengembalian->detail as $pd)
                                                    <tr>
                                                        <td class="py-1.5 font-semibold text-stone-800">{{ $pd->pesananDetail?->alat?->nama ?? 'Alat' }}</td>
                                                        <td class="py-1.5 text-center text-emerald-700 font-bold">{{ $pd->jml_baik ?? 0 }}</td>
                                                        <td class="py-1.5 text-center text-amber-700 font-bold">{{ $pd->jml_rusak_ringan ?? 0 }}</td>
                                                        <td class="py-1.5 text-center text-rose-700 font-bold">{{ $pd->jml_rusak_berat ?? 0 }}</td>
                                                        <td class="py-1.5 text-center text-rose-900 font-black">{{ $pd->jml_hilang ?? 0 }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            <!-- Rincian Denda & Ganti Rugi Perlengkapan -->
                            @if($pesanan->denda->isNotEmpty())
                                <div class="bg-rose-50/70 rounded-2xl p-5 border border-rose-200/90 shadow-2xs">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-3 mb-3 border-b border-rose-200/80 gap-2">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            <span class="font-extrabold text-rose-950 font-urbanist text-sm">Catatan Denda & Ganti Rugi Perlengkapan</span>
                                        </div>
                                        @php
                                            $semuaLunas = $pesanan->denda->every(fn($d) => $d->status_bayar === 'lunas');
                                        @endphp
                                        <div class="flex items-center gap-2 self-start sm:self-auto">
                                            <span class="font-mono font-black text-rose-700 text-xs">Total: Rp{{ number_format($pesanan->denda->sum('jumlah'), 0, ',', '.') }}</span>
                                            <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider {{ $semuaLunas ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-rose-200 text-rose-900 border border-rose-300' }}">
                                                {{ $semuaLunas ? 'Lunas' : 'Belum Lunas' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="space-y-3">
                                        @foreach($pesanan->denda as $d)
                                            <div class="p-3 bg-white rounded-xl border border-rose-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 text-xs shadow-2xs">
                                                <div class="space-y-1">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider {{ $d->jenis->value === 'kehilangan' ? 'bg-rose-100 text-rose-900 border border-rose-200' : ($d->jenis->value === 'keterlambatan' ? 'bg-amber-100 text-amber-900 border border-amber-200' : 'bg-orange-100 text-orange-900 border border-orange-200') }}">
                                                            {{ $d->kategoriDenda?->nama ?? $d->jenis->label() }}
                                                        </span>
                                                        <span class="font-mono font-black text-rose-800 text-xs">Rp{{ number_format($d->jumlah, 0, ',', '.') }}</span>
                                                    </div>
                                                    <p class="text-stone-700 leading-relaxed text-[11px]">
                                                        <strong class="text-stone-900">Keterangan:</strong> {{ $d->keterangan }}
                                                    </p>
                                                </div>

                                                <div class="shrink-0 text-right">
                                                    @if($d->status_bayar === 'lunas')
                                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-1 rounded-lg border border-emerald-200">
                                                            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                            <span>Lunas {{ $d->dilunasi_pada ? '('.$d->dilunasi_pada->format('d/m/Y').')' : '' }}</span>
                                                        </span>
                                                    @else
                                                        <span class="inline-block text-[10px] font-bold text-rose-700 bg-rose-100 px-2.5 py-1 rounded-lg border border-rose-200">
                                                            Selesaikan di Toko
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @if(!$semuaLunas)
                                        <p class="text-[10px] text-rose-800 font-medium mt-3 pt-2 border-t border-rose-200/60 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Silakan tunjukkan nomor resi pesanan ini di meja kasir untuk melunasi denda dan mengambil kembali kartu identitas jaminan Anda.</span>
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif

                </div>

                <!-- Kolom Kanan: Jadwal Sewa & Ringkasan Pelanggan -->
                <div class="space-y-6">

                    <!-- Card Jadwal Sewa -->
                    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-stone-200/90 shadow-sm">
                        <h3 class="text-sm font-extrabold text-[#0e1b15] font-urbanist uppercase tracking-wider mb-4 pb-2 border-b border-stone-100">
                            Jadwal & Waktu Sewa
                        </h3>

                        <div class="space-y-3 text-xs">
                            <div>
                                <span class="text-stone-400 block text-[11px] mb-0.5">Tanggal Mulai Ambil:</span>
                                <div class="font-extrabold text-stone-900 text-sm">
                                    {{ $pesanan->tanggal_mulai->translatedFormat('l, d F Y') }}
                                </div>
                            </div>

                            <div>
                                <span class="text-stone-400 block text-[11px] mb-0.5">Batas Pengembalian:</span>
                                <div class="font-extrabold text-stone-900 text-sm">
                                    {{ $pesanan->tanggal_selesai->translatedFormat('l, d F Y') }}
                                </div>
                                <span class="text-rose-600 font-bold text-[11px] block mt-0.5">Maksimal pukul 12:00 WIB</span>
                            </div>

                            <div class="pt-2 border-t border-stone-100 flex justify-between items-center">
                                <span class="text-stone-500">Durasi Penyewaan:</span>
                                <span class="font-extrabold text-[#0e1b15] bg-[#5edd5b]/20 px-2.5 py-1 rounded-xl">
                                    {{ $pesanan->durasi_hari }} Hari
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Identitas Penyewa -->
                    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-stone-200/90 shadow-sm">
                        <h3 class="text-sm font-extrabold text-[#0e1b15] font-urbanist uppercase tracking-wider mb-4 pb-2 border-b border-stone-100">
                            Data Penyewa
                        </h3>

                        <div class="space-y-2 text-xs">
                            <div>
                                <span class="text-stone-400 block text-[11px]">Nama Lengkap:</span>
                                <div class="font-bold text-stone-900 text-sm font-urbanist">{{ $pesanan->pelanggan->nama_lengkap }}</div>
                            </div>
                            <div>
                                <span class="text-stone-400 block text-[11px]">Nomor WhatsApp:</span>
                                <div class="font-mono text-stone-800 font-semibold">{{ $pesanan->pelanggan->no_hp }}</div>
                            </div>
                            <div>
                                <span class="text-stone-400 block text-[11px]">Alamat:</span>
                                <div class="text-stone-600 leading-relaxed">{{ $pesanan->pelanggan->alamat }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Petunjuk & Syarat Cepat -->
                    <div class="bg-stone-50/90 rounded-3xl p-6 border border-stone-200/80 text-xs text-stone-600 space-y-2">
                        <h4 class="font-extrabold text-stone-900 uppercase tracking-wider text-[11px]">Ketentuan Pengambilan:</h4>
                        <ul class="list-disc list-inside space-y-1 text-[11px] leading-relaxed">
                            <li>Tunjukkan <strong>Resi #{{ $pesanan->no_resi }}</strong> saat di toko.</li>
                            <li>Tinggalkan <strong>KTP / SIM Asli</strong> sebagai jaminan.</li>
                            <li>Pelunasan biaya sewa tunai/transfer saat ambil alat.</li>
                            <li>Kembalikan alat maksimal jam 12:00 WIB di hari terakhir.</li>
                        </ul>
                    </div>

                    <!-- Tombol Resi & Pembatalan di Bawah Card -->
                    <div class="space-y-2 pt-2">
                        <a href="{{ route('pesanan.resi', $pesanan->no_resi) }}"
                           class="w-full py-3 rounded-2xl bg-[#0e1b15] hover:bg-[#1e3a2c] text-[#5edd5b] text-xs font-black transition flex items-center justify-center gap-2 shadow-xs">
                            <svg class="w-4 h-4 text-[#5edd5b]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Buka Lembar Resi Resmi</span>
                        </a>

                        @can('batal', $pesanan)
                            <button type="button" @click="showModalBatal = true"
                                    class="w-full py-2.5 rounded-2xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold transition flex items-center justify-center gap-1.5 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Batalkan Pesanan Ini</span>
                            </button>
                        @endcan
                    </div>

                </div>

            </div>

        </div>

        <!-- =================== MODAL: BATALKAN PESANAN =================== -->
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
                            <span class="text-[11px] text-stone-500 font-mono">Resi {{ $pesanan->no_resi }}</span>
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
                        Apakah Anda yakin ingin membatalkan pesanan ini? Kuota unit alat yang telah Anda pesan akan dilepaskan kembali ke katalog publik agar dapat disewa oleh pengguna lain.
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
