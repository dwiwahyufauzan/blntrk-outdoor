<x-layouts.admin>
    <x-slot:title>Detail Pesanan #{{ $pesanan->no_resi }} - BLNTRK Outdoor</x-slot:title>

    <div x-data="{
        showModalBayar: {{ $errors->has('jumlah') || $errors->has('metode') ? 'true' : 'false' }},
        showModalAmbil: {{ $errors->has('jenis_jaminan') || $errors->has('nomor_identitas') || $errors->has('nominal_deposit') ? 'true' : 'false' }},
        showModalBatal: {{ $errors->has('alasan_batal') || ($pesanan->status === \App\Enums\StatusPesanan::Dibayar && ($errors->has('penanganan_dana') || $errors->has('jumlah_refund') || $errors->has('metode_refund'))) ? 'true' : 'false' }},
        showModalRefund: {{ $pesanan->status === \App\Enums\StatusPesanan::Dibatalkan && ($errors->has('jumlah_refund') || $errors->has('metode_refund')) ? 'true' : 'false' }},
        penangananDana: '{{ old('penanganan_dana', '') }}',
        showModalJadwal: {{ $errors->has('tanggal_mulai') ? 'true' : 'false' }},
        showModalPerpanjang: {{ $errors->has('tanggal_selesai') && $pesanan->status === \App\Enums\StatusPesanan::Disewa ? 'true' : 'false' }},
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
        @php
            $pembayaran = $pesanan->pembayaran->first();
            $dendaBelumLunas = $pesanan->denda->where('status_bayar', 'belum_lunas')->sum('jumlah');
        @endphp

        @include('admin.pesanan.partials.header')

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
        @include('admin.pesanan.partials.timeline')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- KOLOM KIRI (2 Bagian): DAFTAR ALAT & JAMINAN/PEMBAYARAN -->
            <div class="lg:col-span-2 space-y-6">
                @include('admin.pesanan.partials.item-alat')

                <!-- Info Pembayaran & Jaminan Identitas (Jika Sudah Ada) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @include('admin.pesanan.partials.kartu-pembayaran')
                    @include('admin.pesanan.partials.kartu-jaminan')
                </div>
                @include('admin.pesanan.partials.kartu-pengembalian')

                @include('admin.pesanan.partials.kartu-denda')

                @include('admin.pesanan.partials.kartu-jadwal')
            </div>

            <!-- KOLOM KANAN (1 Bagian): DATA PELANGGAN & RINGKASAN -->
            <div class="space-y-6">
                @include('admin.pesanan.partials.sidebar')
            </div>
        </div>
        {{-- Modal aksi pesanan --}}
        @include('admin.pesanan.partials.modal-bayar')
        @include('admin.pesanan.partials.modal-ambil')
        @include('admin.pesanan.partials.modal-batal')
        @include('admin.pesanan.partials.modal-refund')
        @include('admin.pesanan.partials.modal-jadwal')
        @include('admin.pesanan.partials.modal-perpanjang')
        @include('admin.pesanan.partials.modal-tambah-denda')

    </div>
</x-layouts.admin>
