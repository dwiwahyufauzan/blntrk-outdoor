<x-layouts.admin>
    <x-slot:title>Buat Pesanan Walk-in - BLNTRK Outdoor</x-slot:title>

    <div class="mb-6">
        <a href="{{ route('admin.pesanan.index') }}" class="text-xs font-bold text-stone-500 hover:text-[#0e1b15] transition inline-flex items-center gap-1.5 mb-2">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Daftar Pesanan</span>
        </a>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#14201a] tracking-tight bl-d">Buat Pesanan Walk-in</h1>
        <p class="text-xs text-stone-500 mt-0.5">Catat penyewaan langsung di tempat untuk pelanggan yang datang ke toko fisik</p>
    </div>

    @if($errors->any())
        <div class="mb-6 max-w-4xl p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
            <div class="font-extrabold text-sm mb-1 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Periksa kembali formulir Anda:
            </div>
            <ul class="list-disc list-inside space-y-0.5 ml-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.pesanan.store') }}"
          x-data="adminWalkinForm({{ json_encode($alatList) }})"
          class="space-y-6 max-w-4xl">
        @csrf

        <!-- KARTU 1: PILIHAN PELANGGAN -->
        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-stone-200/90 shadow-xs">
            <h2 class="text-base font-extrabold text-[#14201a] bl-d mb-4 pb-2 border-b border-stone-100 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#5edd5b]"></span>
                Identitas Pelanggan Penyewa
            </h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-[#14201a] uppercase tracking-wider mb-2">Tipe Pelanggan</label>
                    <div class="flex items-center gap-6">
                        <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-semibold text-stone-700">
                            <input type="radio" name="tipe_pelanggan" value="terdaftar" x-model="tipePelanggan"
                                   class="text-[#0e1b15] focus:ring-[#5edd5b]">
                            <span>Pelanggan Terdaftar di Sistem</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-semibold text-stone-700">
                            <input type="radio" name="tipe_pelanggan" value="baru" x-model="tipePelanggan"
                                   class="text-[#0e1b15] focus:ring-[#5edd5b]">
                            <span>Pelanggan Baru (Walk-in Langsung)</span>
                        </label>
                    </div>
                </div>

                <!-- Jika Pelanggan Terdaftar -->
                <div x-show="tipePelanggan === 'terdaftar'" class="pt-2">
                    <label for="pelanggan_id" class="block text-xs font-bold text-[#14201a] uppercase tracking-wider mb-1.5">
                        Pilih Pelanggan <span class="text-rose-500">*</span>
                    </label>
                    <select id="pelanggan_id" name="pelanggan_id"
                            class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs text-[#14201a]">
                        <option value="">-- Pilih dari {{ $pelangganList->count() }} Pelanggan Terdaftar --</option>
                        @foreach($pelangganList as $pel)
                            <option value="{{ $pel->id }}" {{ old('pelanggan_id') == $pel->id ? 'selected' : '' }}>
                                {{ $pel->nama_lengkap }} ({{ $pel->no_hp }}) - {{ Str::limit($pel->alamat, 30) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Jika Pelanggan Baru Walk-in -->
                <div x-show="tipePelanggan === 'baru'" class="space-y-3.5 pt-2" style="display: none;">
                    <div>
                        <label for="nama_lengkap" class="block text-xs font-bold text-[#14201a] uppercase tracking-wider mb-1.5">
                            Nama Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" placeholder="Contoh: Budi Santoso"
                               class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs text-[#14201a]">
                    </div>

                    <div>
                        <label for="no_hp" class="block text-xs font-bold text-[#14201a] uppercase tracking-wider mb-1.5">
                            Nomor HP / WhatsApp <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp') }}" placeholder="08xxxxxxxxxx"
                               class="w-full text-xs font-mono rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs text-[#14201a]">
                    </div>

                    <div>
                        <label for="alamat" class="block text-xs font-bold text-[#14201a] uppercase tracking-wider mb-1.5">
                            Alamat Domisili <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="alamat" name="alamat" rows="2" placeholder="Alamat tinggal penyewa..."
                                  class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs text-[#14201a]">{{ old('alamat') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- KARTU 2: JADWAL SEWA -->
        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-stone-200/90 shadow-xs">
            <h2 class="text-base font-extrabold text-[#14201a] bl-d mb-4 pb-2 border-b border-stone-100 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#5edd5b]"></span>
                Jadwal Pengambilan & Pengembalian
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="tanggal_mulai" class="block text-xs font-bold text-[#14201a] uppercase tracking-wider mb-1.5">
                        Tanggal Mulai Sewa <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" x-model="tanggalMulai" @change="hitungDurasi()"
                           min="{{ today()->toDateString() }}" required
                           class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs text-[#14201a]">
                </div>

                <div>
                    <label for="tanggal_selesai" class="block text-xs font-bold text-[#14201a] uppercase tracking-wider mb-1.5">
                        Tanggal Selesai Sewa <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" id="tanggal_selesai" name="tanggal_selesai" x-model="tanggalSelesai" @change="hitungDurasi()"
                           :min="tanggalMulai" required
                           class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs text-[#14201a]">
                </div>
            </div>

            <div class="mt-3 text-xs text-stone-500 flex items-center gap-2">
                <span>Durasi terhitung:</span>
                <strong class="text-[#0e1b15] font-bold" x-text="durasiHari + ' Hari'"></strong>
                <span>(Batas pengembalian maks 12:00 WIB)</span>
            </div>
        </div>

        <!-- KARTU 3: ITEM ALAT -->
        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-stone-200/90 shadow-xs">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-stone-100">
                <h2 class="text-base font-extrabold text-[#14201a] bl-d flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#5edd5b]"></span>
                    Daftar Alat yang Disewa
                </h2>
                <button type="button" @click="tambahBaris()"
                        class="px-3 py-1.5 rounded-xl bg-stone-100 hover:bg-[#0e1b15] hover:text-[#5edd5b] text-stone-800 text-xs font-bold transition inline-flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Baris</span>
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(item, idx) in items" :key="idx">
                    <div class="p-3.5 rounded-xl bg-stone-50 border border-stone-200 flex flex-col sm:flex-row sm:items-center gap-3">
                        <span class="w-6 h-6 rounded-lg bg-white border border-stone-200 text-stone-500 flex items-center justify-center text-xs font-bold shrink-0" x-text="idx + 1"></span>

                        <div class="flex-1">
                            <select :name="'items[' + idx + '][alat_id]'" x-model="item.alat_id" @change="updateHarga(idx)" required
                                    class="w-full text-xs font-bold rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs text-[#14201a]">
                                <option value="">-- Pilih Alat --</option>
                                <template x-for="alat in daftarAlat" :key="alat.id">
                                    <option :value="alat.id" x-text="alat.nama + ' (' + alat.kode_alat + ') - Rp' + formatRupiah(alat.harga_per_hari) + '/hari'"></option>
                                </template>
                            </select>
                        </div>

                        <div class="w-full sm:w-24">
                            <input type="number" :name="'items[' + idx + '][jumlah]'" x-model.number="item.jumlah" min="1" max="50" required placeholder="Jumlah"
                                   class="w-full text-xs font-bold text-center rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs text-[#14201a]">
                        </div>

                        <div class="w-full sm:w-32 text-right">
                            <span class="text-xs font-bold text-[#14201a] font-mono" x-text="'Rp' + formatRupiah(item.harga * item.jumlah * durasiHari)"></span>
                        </div>

                        <button type="button" @click="hapusBaris(idx)" :disabled="items.length <= 1"
                                class="p-2 text-stone-400 hover:text-rose-600 disabled:opacity-30 disabled:cursor-not-allowed transition cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </template>
            </div>

            <div class="mt-4 pt-3 border-t border-stone-100">
                <label for="catatan" class="block text-xs font-bold text-[#14201a] uppercase tracking-wider mb-1.5">Catatan Pesanan Walk-in</label>
                <textarea id="catatan" name="catatan" rows="2" placeholder="Catatan khusus dari kasir/pelanggan..."
                          class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs text-[#14201a]">{{ old('catatan') }}</textarea>
            </div>
        </div>

        <!-- TOTAL & SUBMIT -->
        <div class="bg-[#0e1b15] p-6 sm:p-7 rounded-2xl text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 shadow-sm">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-[#5edd5b] block">Total Tagihan Sewa</span>
                <div class="text-2xl sm:text-3xl font-extrabold bl-d" x-text="'Rp' + formatRupiah(hitungTotal())"></div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.pesanan.index') }}" class="px-4 py-2.5 rounded-xl bg-stone-800 hover:bg-stone-700 text-stone-300 text-xs font-bold transition">
                    Batal
                </a>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-extrabold shadow-sm transition inline-flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Pesanan Walk-in</span>
                </button>
            </div>
        </div>
    </form>

    <script>
        function adminWalkinForm(daftarAlat) {
            return {
                tipePelanggan: '{{ old('tipe_pelanggan', 'terdaftar') }}',
                daftarAlat: daftarAlat,
                tanggalMulai: '{{ old('tanggal_mulai', today()->toDateString()) }}',
                tanggalSelesai: '{{ old('tanggal_selesai', today()->addDay()->toDateString()) }}',
                durasiHari: 1,
                items: [
                    { alat_id: daftarAlat.length > 0 ? daftarAlat[0].id : '', jumlah: 1, harga: 0 }
                ],

                init() {
                    this.hitungDurasi();
                    this.items.forEach((item, idx) => this.updateHarga(idx));
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
                    this.items.push({ alat_id: defaultId, jumlah: 1, harga: 0 });
                    this.updateHarga(this.items.length - 1);
                },

                hapusBaris(idx) {
                    if (this.items.length > 1) {
                        this.items.splice(idx, 1);
                    }
                },

                updateHarga(idx) {
                    const id = this.items[idx].alat_id;
                    const alat = this.daftarAlat.find(a => a.id == id);
                    this.items[idx].harga = alat ? parseFloat(alat.harga_per_hari) : 0;
                },

                hitungTotal() {
                    return this.items.reduce((acc, curr) => acc + (curr.harga * curr.jumlah * this.durasiHari), 0);
                },

                formatRupiah(val) {
                    return new Intl.NumberFormat('id-ID').format(val || 0);
                }
            };
        }
    </script>
</x-layouts.admin>
