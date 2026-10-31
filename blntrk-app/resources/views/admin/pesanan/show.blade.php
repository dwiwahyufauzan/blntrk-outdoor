<x-layouts.admin>
    <x-slot:title>Detail Pesanan #{{ $pesanan->no_resi }} - BLNTRK Outdoor</x-slot:title>

    <div x-data="{
        showModalBayar: {{ $errors->has('jumlah') || $errors->has('metode') ? 'true' : 'false' }},
        showModalAmbil: {{ $errors->has('jenis_jaminan') || $errors->has('nomor_identitas') ? 'true' : 'false' }},
        showModalBatal: {{ $errors->has('alasan_batal') ? 'true' : 'false' }},
        showModalTambahDenda: {{ $errors->has('kategori_denda_id') ? 'true' : 'false' }},
        kategoriDendaId: '{{ old('kategori_denda_id', '') }}',
        jumlahDenda: '{{ old('jumlah', '') }}',
        keteranganDenda: '{{ addslashes(old('keterangan', '')) }}',
        pilihanAlatId: '',
        alatList: [
            @foreach($pesanan->detail as $pd)
            {
                id: '{{ $pd->id }}',
                nama: '{{ addslashes($pd->alat->nama) }}',
                hargaBeli: {{ (float) ($pd->alat->harga_beli ?? 0) }}
            },
            @endforeach
        ],
        kategoriList: [
            @foreach($kategoriDendaList ?? [] as $kd)
            { id: '{{ $kd->id }}', kode: '{{ $kd->kode }}', nama: '{{ addslashes($kd->nama) }}', tarif: {{ (float) $kd->tarif_standar }} },
            @endforeach
        ],
        onPilihAlat() {
            if (this.pilihanAlatId) {
                const a = this.alatList.find(item => item.id == this.pilihanAlatId);
                if (a) {
                    if (a.hargaBeli > 0) {
                        this.jumlahDenda = a.hargaBeli;
                    }
                    const katHilang = this.kategoriList.find(k => k.kode === 'HILANG');
                    if (katHilang && !this.kategoriDendaId) {
                        this.kategoriDendaId = katHilang.id;
                    }
                    if (!this.keteranganDenda) {
                        this.keteranganDenda = 'Ganti rugi kehilangan 1 unit ' + a.nama + ' (Sesuai harga beli aset toko)';
                    }
                }
            }
        },
        onPilihKategori() {
            const k = this.kategoriList.find(item => item.id == this.kategoriDendaId);
            if (k) {
                if (k.kode === 'HILANG' && this.pilihanAlatId) {
                    const a = this.alatList.find(item => item.id == this.pilihanAlatId);
                    if (a && a.hargaBeli > 0) {
                        this.jumlahDenda = a.hargaBeli;
                        return;
                    }
                }
                if (k.tarif > 0) {
                    this.jumlahDenda = k.tarif;
                }
            }
        },
        metodeBayar: 'tunai',
        jenisJaminan: 'ktp'
    }">

        <!-- Header & Action -->
        <div class="mb-7 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('admin.pesanan.index') }}" class="text-xs font-bold text-stone-500 hover:text-[#0e1b15] transition inline-flex items-center gap-1.5 mb-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali ke Daftar Pesanan</span>
                </a>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-[#14201a] tracking-tight bl-d font-mono">
                        {{ $pesanan->no_resi }}
                    </h1>
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
                    <span class="px-3 py-1 rounded-full text-xs uppercase tracking-wider {{ $badge }}">
                        {{ $pesanan->status->label() }}
                    </span>
                </div>
                <p class="text-xs text-stone-500 mt-1">Dibuat pada {{ $pesanan->created_at->format('d/m/Y H:i') }} WIB oleh {{ $pesanan->pembuat?->nama ?? 'Sistem Reservasi Daring' }}</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <!-- Tombol WhatsApp Pelanggan (T14) -->
                @php
                    $dendaBelumLunas = $pesanan->denda->where('status_bayar', 'belum_lunas')->sum('jumlah');
                @endphp

                @if($pesanan->status === \App\Enums\StatusPesanan::Terlambat || ($infoKeterlambatan && $infoKeterlambatan['hari'] > 0))
                    <a href="{{ wa_link($pesanan->pelanggan->no_hp, \App\Services\WhatsAppService::pesanPeringatanTerlambat($pesanan, $infoKeterlambatan['hari'] ?? 1, $infoKeterlambatan['jumlah'] ?? 0)) }}" target="_blank"
                       class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-900 border border-rose-300 text-xs font-bold transition inline-flex items-center gap-1.5 shadow-2xs">
                        <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.173.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.159.57 4.184 1.564 5.938l-1.564 5.714 5.861-1.537c1.7 1.006 3.684 1.585 5.807 1.585 6.627 0 12-5.373 12-12 0-6.627-5.373-12-12-12z"/></svg>
                        <span>Peringatan Terlambat WA (Rp{{ number_format($infoKeterlambatan['jumlah'] ?? 0, 0, ',', '.') }})</span>
                    </a>
                @elseif($dendaBelumLunas > 0)
                    <a href="{{ wa_link($pesanan->pelanggan->no_hp, \App\Services\WhatsAppService::pesanTagihanDenda($pesanan, $dendaBelumLunas)) }}" target="_blank"
                       class="px-3.5 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 text-xs font-bold transition inline-flex items-center gap-1.5 shadow-2xs">
                        <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.173.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.159.57 4.184 1.564 5.938l-1.564 5.714 5.861-1.537c1.7 1.006 3.684 1.585 5.807 1.585 6.627 0 12-5.373 12-12 0-6.627-5.373-12-12-12z"/></svg>
                        <span>Kirim Tagihan Denda WA</span>
                    </a>
                @elseif(in_array($pesanan->status->value, ['disewa']))
                    <a href="{{ wa_link($pesanan->pelanggan->no_hp, \App\Services\WhatsAppService::pesanPengingatJatuhTempo($pesanan)) }}" target="_blank"
                       class="px-3.5 py-2 rounded-xl bg-white hover:bg-stone-50 text-stone-800 border border-stone-200 text-xs font-bold transition inline-flex items-center gap-1.5 shadow-2xs">
                        <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.173.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.159.57 4.184 1.564 5.938l-1.564 5.714 5.861-1.537c1.7 1.006 3.684 1.585 5.807 1.585 6.627 0 12-5.373 12-12 0-6.627-5.373-12-12-12z"/></svg>
                        <span>Kirim Pengingat Jatuh Tempo</span>
                    </a>
                @else
                    <a href="{{ wa_link($pesanan->pelanggan->no_hp, \App\Services\WhatsAppService::pesanKonfirmasiBooking($pesanan)) }}" target="_blank"
                       class="px-3.5 py-2 rounded-xl bg-white hover:bg-stone-50 text-stone-800 border border-stone-200 text-xs font-bold transition inline-flex items-center gap-1.5 shadow-2xs">
                        <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.173.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.159.57 4.184 1.564 5.938l-1.564 5.714 5.861-1.537c1.7 1.006 3.684 1.585 5.807 1.585 6.627 0 12-5.373 12-12 0-6.627-5.373-12-12-12z"/></svg>
                        <span>Hubungi WA</span>
                    </a>
                @endif

                <!-- Cetak Resi & Unduh PDF -->
                <a href="{{ route('pesanan.resi', $pesanan->no_resi) }}" target="_blank"
                   class="px-3.5 py-2 rounded-xl bg-white hover:bg-stone-50 text-stone-800 border border-stone-200 text-xs font-bold transition inline-flex items-center gap-1.5 shadow-2xs"
                   title="Buka tampilan lembar dokumen resi siap cetak">
                    <svg class="w-4 h-4 text-[#0e1b15]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Lihat Resi Dokumen</span>
                </a>

                <a href="{{ route('admin.pesanan.pdf', $pesanan->id) }}"
                   class="px-3.5 py-2 rounded-xl bg-white hover:bg-stone-50 text-stone-700 border border-stone-200 text-xs font-bold transition inline-flex items-center gap-1.5 shadow-2xs"
                   title="Unduh file dokumen PDF resi resmi">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Unduh PDF</span>
                </a>

                <a href="{{ route('pesanan.resi', $pesanan->no_resi) }}?cetak=1" target="_blank"
                   class="px-3.5 py-2 rounded-xl bg-white hover:bg-stone-50 text-stone-700 border border-stone-200 text-xs font-bold transition inline-flex items-center gap-1.5 shadow-2xs"
                   title="Cetak langsung lembar dokumen resi fisik">
                    <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Resi</span>
                </a>

                <!-- Tombol Aksi Panel Sesuai Status (F05) -->
                @if($pesanan->status === \App\Enums\StatusPesanan::Menunggu)
                    <button type="button" @click="showModalBayar = true"
                            class="px-4 py-2 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-extrabold shadow-sm transition inline-flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Catat Pembayaran</span>
                    </button>
                @endif

                @if($pesanan->status === \App\Enums\StatusPesanan::Dibayar)
                    <button type="button" @click="showModalAmbil = true"
                            class="px-4 py-2 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-extrabold shadow-sm transition inline-flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Konfirmasi Serah Terima (Ambil)</span>
                    </button>
                @endif

                @if(in_array($pesanan->status, [\App\Enums\StatusPesanan::Disewa, \App\Enums\StatusPesanan::Terlambat]))
                    <a href="{{ route('admin.pesanan.pengembalian.create', $pesanan->id) }}"
                       class="px-4 py-2 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-extrabold shadow-sm transition inline-flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Proses Pengembalian</span>
                    </a>
                @endif

                @can('batal', $pesanan)
                    <button type="button" @click="showModalBatal = true"
                            class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold transition inline-flex items-center gap-1 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>Batalkan Pesanan</span>
                    </button>
                @endcan
            </div>
        </div>

        <!-- Alert Notifikasi Status / Validasi -->
        @if(session('status'))
            <div class="mb-6 p-4 rounded-2xl bg-[#0e1b15] text-[#5edd5b] border border-[#1e3a2c] text-xs font-bold flex items-center gap-3">
                <svg class="w-5 h-5 text-[#5edd5b] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 text-rose-800 border border-rose-200 text-xs">
                <div class="font-bold mb-1 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Terjadi kesalahan input pada formulir:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 pl-1">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Timeline Status Transaksi Modern (Foresta Style) -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-stone-200/90 shadow-xs mb-6">
            <h2 class="text-xs font-extrabold uppercase tracking-widest text-stone-400 mb-4 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#5edd5b]"></span>
                <span>Progres Siklus Pesanan</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 relative">
                <!-- Tahap 1: Dibuat -->
                <div class="p-3.5 rounded-xl border border-stone-200 bg-stone-50/70">
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="w-5 h-5 rounded-full bg-[#0e1b15] text-[#5edd5b] flex items-center justify-center text-[10px] font-bold">1</span>
                        <span class="text-xs font-bold text-stone-900">Pesanan Dibuat</span>
                    </div>
                    <div class="text-[11px] text-stone-600 font-medium">{{ $pesanan->created_at->format('d/m/Y H:i') }} WIB</div>
                    <span class="text-[10px] text-stone-400 font-mono block mt-0.5">Oleh: {{ $pesanan->pembuat?->nama ?? 'Sistem Daring' }}</span>
                </div>

                <!-- Tahap 2: Pembayaran -->
                @php
                    $pembayaran = $pesanan->pembayaran->first();
                    $isPaid = (bool) $pembayaran || in_array($pesanan->status->value, ['dibayar', 'disewa', 'dikembalikan', 'selesai']);
                @endphp
                <div class="p-3.5 rounded-xl border {{ $isPaid ? 'border-[#5edd5b]/60 bg-[#5edd5b]/10' : 'border-stone-200 bg-stone-50/40 text-stone-400' }}">
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="w-5 h-5 rounded-full {{ $isPaid ? 'bg-[#5edd5b] text-black' : 'bg-stone-200 text-stone-600' }} flex items-center justify-center text-[10px] font-bold">2</span>
                        <span class="text-xs font-bold {{ $isPaid ? 'text-stone-900' : 'text-stone-500' }}">Pembayaran</span>
                    </div>
                    @if($pembayaran)
                        <div class="text-[11px] text-stone-800 font-bold font-mono">Rp{{ number_format($pembayaran->jumlah, 0, ',', '.') }} ({{ ucfirst($pembayaran->metode->value) }})</div>
                        <span class="text-[10px] text-stone-500 font-mono block mt-0.5">{{ $pembayaran->dibayar_pada->format('d/m/Y H:i') }} WIB</span>
                    @else
                        <div class="text-[11px] text-stone-400 italic">Menunggu pembayaran</div>
                    @endif
                </div>

                <!-- Tahap 3: Diambil / Disewa -->
                @php
                    $isDiambil = $pesanan->diambil_pada || in_array($pesanan->status->value, ['disewa', 'dikembalikan', 'selesai', 'terlambat']);
                @endphp
                <div class="p-3.5 rounded-xl border {{ $isDiambil ? 'border-sky-300 bg-sky-50' : 'border-stone-200 bg-stone-50/40 text-stone-400' }}">
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="w-5 h-5 rounded-full {{ $isDiambil ? 'bg-sky-600 text-white' : 'bg-stone-200 text-stone-600' }} flex items-center justify-center text-[10px] font-bold">3</span>
                        <span class="text-xs font-bold {{ $isDiambil ? 'text-stone-900' : 'text-stone-500' }}">Serah Terima Alat</span>
                    </div>
                    @if($pesanan->diambil_pada)
                        <div class="text-[11px] text-sky-950 font-bold">Unit Telah Diambil</div>
                        <span class="text-[10px] text-stone-500 font-mono block mt-0.5">{{ $pesanan->diambil_pada->format('d/m/Y H:i') }} WIB</span>
                    @else
                        <div class="text-[11px] text-stone-400 italic">Belum diserahterimakan</div>
                    @endif
                </div>

                <!-- Tahap 4: Selesai / Status Akhir -->
                <div class="p-3.5 rounded-xl border {{ $pesanan->status->value === 'dibatalkan' ? 'border-rose-300 bg-rose-50' : ($pesanan->status->value === 'dikembalikan' ? 'border-emerald-300 bg-emerald-50' : 'border-stone-200 bg-stone-50/40') }}">
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="w-5 h-5 rounded-full {{ $pesanan->status->value === 'dibatalkan' ? 'bg-rose-600 text-white' : ($pesanan->status->value === 'dikembalikan' ? 'bg-emerald-600 text-white' : 'bg-stone-200 text-stone-600') }} flex items-center justify-center text-[10px] font-bold">4</span>
                        <span class="text-xs font-bold text-stone-900">
                            {{ $pesanan->status->value === 'dibatalkan' ? 'Dibatalkan' : ($pesanan->status->value === 'dikembalikan' ? 'Selesai / Kembali' : 'Pengembalian') }}
                        </span>
                    </div>
                    @if($pesanan->status->value === 'dibatalkan')
                        <div class="text-[11px] text-rose-800 font-bold">{{ $pesanan->dibatalkan_pada?->format('d/m/Y H:i') }} WIB</div>
                        <span class="text-[10px] text-rose-600 line-clamp-1 block mt-0.5">{{ $pesanan->alasan_batal }}</span>
                    @elseif($pesanan->status->value === 'dikembalikan')
                        <div class="text-[11px] text-emerald-800 font-bold">Alat Telah Dikembalikan</div>
                    @else
                        <div class="text-[11px] text-stone-400 italic">Tempo: {{ $pesanan->tanggal_selesai->format('d/m/Y') }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- KOLOM KIRI (2 Bagian): DAFTAR ALAT & JAMINAN/PEMBAYARAN -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Tabel Item Alat -->
                <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs overflow-hidden">
                    <div class="p-5 border-b border-stone-100 flex items-center justify-between">
                        <h2 class="text-sm font-extrabold text-[#14201a] bl-d flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#5edd5b]"></span>
                            Perlengkapan Outdoor yang Disewa
                        </h2>
                        <span class="text-xs text-stone-500 font-bold">{{ $pesanan->detail->count() }} Jenis Alat ({{ $pesanan->detail->sum('jumlah') }} Unit)</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-stone-50/80 text-stone-500 uppercase text-[10px] font-bold border-b border-stone-200 tracking-wider">
                                <tr>
                                    <th class="py-3 px-5">Nama Alat</th>
                                    <th class="py-3 px-4 text-center">Jumlah</th>
                                    <th class="py-3 px-4 text-right">Tarif / Hari</th>
                                    <th class="py-3 px-5 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-100">
                                @foreach($pesanan->detail as $item)
                                    <tr>
                                        <td class="py-3.5 px-5">
                                            <div class="font-bold text-[#14201a] text-sm bl-d">{{ $item->alat->nama }}</div>
                                            <div class="text-[10px] font-mono text-stone-400">{{ $item->alat->kode_alat }} &bull; {{ $item->alat->kategori?->nama }}</div>
                                        </td>
                                        <td class="py-3.5 px-4 text-center font-bold text-stone-800">
                                            {{ $item->jumlah }} Unit
                                        </td>
                                        <td class="py-3.5 px-4 text-right font-mono text-stone-600">
                                            Rp{{ number_format($item->harga_per_hari, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3.5 px-5 text-right font-bold text-[#14201a] font-mono">
                                            Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-t-2 border-stone-200 bg-stone-50/50">
@if($pesanan->diskon_paket > 0)
                                <tr>
                                    <td colspan="3" class="py-2 px-5 text-right font-bold text-[#2f8f2c] uppercase text-[11px]">
                                        Diskon {{ $pesanan->paketBundling?->nama ?? 'Paket Bundling' }}{{ $pesanan->jumlah_paket > 1 ? ' x'.$pesanan->jumlah_paket : '' }}
                                    </td>
                                    <td class="py-2 px-5 text-right font-mono font-bold text-[#2f8f2c]">
                                        -Rp{{ number_format($pesanan->diskon_paket, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endif
                                                                <tr>
                                    <td colspan="3" class="py-3.5 px-5 text-right font-bold text-stone-500 uppercase text-[11px]">
                                        Total Biaya Sewa ({{ $pesanan->durasi_hari }} Hari)
                                    </td>
                                    <td class="py-3.5 px-5 text-right font-black text-base text-[#14201a] font-mono">
                                        Rp{{ number_format($pesanan->total_sewa, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if($pesanan->catatan)
                        <div class="p-4 border-t border-stone-100 bg-stone-50/50 text-xs text-stone-600">
                            <strong class="text-stone-800">Catatan Pemesan:</strong> {{ $pesanan->catatan }}
                        </div>
                    @endif
                </div>

                <!-- Info Pembayaran & Jaminan Identitas (Jika Sudah Ada) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Card Pembayaran -->
                    <div class="bg-white p-5 rounded-2xl border border-stone-200/90 shadow-xs">
                        <div class="flex items-center justify-between pb-3 mb-3 border-b border-stone-100">
                            <h3 class="text-xs font-bold text-[#14201a] uppercase tracking-wider flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span>Informasi Pembayaran</span>
                            </h3>
                            @if($pembayaran)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">LUNAS</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">BELUM DIBAYAR</span>
                            @endif
                        </div>

                        @if($pembayaran)
                            <div class="space-y-2 text-xs">
                                <div class="flex justify-between py-1 border-b border-stone-100">
                                    <span class="text-stone-500">Nominal Lunas:</span>
                                    <span class="font-bold text-stone-900 font-mono">Rp{{ number_format($pembayaran->jumlah, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between py-1 border-b border-stone-100">
                                    <span class="text-stone-500">Metode:</span>
                                    <span class="font-bold text-stone-800">{{ $pembayaran->metode->label() }}</span>
                                </div>
                                <div class="flex justify-between py-1 border-b border-stone-100">
                                    <span class="text-stone-500">Waktu Bayar:</span>
                                    <span class="font-mono text-stone-800">{{ $pembayaran->dibayar_pada->format('d/m/Y H:i') }} WIB</span>
                                </div>
                                <div class="flex justify-between py-1">
                                    <span class="text-stone-500">Dicatat Oleh:</span>
                                    <span class="font-bold text-[#14201a]">{{ $pembayaran->pencatat?->nama ?? 'Admin' }}</span>
                                </div>
                                @if($pembayaran->catatan)
                                    <div class="pt-2 text-[11px] text-stone-500 italic">
                                        Catatan: "{{ $pembayaran->catatan }}"
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="py-4 text-center text-xs text-stone-400">
                                Pembayaran belum dicatat oleh kasir toko.
                            </div>
                        @endif
                    </div>

                    <!-- Card Jaminan Identitas -->
                    @php
                        $jaminan = $pesanan->jaminan;
                        $estimasiTelat = $infoKeterlambatan ? (float) $infoKeterlambatan['jumlah'] : 0.0;
                        $adaDendaBelumLunas = $pesanan->denda()->where('status_bayar', 'belum_lunas')->exists() || $estimasiTelat > 0;
                        $nominalDendaTertunggak = $pesanan->denda()->where('status_bayar', 'belum_lunas')->sum('jumlah') + $estimasiTelat;
                    @endphp
                    <div class="bg-white p-5 rounded-2xl border border-stone-200/90 shadow-xs">
                        <div class="flex items-center justify-between pb-3 mb-3 border-b border-stone-100">
                            <h3 class="text-xs font-bold text-[#14201a] uppercase tracking-wider flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                                <span>Jaminan Kartu Identitas</span>
                            </h3>
                            @if($jaminan)
                                @if($jaminan->status === 'dititipkan')
                                    @if($adaDendaBelumLunas)
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-100 text-rose-800 border border-rose-300 animate-pulse">
                                            DITAHAN (DENDA AKTIF)
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-sky-100 text-sky-800">
                                            DITITIPKAN DI KASIR
                                        </span>
                                    @endif
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        DIKEMBALIKAN
                                    </span>
                                @endif
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-stone-100 text-stone-500">BELUM DITERIMA</span>
                            @endif
                        </div>

                        @if($jaminan)
                            <div class="space-y-2 text-xs">
                                <div class="flex justify-between py-1 border-b border-stone-100">
                                    <span class="text-stone-500">Jenis Jaminan:</span>
                                    <span class="font-bold text-stone-900">{{ $jaminan->jenis->label() }}</span>
                                </div>
                                <div class="flex justify-between py-1 border-b border-stone-100">
                                    <span class="text-stone-500">No. Identitas:</span>
                                    <span class="font-mono font-black text-stone-800 tracking-wider">{{ $jaminan->nomor_identitas_samar }}</span>
                                </div>
                                <div class="flex justify-between py-1 border-b border-stone-100">
                                    <span class="text-stone-500">Diterima Pada:</span>
                                    <span class="font-mono text-stone-800">{{ $jaminan->diterima_pada->format('d/m/Y H:i') }} WIB</span>
                                </div>
                                <div class="flex justify-between py-1">
                                    <span class="text-stone-500">Diterima Oleh:</span>
                                    <span class="font-bold text-[#14201a]">{{ $jaminan->penerima?->nama ?? 'Admin' }}</span>
                                </div>
                                @if($jaminan->keterangan)
                                    <div class="pt-2 text-[11px] text-stone-500 italic">
                                        Keterangan: "{{ $jaminan->keterangan }}"
                                    </div>
                                @endif

                                @if($jaminan->status === 'dititipkan')
                                    <div class="pt-3 border-t border-stone-100">
                                        @if($adaDendaBelumLunas)
                                            <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-[11px] leading-relaxed mb-2.5">
                                                <div class="font-extrabold flex items-center gap-1.5 mb-1 text-rose-900">
                                                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                    <span>Jaminan Ditahan (SOP Toko)</span>
                                                </div>
                                                <span>Terdapat denda aktif tertunggak sebesar <strong>Rp{{ number_format($nominalDendaTertunggak, 0, ',', '.') }}</strong>. Kartu identitas jaminan ditahan sampai seluruh denda dilunasi pelanggan.</span>
                                            </div>
                                            <button type="button" disabled
                                                    class="w-full px-3 py-2 rounded-xl bg-stone-100 text-stone-400 text-[11px] font-bold border border-stone-200 cursor-not-allowed flex items-center justify-center gap-1.5 opacity-80"
                                                    title="Lunasi denda terlebih dahulu">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                <span>Jaminan Terkunci (Denda Belum Lunas)</span>
                                            </button>
                                        @else
                                            <form method="POST" action="{{ route('admin.pesanan.jaminan.kembali', $pesanan->id) }}"
                                                  data-konfirmasi="primary"
                                                  data-judul="Kembalikan Kartu Identitas Jaminan"
                                                  data-pesan="Konfirmasi penyerahan kembali kartu identitas jaminan (KTP/SIM) kepada penyewa? Pastikan seluruh kewajiban telah terpenuhi."
                                                  data-tombol="Ya, Serahkan Kembali">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="w-full px-3 py-2 rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-800 text-[11px] font-bold border border-sky-200 transition cursor-pointer flex items-center justify-center gap-1.5">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    <span>Kembalikan Kartu Identitas</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @else
                                    <div class="pt-2 text-[10px] text-emerald-700 font-bold flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        <span>Dikembalikan: {{ $jaminan->dikembalikan_pada?->format('d/m/Y H:i') }}</span>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="py-4 text-center text-xs text-stone-400">
                                Jaminan fisik kartu identitas akan dicatat saat serah terima barang.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Kartu Hasil Pengembalian & Denda (Jika Sudah Dikembalikan) -->
                @if($pesanan->pengembalian)
                    @php $kembali = $pesanan->pengembalian; @endphp
                    <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs overflow-hidden">
                        <div class="p-5 border-b border-stone-100 flex items-center justify-between">
                            <h2 class="text-sm font-extrabold text-[#14201a] bl-d flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                Hasil Pemeriksaan Pengembalian Alat
                            </h2>
                            <span class="text-xs text-stone-500 font-mono">{{ $kembali->dikembalikan_pada->format('d/m/Y H:i') }} WIB</span>
                        </div>

                        <div class="p-5 border-b border-stone-100 bg-stone-50/40 grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-stone-400 block mb-0.5">Diterima Oleh</span>
                                <div class="font-bold text-stone-800">{{ $kembali->penerima?->nama ?? 'Admin' }}</div>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-stone-400 block mb-0.5">Keterlambatan</span>
                                <div class="font-bold {{ $kembali->hari_terlambat > 0 ? 'text-rose-700' : 'text-emerald-700' }}">
                                    {{ $kembali->hari_terlambat > 0 ? $kembali->hari_terlambat . ' Hari' : 'Tepat Waktu' }}
                                </div>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-stone-400 block mb-0.5">Catatan Kasir</span>
                                <div class="text-stone-600 italic">{{ $kembali->catatan ?: '-' }}</div>
                            </div>
                        </div>

                        <!-- Kondisi Alat Tiap Item -->
                        <div class="p-5">
                            <span class="text-[10px] uppercase font-bold text-stone-400 block mb-2 tracking-wider">Kondisi Alat Saat Diterima</span>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-stone-50 text-stone-400 uppercase text-[10px] font-bold border-b border-stone-200">
                                        <tr>
                                            <th class="py-2 px-3">Nama Alat</th>
                                            <th class="py-2 px-2 text-center text-emerald-700">Baik</th>
                                            <th class="py-2 px-2 text-center text-amber-700">R. Ringan</th>
                                            <th class="py-2 px-2 text-center text-orange-700">R. Berat</th>
                                            <th class="py-2 px-2 text-center text-rose-700">Hilang</th>
                                            <th class="py-2 px-3">Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-stone-100">
                                        @foreach($kembali->detail as $kd)
                                            <tr>
                                                <td class="py-2.5 px-3 font-bold text-stone-800">{{ $kd->pesananDetail->alat->nama }}</td>
                                                <td class="py-2.5 px-2 text-center font-bold text-emerald-700">{{ $kd->jml_baik }}</td>
                                                <td class="py-2.5 px-2 text-center font-bold text-amber-700">{{ $kd->jml_rusak_ringan }}</td>
                                                <td class="py-2.5 px-2 text-center font-bold text-orange-700">{{ $kd->jml_rusak_berat }}</td>
                                                <td class="py-2.5 px-2 text-center font-bold text-rose-700">{{ $kd->jml_hilang }}</td>
                                                <td class="py-2.5 px-3 text-stone-500 text-[11px]">{{ $kd->catatan ?: '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Kartu Denda & Ganti Rugi Perlengkapan (Dapat Dikelola Kapan Saja) -->
                <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs overflow-hidden">
                    <div class="p-5 border-b border-stone-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full {{ $pesanan->denda->where('status_bayar', 'belum_lunas')->isNotEmpty() || ($infoKeterlambatan && $infoKeterlambatan['hari'] > 0) ? 'bg-rose-500 animate-ping' : 'bg-[#5edd5b]' }}"></span>
                            <div>
                                <h2 class="text-sm font-extrabold text-[#14201a] bl-d">Tagihan Denda & Ganti Rugi Perlengkapan</h2>
                                <span class="text-[11px] text-stone-500">Keterlambatan, barang hilang, atau kerusakan fisik perlengkapan</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            @php
                                $totalDendaTercatat = $pesanan->denda->sum('jumlah');
                                $totalDendaDenganBerjalan = $totalDendaTercatat + ($infoKeterlambatan && !$pesanan->pengembalian ? (float)$infoKeterlambatan['jumlah'] : 0);
                            @endphp

                            @if($totalDendaDenganBerjalan > 0)
                                <span class="text-xs font-mono font-black text-rose-700 bg-rose-50 px-2.5 py-1 rounded-lg border border-rose-200">
                                    Total: Rp{{ number_format($totalDendaDenganBerjalan, 0, ',', '.') }}
                                </span>
                            @endif

                            @if($pesanan->status->value !== 'dibatalkan')
                                <button type="button" @click="showModalTambahDenda = true"
                                        class="px-3 py-1.5 rounded-xl bg-[#0e1b15] hover:bg-[#1a2e24] text-[#5edd5b] text-xs font-bold transition flex items-center gap-1.5 shadow-2xs cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                    <span>Tambah Denda Manual</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Box Peringatan Denda Keterlambatan Berjalan (Otomatis) -->
                    @if($infoKeterlambatan && $infoKeterlambatan['hari'] > 0 && !$pesanan->pengembalian)
                        <div class="p-5 border-b border-rose-200 bg-rose-50/70">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-200 text-rose-900 border border-rose-300 animate-pulse font-mono">
                                            TERLAMBAT {{ $infoKeterlambatan['hari'] }} HARI
                                        </span>
                                        <span class="text-xs font-extrabold text-rose-950 font-urbanist">Denda Keterlambatan Sedang Berjalan (Otomatis)</span>
                                    </div>
                                    <p class="text-xs text-stone-600">Unit belum diserahkan ke kasir toko. Batas jadwal sewa: {{ $pesanan->tanggal_selesai->format('d/m/Y') }} 12:00 WIB.</p>
                                </div>
                                <div class="text-left sm:text-right">
                                    <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Estimasi Denda Berjalan</span>
                                    <span class="text-xl font-black font-mono text-rose-600">Rp{{ number_format($infoKeterlambatan['jumlah'], 0, ',', '.') }}</span>
                                    <span class="text-[10px] text-stone-400 block font-mono">Rp{{ number_format($infoKeterlambatan['tarif_harian'], 0, ',', '.') }}/hari</span>
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-t border-rose-200/60 flex flex-wrap items-center justify-between gap-3 text-xs">
                                <span class="text-stone-600 text-[11px]">*Denda keterlambatan akan dibukukan resmi saat form pengembalian diproses. Denda kerusakan & kelengkapan hilang diinput manual saat cek fisik.</span>
                                <a href="{{ route('admin.pesanan.pengembalian.create', $pesanan->id) }}"
                                   class="px-3.5 py-1.5 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-extrabold transition shadow-2xs inline-flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    <span>Proses Pengembalian Sekarang</span>
                                </a>
                            </div>
                        </div>
                    @endif

                    @if($pesanan->denda->isNotEmpty())
                        <div class="p-5 space-y-3 bg-stone-50/40">
                            @foreach($pesanan->denda as $d)
                                <div class="p-4 rounded-xl bg-white border border-stone-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs shadow-2xs">
                                    <div class="space-y-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <!-- Badge Kategori Denda -->
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider {{ $d->jenis->value === 'kehilangan' ? 'bg-rose-100 text-rose-900 border border-rose-200' : ($d->jenis->value === 'keterlambatan' ? 'bg-amber-100 text-amber-900 border border-amber-200' : 'bg-orange-100 text-orange-900 border border-orange-200') }}">
                                                {{ $d->kategoriDenda?->nama ?? $d->jenis->label() }}
                                            </span>
                                            <span class="font-bold text-stone-900 font-mono text-sm">Rp{{ number_format($d->jumlah, 0, ',', '.') }}</span>
                                            <span class="text-[10px] text-stone-400 font-mono">Dicatat: {{ $d->created_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                        <div class="text-stone-700 text-xs">
                                            <strong class="text-stone-900">Informasi Denda:</strong> {{ $d->keterangan }}
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 shrink-0">
                                        @if($d->status_bayar === 'lunas')
                                            <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-1">
                                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                <span>Lunas ({{ $d->dilunasi_pada?->format('d/m H:i') }})</span>
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-rose-100 text-rose-800 border border-rose-200">
                                                Belum Lunas
                                            </span>
                                            <form method="POST" action="{{ route('admin.denda.lunas', $d->id) }}"
                                                  data-konfirmasi="primary"
                                                  data-judul="Konfirmasi Pelunasan Denda"
                                                  data-pesan="Tandai lunas denda Rp{{ number_format($d->jumlah, 0, ',', '.') }}?"
                                                  data-tombol="Ya, Tandai Lunas">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-[11px] font-extrabold transition shadow-2xs cursor-pointer inline-flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                    <span>Tandai Lunas</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-6 text-center text-xs text-stone-400">
                            <svg class="w-8 h-8 mx-auto text-stone-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p>Tidak ada catatan denda atau ganti rugi pada pesanan ini.</p>
                        </div>
                    @endif
                </div>

                <!-- Kartu Jadwal Sewa -->
                <div class="bg-white p-6 rounded-2xl border border-stone-200/90 shadow-xs">
                    <h2 class="text-sm font-extrabold text-[#14201a] bl-d mb-4 pb-2 border-b border-stone-100 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#5edd5b]"></span>
                        Waktu & Siklus Sewa
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                        <div class="p-3.5 rounded-xl bg-stone-50 border border-stone-200/80">
                            <span class="text-[10px] uppercase font-bold text-stone-400 block mb-1">Mulai Ambil</span>
                            <div class="font-bold text-stone-800 text-sm">{{ $pesanan->tanggal_mulai->format('d/m/Y') }}</div>
                            <span class="text-[11px] text-stone-500">Maks. 23:59 WIB</span>
                        </div>

                        <div class="p-3.5 rounded-xl bg-stone-50 border border-stone-200/80">
                            <span class="text-[10px] uppercase font-bold text-stone-400 block mb-1">Jatuh Tempo Kembali</span>
                            <div class="font-bold text-stone-800 text-sm">{{ $pesanan->tanggal_selesai->format('d/m/Y') }}</div>
                            <span class="text-[11px] text-stone-500">Pukul {{ $pesanan->jatuh_tempo?->format('H:i') ?? '12:00' }} WIB</span>
                        </div>

                        <div class="p-3.5 rounded-xl bg-stone-50 border border-stone-200/80">
                            <span class="text-[10px] uppercase font-bold text-stone-400 block mb-1">Total Durasi</span>
                            <div class="font-black text-sm text-[#0e1b15]">{{ $pesanan->durasi_hari }} Hari Sewa</div>
                            <span class="text-[11px] text-stone-500">x24 Jam Operasional</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN (1 Bagian): DATA PELANGGAN & RINGKASAN -->
            <div class="space-y-6">
                <!-- Profil Pelanggan -->
                <div class="bg-white p-6 rounded-2xl border border-stone-200/90 shadow-xs">
                    <h2 class="text-sm font-extrabold text-[#14201a] bl-d mb-4 pb-2 border-b border-stone-100 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#5edd5b]"></span>
                        Informasi Penyewa
                    </h2>

                    <div class="space-y-3 text-xs">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-stone-400 block">Nama Lengkap</span>
                            <div class="font-bold text-stone-900 text-sm bl-d">{{ $pesanan->pelanggan->nama_lengkap }}</div>
                            @if($pesanan->pelanggan->is_walk_in)
                                <span class="inline-block mt-0.5 px-2 py-0.5 rounded-md bg-stone-100 text-[10px] font-bold text-stone-600">Pelanggan Walk-in</span>
                            @else
                                <span class="inline-block mt-0.5 px-2 py-0.5 rounded-md bg-[#5edd5b]/20 text-[10px] font-bold text-[#0e1b15]">Akun Daring</span>
                            @endif
                        </div>

                        <div>
                            <span class="text-[10px] uppercase font-bold text-stone-400 block">Kontak WhatsApp</span>
                            <div class="font-mono text-stone-800 font-bold">{{ $pesanan->pelanggan->no_hp }}</div>
                        </div>

                        <div>
                            <span class="text-[10px] uppercase font-bold text-stone-400 block">Alamat Domisili</span>
                            <div class="text-stone-600 leading-relaxed">{{ $pesanan->pelanggan->alamat }}</div>
                        </div>

                        <div class="pt-3 border-t border-stone-100">
                            <a href="{{ route('admin.pelanggan.show', $pesanan->pelanggan->id) }}"
                               class="text-xs font-bold text-[#4e7a5a] hover:text-[#1e3a2c] transition inline-flex items-center gap-1">
                                <span>Lihat Riwayat Pelanggan Ini</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Info Kontak Toko & Status Transaksi -->
                <div class="bg-[#0e1b15] p-6 rounded-2xl text-white shadow-xs">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#5edd5b] block mb-1">Status Transaksi Saat Ini</span>
                    <div class="text-lg font-black font-urbanist text-white">
                        {{ $pesanan->status->label() }}
                    </div>
                    @if($pesanan->status === \App\Enums\StatusPesanan::Menunggu)
                        <p class="text-xs text-stone-300 mt-2">
                            Penyewa belum melunasi biaya sewa. Silakan konfirmasi pembayaran di kasir toko sebelum menyerahkan alat.
                        </p>
                    @elseif($pesanan->status === \App\Enums\StatusPesanan::Dibayar)
                        <p class="text-xs text-[#5edd5b] mt-2 font-bold">
                            Pembayaran telah lunas. Lakukan serah terima alat dan catat kartu identitas sebagai jaminan.
                        </p>
                    @elseif($pesanan->status === \App\Enums\StatusPesanan::Disewa)
                        <p class="text-xs text-sky-200 mt-2">
                            Unit perlengkapan sedang dibawa oleh penyewa. Jadwal kembali maksimal {{ $pesanan->tanggal_selesai->format('d/m/Y') }} pukul {{ $pesanan->jatuh_tempo?->format('H:i') ?? '12:00' }} WIB.
                        </p>
                    @elseif($pesanan->status === \App\Enums\StatusPesanan::Dibatalkan)
                        <p class="text-xs text-rose-300 mt-2">
                            Pesanan ini telah dibatalkan. Alasan: "{{ $pesanan->alasan_batal }}".
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- =================== MODAL: CATAT PEMBAYARAN =================== -->
        <div x-show="showModalBayar"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.away="showModalBayar = false"
                 class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-stone-200/90 relative">
                
                <div class="flex items-center justify-between pb-4 border-b border-stone-100 mb-5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-[#5edd5b]/20 text-[#0e1b15] flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-[#14201a] bl-d">Konfirmasi Pembayaran</h3>
                            <span class="text-[11px] text-stone-500">Resi {{ $pesanan->no_resi }}</span>
                        </div>
                    </div>
                    <button type="button" @click="showModalBayar = false" class="text-stone-400 hover:text-stone-700 p-1 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.pesanan.bayar', $pesanan->id) }}" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1">Total Tagihan Sewa</label>
                        <div class="p-3 rounded-xl bg-stone-50 border border-stone-200 font-mono font-black text-lg text-[#0e1b15]">
                            Rp{{ number_format($pesanan->total_sewa, 0, ',', '.') }}
                        </div>
                        <input type="hidden" name="jumlah" value="{{ $pesanan->total_sewa }}">
                        <p class="text-[10px] text-stone-400 mt-1">Jumlah pembayaran sewa harus lunas sesuai nominal pesanan.</p>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-2">Pilih Metode Pembayaran</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="border rounded-2xl p-3 cursor-pointer flex flex-col items-center justify-center text-center transition"
                                   :class="metodeBayar === 'tunai' ? 'border-[#0e1b15] bg-[#5edd5b]/10 text-[#0e1b15] font-bold shadow-xs' : 'border-stone-200 bg-white text-stone-600 hover:bg-stone-50'">
                                <input type="radio" name="metode" value="tunai" x-model="metodeBayar" class="sr-only">
                                <svg class="w-5 h-5 mb-1 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span class="text-xs">Tunai di Kasir</span>
                            </label>

                            <label class="border rounded-2xl p-3 cursor-pointer flex flex-col items-center justify-center text-center transition"
                                   :class="metodeBayar === 'transfer' ? 'border-[#0e1b15] bg-[#5edd5b]/10 text-[#0e1b15] font-bold shadow-xs' : 'border-stone-200 bg-white text-stone-600 hover:bg-stone-50'">
                                <input type="radio" name="metode" value="transfer" x-model="metodeBayar" class="sr-only">
                                <svg class="w-5 h-5 mb-1 text-sky-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                <span class="text-xs">Transfer Bank</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="catatan_bayar" class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1">Catatan Tambahan (Opsional)</label>
                        <input type="text" id="catatan_bayar" name="catatan" placeholder="Contoh: Pembayaran tunai pas di meja kasir..."
                               class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs">
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-2 border-t border-stone-100">
                        <button type="button" @click="showModalBayar = false"
                                class="px-4 py-2.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold transition">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-5 py-2.5 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-extrabold shadow-sm transition">
                            Simpan Pembayaran Lunas
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- =================== MODAL: SERAH TERIMA & JAMINAN =================== -->
        <div x-show="showModalAmbil"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.away="showModalAmbil = false"
                 class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-stone-200/90 relative">
                
                <div class="flex items-center justify-between pb-4 border-b border-stone-100 mb-5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-sky-100 text-sky-800 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-[#14201a] bl-d">Konfirmasi Pengambilan & Jaminan</h3>
                            <span class="text-[11px] text-stone-500">Serah terima unit alat untuk resi {{ $pesanan->no_resi }}</span>
                        </div>
                    </div>
                    <button type="button" @click="showModalAmbil = false" class="text-stone-400 hover:text-stone-700 p-1 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.pesanan.ambil', $pesanan->id) }}" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-2">Jenis Kartu Identitas Fisik yang Dititipkan</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            @foreach([
                                'ktp' => 'KTP',
                                'sim' => 'SIM',
                                'kartu_pelajar' => 'K. Pelajar',
                                'lainnya' => 'Lainnya'
                            ] as $val => $lbl)
                                <label class="border rounded-xl p-2.5 cursor-pointer flex flex-col items-center justify-center text-center transition"
                                       :class="jenisJaminan === '{{ $val }}' ? 'border-[#0e1b15] bg-[#5edd5b]/15 text-[#0e1b15] font-extrabold' : 'border-stone-200 bg-white text-stone-600 hover:bg-stone-50'">
                                    <input type="radio" name="jenis_jaminan" value="{{ $val }}" x-model="jenisJaminan" class="sr-only">
                                    <span class="text-xs">{{ $lbl }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label for="nomor_identitas" class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1">
                            Nomor Kartu Identitas Fisik <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="nomor_identitas" name="nomor_identitas" required placeholder="Contoh: 3204123456780001"
                               class="w-full text-xs font-mono rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs">
                        <div class="mt-1.5 p-2.5 rounded-xl bg-amber-50 border border-amber-200/70 text-[10px] text-amber-900 flex items-start gap-2">
                            <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Privasi NF09: Sistem hanya akan menyimpan 4 digit terakhir nomor identitas secara tersamar (contoh: <strong>****0001</strong>).</span>
                        </div>
                    </div>

                    <div>
                        <label for="keterangan_jaminan" class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1">Keterangan / Kondisi Jaminan (Opsional)</label>
                        <input type="text" id="keterangan_jaminan" name="keterangan" placeholder="Contoh: KTP atas nama Dwi, fisik mulus disimpan di box kasir..."
                               class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs">
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-2 border-t border-stone-100">
                        <button type="button" @click="showModalAmbil = false"
                                class="px-4 py-2.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold transition">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-5 py-2.5 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-extrabold shadow-sm transition">
                            Serahkan Alat & Simpan Jaminan
                        </button>
                    </div>
                </form>
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
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
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

                <form method="POST" action="{{ route('admin.pesanan.batal', $pesanan->id) }}" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    
                    <p class="text-xs text-stone-600 leading-relaxed">
                        Pesanan yang dibatalkan akan melepaskan seluruh alokasi stok alat terkait ke sistem ketersediaan.
                    </p>

                    <div>
                        <label for="alasan_batal" class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1">
                            Alasan Pembatalan <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="alasan_batal" name="alasan_batal" rows="3" required placeholder="Jelaskan alasan pembatalan pesanan ini..."
                                  class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs">{{ old('alasan_batal') }}</textarea>
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-2 border-t border-stone-100">
                        <button type="button" @click="showModalBatal = false"
                                class="px-4 py-2.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold transition">
                            Tutup
                        </button>
                        <button type="submit"
                                class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-extrabold shadow-sm transition">
                            Ya, Batalkan Pesanan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- =================== MODAL: TAMBAH DENDA MANUAL =================== -->
        <div x-show="showModalTambahDenda"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.away="showModalTambahDenda = false"
                 class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-stone-200/90 relative">
                
                <div class="flex items-center justify-between pb-4 border-b border-stone-100 mb-5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-[#14201a] bl-d">Tambah Denda / Ganti Rugi</h3>
                            <span class="text-[11px] text-stone-500">Resi {{ $pesanan->no_resi }} &bull; {{ $pesanan->pelanggan->nama_lengkap }}</span>
                        </div>
                    </div>
                    <button type="button" @click="showModalTambahDenda = false" class="text-stone-400 hover:text-stone-700 p-1 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.pesanan.denda.tambah', $pesanan->id) }}" class="space-y-4">
                    @csrf
                    
                    <!-- Pilihan Alat Terkait Pesanan (Opsional untuk Hitung Cepat Harga Beli) -->
                    @if($pesanan->detail->isNotEmpty())
                        <div>
                            <label for="modal_pilih_alat" class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                                <span>Pilih Alat yang Disewa (Opsional)</span>
                                <span class="text-[10px] text-emerald-700 font-bold">Auto-isi harga beli aset</span>
                            </label>
                            <select id="modal_pilih_alat" x-model="pilihanAlatId" @change="onPilihAlat()"
                                    class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs bg-stone-50/50">
                                <option value="">-- Bukan spesifik per unit / input bebas --</option>
                                @foreach($pesanan->detail as $pd)
                                    <option value="{{ $pd->id }}">
                                        {{ $pd->alat->nama }} {{ $pd->alat->varian ? '('.$pd->alat->varian.')' : '' }} &bull; Harga Beli: Rp{{ number_format($pd->alat->harga_beli, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-[10px] text-stone-400 mt-1 block">Pilih alat jika ada unit yang hilang agar nominal ganti rugi otomatis terisi sesuai harga beli unit.</span>
                        </div>
                    @endif

                    <div>
                        <label for="modal_kategori_denda" class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">
                            Kategori Denda <span class="text-rose-500">*</span>
                        </label>
                        <select id="modal_kategori_denda" name="kategori_denda_id" required
                                x-model="kategoriDendaId"
                                @change="onPilihKategori()"
                                class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs">
                            <option value="">-- Pilih Kategori Denda --</option>
                            @foreach($kategoriDendaList ?? [] as $k)
                                <option value="{{ $k->id }}">
                                    [{{ strtoupper($k->kode) }}] {{ $k->nama }} - {{ $k->kode === 'HILANG' ? 'Sesuai Harga Beli Unit' : ($k->tarif_standar > 0 ? 'Rp'.number_format($k->tarif_standar, 0, ',', '.') : 'Tarif Fleksibel') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="modal_jumlah_denda" class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">
                            Nominal Denda / Ganti Rugi (Rp) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-mono font-bold text-stone-400">Rp</span>
                            <input type="number" id="modal_jumlah_denda" name="jumlah" required min="1000" step="1000"
                                   x-model="jumlahDenda" placeholder="0"
                                   class="w-full pl-9 text-xs font-mono font-bold rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs">
                        </div>
                        <span class="text-[10px] text-stone-400 mt-1 block">Untuk kehilangan alat, denda tidak dipatok flat melainkan mengganti rugi penuh sesuai harga beli alat.</span>
                    </div>

                    <div>
                        <label for="modal_keterangan_denda" class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">
                            Keterangan & Informasi Tambahan <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="modal_keterangan_denda" name="keterangan" rows="3" required
                                  x-model="keteranganDenda"
                                  placeholder="Contoh: Ganti rugi kehilangan unit tenda sesuai harga beli aset toko..."
                                  class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs"></textarea>
                        <span class="text-[10px] text-stone-400 mt-0.5 block">Keterangan ini akan langsung dapat dibaca oleh pelanggan di detail pesanan mereka.</span>
                    </div>

                    <div class="p-3 rounded-xl bg-amber-50 border border-amber-200/80 text-[11px] text-amber-900 leading-relaxed">
                        <div class="font-bold flex items-center gap-1.5 text-amber-950 mb-0.5">
                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>Dampak pada Jaminan</span>
                        </div>
                        <span>Jika denda ini berstatus belum lunas, jaminan identitas (KTP/SIM) penyewa akan ditahan oleh sistem sampai denda ini diselesaikan.</span>
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-2 border-t border-stone-100">
                        <button type="button" @click="showModalTambahDenda = false"
                                class="px-4 py-2.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold transition">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-extrabold shadow-sm transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Tambahkan Denda</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.admin>
