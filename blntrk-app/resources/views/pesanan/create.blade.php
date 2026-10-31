<x-layouts.publik>
    <x-slot:title>Buat Pesanan Sewa - BLNTRK Outdoor</x-slot:title>

    <div class="bg-[#f4f5f6] min-h-screen py-10 sm:py-14">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Breadcrumb & Header -->
            <div class="mb-8">
                <a href="{{ route('katalog.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-stone-500 hover:text-[#0e1b15] transition mb-3">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali ke Katalog Alat</span>
                </a>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-widest bg-[#0e1b15] text-[#5edd5b] mb-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#5edd5b]"></span>
                            Formulir Pemesanan
                        </span>
                        <h1 class="text-2xl sm:text-4xl font-black text-[#0e1b15] font-urbanist tracking-tight">
                            Reservasi Perlengkapan Outdoor
                        </h1>
                        <p class="text-xs sm:text-sm text-stone-500 mt-1">
                            Pilih tanggal sewa dan daftar alat pendakian yang ingin Anda gunakan. Bayar saat serah terima unit di toko.
                        </p>
                    </div>
                </div>
            </div>

            @if(session('error'))
                <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center gap-3">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
                    <div class="font-extrabold text-sm mb-1 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Mohon periksa kembali input formulir Anda:
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 ml-1">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(!empty($paketNama))
                <div class="mb-6 p-4 rounded-2xl bg-[#0e1b15] text-white border border-[#1e3a2c] shadow-xs flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span class="w-9 h-9 rounded-xl bg-[#5edd5b] text-black font-black flex items-center justify-center text-sm shadow-xs">🎁</span>
                        <div>
                            <span class="text-[10px] font-extrabold text-[#5edd5b] uppercase tracking-wider block">Paket Bundling Terpilih</span>
                            <span class="text-sm font-extrabold font-urbanist">{{ $paketNama }}</span>
                        </div>
                    </div>
                    <span class="text-xs text-stone-300 hidden sm:inline">Daftar item paket telah dimuat otomatis</span>
                </div>
            @elseif(request('from_cart'))
                <div class="mb-6 p-4 rounded-2xl bg-white border border-stone-200 text-stone-800 shadow-2xs flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-sm">🛒</span>
                        <div>
                            <span class="text-[10px] font-extrabold text-emerald-800 uppercase tracking-wider block">Checkout Keranjang Belanja</span>
                            <span class="text-xs text-stone-600">Seluruh item dari keranjang Anda telah dimuat otomatis ke formulir pemesanan.</span>
                        </div>
                    </div>
                    <a href="{{ route('keranjang.index') }}" class="text-xs font-bold text-stone-500 hover:text-stone-900 underline">Ubah Keranjang</a>
                </div>
            @endif

            <!-- Alpine.js Order Form App -->
            <form method="POST" action="{{ route('pesanan.store') }}"
                  @submit.prevent="submitForm($el)"
                  x-data="pemesananForm({{ json_encode($alatList) }}, {{ json_encode($initialItems ?? []) }}, {{ $initialAlatId }}, {{ $initialJumlah }}, '{{ $initialMulai }}', '{{ $initialSelesai }}')"
                  class="space-y-6">
                @csrf

                <!-- KARTU 1: RENTANG JADWAL SEWA -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-stone-200/90 shadow-xs">
                    <div class="flex items-center gap-3 mb-5 pb-4 border-b border-stone-100">
                        <div class="w-10 h-10 rounded-xl bg-[#0e1b15] text-[#5edd5b] flex items-center justify-center font-bold shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-base sm:text-lg font-black text-[#0e1b15] font-urbanist">1. Jadwal Pengambilan & Pengembalian</h2>
                            <p class="text-xs text-stone-500">Maksimal batas pengembalian sewa pukul 12:00 WIB siang</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="tanggal_mulai" class="block text-xs font-extrabold uppercase tracking-wider text-stone-700 mb-1.5">
                                Tanggal Pengambilan Alat <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" id="tanggal_mulai" name="tanggal_mulai" x-model="tanggalMulai" @change="hitungDurasi()"
                                   min="{{ today()->toDateString() }}" required
                                   class="w-full text-xs font-semibold rounded-2xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs text-[#0e1b15] py-3 px-4">
                            @error('tanggal_mulai')
                                <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="tanggal_selesai" class="block text-xs font-extrabold uppercase tracking-wider text-stone-700 mb-1.5">
                                Tanggal Pengembalian Alat <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" id="tanggal_selesai" name="tanggal_selesai" x-model="tanggalSelesai" @change="hitungDurasi()"
                                   :min="tanggalMulai" required
                                   class="w-full text-xs font-semibold rounded-2xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs text-[#0e1b15] py-3 px-4">
                            @error('tanggal_selesai')
                                <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Highlight Durasi -->
                    <div class="mt-4 p-4 rounded-2xl bg-stone-50 border border-stone-200/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#5edd5b]"></span>
                            <span class="text-stone-600">Durasi Terhitung:</span>
                            <strong class="font-extrabold text-[#0e1b15] text-sm font-urbanist" x-text="durasiHari + ' Hari (x24 Jam)'"></strong>
                        </div>
                        <div class="text-stone-500 text-[11px]">
                            *Batas ambil: tanggal mulai s/d 23:59 WIB. Waktu kembali: tanggal selesai maks. 12:00 WIB.
                        </div>
                    </div>
                </div>

                <!-- KARTU 2: DAFTAR PERLENGKAPAN ALAT -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-stone-200/90 shadow-xs">
                    <div class="flex items-center justify-between gap-3 mb-5 pb-4 border-b border-stone-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#0e1b15] text-[#5edd5b] flex items-center justify-center font-bold shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                            <div>
                                <h2 class="text-base sm:text-lg font-black text-[#0e1b15] font-urbanist">2. Daftar Alat Outdoor</h2>
                                <p class="text-xs text-stone-500">Pilih alat dan tentukan jumlah unit yang dibutuhkan</p>
                            </div>
                        </div>

                        <button type="button" @click="tambahBaris()"
                                class="px-4 py-2 rounded-xl bg-stone-100 hover:bg-[#0e1b15] hover:text-[#5edd5b] text-[#0e1b15] text-xs font-extrabold transition inline-flex items-center gap-1.5 cursor-pointer shadow-2xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            <span>Tambah Alat</span>
                        </button>
                    </div>

                    <!-- List Baris Item -->
                    <div class="space-y-3.5">
                        <template x-for="(item, idx) in items" :key="idx">
                            <div class="p-4 rounded-2xl bg-stone-50/70 border border-stone-200/80 flex flex-col md:flex-row md:items-center gap-3.5 transition hover:border-stone-300">
                                <div class="w-7 h-7 rounded-lg bg-white border border-stone-200 text-stone-500 flex items-center justify-center text-xs font-bold shrink-0" x-text="idx + 1"></div>

                                <!-- Dropdown Pilihan Alat -->
                                <div class="flex-1">
                                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-stone-500 mb-1">Pilih Alat</label>
                                    <select :name="'items[' + idx + '][alat_id]'" x-model="item.alat_id" @change="updateItemHarga(idx)" required
                                            class="w-full text-xs font-bold rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs text-[#0e1b15] py-2.5">
                                        <option value="">-- Pilih Perlengkapan Outdoor --</option>
                                        <template x-for="alat in daftarAlat" :key="alat.id">
                                            <option :value="alat.id" :selected="alat.id == item.alat_id"
                                                    :disabled="alat.stok_tersedia <= 0"
                                                    x-text="alat.nama + ' (' + alat.kode_alat + ') - Rp' + formatRupiah(alat.harga_per_hari) + '/hari ' + (alat.stok_tersedia > 0 ? '[Tersedia: ' + alat.stok_tersedia + ' unit]' : '[STOK HABIS - Tidak Dapat Disewa]')"></option>
                                        </template>
                                    </select>
                                </div>

                                <!-- Jumlah Unit -->
                                <div class="w-full md:w-28">
                                    <label class="block text-[10px] font-extrabold uppercase tracking-wider text-stone-500 mb-1">Jumlah</label>
                                    <div class="flex items-center">
                                        <input type="number" :name="'items[' + idx + '][jumlah]'" x-model.number="item.jumlah" min="1" :max="getMaxStok(item.alat_id)" required
                                               class="w-full text-xs font-mono font-bold text-center rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs text-[#0e1b15] py-2.5">
                                    </div>
                                </div>

                                <!-- Subtotal Baris -->
                                <div class="w-full md:w-36 text-left md:text-right">
                                    <span class="block text-[10px] font-extrabold uppercase tracking-wider text-stone-500 mb-1">Subtotal</span>
                                    <span class="text-xs sm:text-sm font-extrabold text-[#0e1b15] font-urbanist"
                                          x-text="'Rp' + formatRupiah(item.harga * item.jumlah * durasiHari)"></span>
                                </div>

                                <!-- Tombol Hapus Baris -->
                                <div class="shrink-0 pt-2 md:pt-0">
                                    <button type="button" @click="hapusBaris(idx)" :disabled="items.length <= 1"
                                            class="p-2.5 rounded-xl border border-stone-200 text-stone-400 hover:text-rose-600 hover:border-rose-300 hover:bg-rose-50 disabled:opacity-30 disabled:cursor-not-allowed transition cursor-pointer"
                                            title="Hapus baris alat">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    @unless($pelanggan)
                        <!-- Kontak Pelanggan (profil belum lengkap) -->
                        <div class="mt-6 pt-5 border-t border-stone-100 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="no_hp" class="block text-xs font-extrabold uppercase tracking-wider text-stone-700 mb-1.5">No. WhatsApp / HP</label>
                                <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp') }}" required placeholder="08xxxxxxxxxx"
                                       class="w-full text-xs rounded-2xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs text-[#0e1b15] p-3.5">
                            </div>
                            <div>
                                <label for="alamat" class="block text-xs font-extrabold uppercase tracking-wider text-stone-700 mb-1.5">Alamat</label>
                                <input type="text" id="alamat" name="alamat" value="{{ old('alamat') }}" required
                                       class="w-full text-xs rounded-2xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs text-[#0e1b15] p-3.5">
                            </div>
                        </div>
                    @endunless

                    <!-- Catatan Tambahan -->
                    <div class="mt-6 pt-5 border-t border-stone-100">
                        <label for="catatan" class="block text-xs font-extrabold uppercase tracking-wider text-stone-700 mb-1.5">
                            Catatan Tambahan untuk Toko (Opsional)
                        </label>
                        <textarea id="catatan" name="catatan" rows="2" placeholder="Contoh: Saya ambil pukul 16.00 sore sepulang kerja..."
                                  class="w-full text-xs rounded-2xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs text-[#0e1b15] p-3.5">{{ old('catatan') }}</textarea>
                    </div>
                </div>

                <!-- KARTU 3: RINGKASAN BIAYA & SUBMIT -->
                <div class="bg-[#0e1b15] rounded-3xl p-6 sm:p-8 text-white shadow-xl">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                        <div>
                            <span class="text-[11px] font-extrabold uppercase tracking-widest text-[#5edd5b] block mb-1">Ringkasan Estimasi Biaya</span>
                            <div class="text-3xl sm:text-4xl font-black font-urbanist text-white" x-text="'Rp' + formatRupiah(hitungTotal())"></div>
                            <p class="text-xs text-stone-400 mt-1">
                                Termasuk <span class="font-bold text-white" x-text="hitungTotalUnit()"></span> unit alat untuk <span class="font-bold text-white" x-text="durasiHari"></span> hari sewa.
                            </p>
                            <p class="text-xs text-[#5edd5b] mt-1 font-bold" x-show="diskonPaket().diskon > 0" x-cloak>
                                Diskon <span x-text="diskonPaket().nama"></span>: -Rp<span x-text="formatRupiah(diskonPaket().diskon)"></span>
                            </p>
                        </div>

                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                            <a href="{{ route('katalog.index') }}" class="px-5 py-3 rounded-2xl bg-[#14251e] hover:bg-[#1a3026] text-stone-300 text-xs font-bold text-center transition">
                                Batal
                            </a>
                            <button type="submit"
                                    class="px-8 py-3.5 rounded-2xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-sm font-black transition shadow-lg inline-flex items-center justify-center gap-2 cursor-pointer font-urbanist hover:scale-102">
                                <span>Konfirmasi & Buat Pesanan</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Banner Info Bayar Toko -->
                    <div class="mt-6 pt-5 border-t border-[#1e3a2c] flex items-center gap-3 text-xs text-stone-300">
                        <svg class="w-5 h-5 text-[#5edd5b] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Sistem pembayaran dilakukan secara tunai/transfer langsung saat serah terima barang di toko fisik BLNTRK Outdoor. Wajib membawa kartu identitas (KTP/SIM) asli sebagai jaminan.</span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @include('partials.diskon-paket-js')

    <!-- Script Alpine Component -->
    <script>
        function pemesananForm(daftarAlat, initialItems, initialAlatId, initialJumlah, initialMulai, initialSelesai) {
            let startingItems = [];

            if (Array.isArray(initialItems) && initialItems.length > 0) {
                startingItems = initialItems.map(it => ({
                    alat_id: it.alat_id,
                    jumlah: parseInt(it.jumlah) || 1,
                    harga: 0
                }));
            } else {
                startingItems = [
                    {
                        alat_id: initialAlatId > 0 ? initialAlatId : (daftarAlat.length > 0 ? daftarAlat[0].id : ''),
                        jumlah: initialJumlah > 0 ? initialJumlah : 1,
                        harga: 0
                    }
                ];
            }

            return {
                daftarAlat: daftarAlat,
                tanggalMulai: initialMulai,
                tanggalSelesai: initialSelesai,
                durasiHari: 1,
                items: startingItems,

                init() {
                    this.hitungDurasi();
                    this.items.forEach((item, idx) => {
                        this.updateItemHarga(idx);
                    });
                },

                hitungDurasi() {
                    if (!this.tanggalMulai || !this.tanggalSelesai) {
                        this.durasiHari = 1;
                        return;
                    }
                    const d1 = new Date(this.tanggalMulai);
                    const d2 = new Date(this.tanggalSelesai);
                    const diffTime = d2.getTime() - d1.getTime();
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    this.durasiHari = Math.max(1, diffDays > 0 ? diffDays : 1);
                },

                tambahBaris() {
                    const defaultId = this.daftarAlat.length > 0 ? this.daftarAlat[0].id : '';
                    this.items.push({
                        alat_id: defaultId,
                        jumlah: 1,
                        harga: 0
                    });
                    this.updateItemHarga(this.items.length - 1);
                },

                hapusBaris(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },

                updateItemHarga(index) {
                    const selectedId = this.items[index].alat_id;
                    const alat = this.daftarAlat.find(a => a.id == selectedId);
                    this.items[index].harga = alat ? parseFloat(alat.harga_per_hari) : 0;
                },

                hitungKotor() {
                    return this.items.reduce((acc, curr) => {
                        return acc + (curr.harga * curr.jumlah * this.durasiHari);
                    }, 0);
                },

                diskonPaket() {
                    return window.hitungDiskonPaket(this.items, this.daftarAlat, this.durasiHari);
                },

                hitungTotal() {
                    return Math.max(0, this.hitungKotor() - this.diskonPaket().diskon);
                },

                hitungTotalUnit() {
                    return this.items.reduce((acc, curr) => acc + (parseInt(curr.jumlah) || 0), 0);
                },

                getMaxStok(alatId) {
                    const alat = this.daftarAlat.find(a => a.id == alatId);
                    return alat ? Math.max(1, (alat.stok_tersedia !== undefined ? alat.stok_tersedia : 50)) : 50;
                },

                submitForm(form) {
                    for (let i = 0; i < this.items.length; i++) {
                        const it = this.items[i];
                        const alat = this.daftarAlat.find(a => a.id == it.alat_id);
                        if (!alat) continue;
                        const sedia = alat.stok_tersedia !== undefined ? alat.stok_tersedia : 0;
                        if (sedia <= 0) {
                            alert('Maaf, perlengkapan ' + alat.nama + ' saat ini sedang habis stoknya dan tidak dapat dipesan.');
                            return;
                        }
                        if (it.jumlah > sedia) {
                            alert('Jumlah unit untuk ' + alat.nama + ' (' + it.jumlah + ' unit) melebihi stok yang tersedia (' + sedia + ' unit).');
                            return;
                        }
                    }
                    form.submit();
                },

                formatRupiah(angka) {
                    return new Intl.NumberFormat('id-ID').format(angka || 0);
                }
            };
        }
    </script>
</x-layouts.publik>
