<x-layouts.publik>
    <x-slot:title>Detail Pesanan #{{ $pesanan->no_resi }} - BLNTRK Outdoor</x-slot:title>

    <div x-data="{ showModalBatal: false }" class="bg-[#f4f5f6] min-h-screen py-10 sm:py-14">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            @include('pesanan.partials.aksi-atas')

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
                $adaPembayaran = $pesanan->pembayaran && $pesanan->pembayaran->isNotEmpty();
                $pembayaranUtama = $adaPembayaran ? $pesanan->pembayaran->first() : null;
                $totalBayar = $adaPembayaran ? $pesanan->pembayaran->sum('jumlah') : 0;
            @endphp
            @include('pesanan.partials.banner-denda')

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
                @include('pesanan.partials.timeline')

                @include('pesanan.partials.banner-status')
            </div>

            <!-- Konten Utama: 2 Kolom (Rincian Alat & Jadwal/Transaksi) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Kolom Kiri: Rincian Barang yang Disewa (2 Kolom Lebar) -->
                <div class="lg:col-span-2 space-y-6">
                    @include('pesanan.partials.rincian-alat')

                    @include('pesanan.partials.rekam-jejak')

                </div>

                <!-- Kolom Kanan: Jadwal Sewa & Ringkasan Pelanggan -->
                <div class="space-y-6">
                    @include('pesanan.partials.kartu-jadwal')

                    @include('pesanan.partials.kartu-identitas')

                    @include('pesanan.partials.ketentuan')

                    @include('pesanan.partials.tombol-resi')

                </div>

            </div>

        </div>
        @include('pesanan.partials.modal-batal')

    </div>
</x-layouts.publik>
