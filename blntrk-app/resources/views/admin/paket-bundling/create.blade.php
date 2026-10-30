<x-layouts.admin>
    <x-slot:title>Tambah Paket Bundling Baru - BLNTRK Outdoor</x-slot:title>

    <div class="max-w-4xl mx-auto pb-12" x-data="bundlingForm()">
        <div class="mb-6">
            <a href="{{ route('admin.paket-bundling.index') }}"
               class="inline-flex items-center gap-1.5 text-xs font-bold text-stone-500 hover:text-[#0e1b15] transition mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Daftar Paket</span>
            </a>
            <h1 class="text-2xl font-extrabold text-[#14201a] tracking-tight bl-d">Tambah Paket Bundling Baru</h1>
            <p class="text-xs text-stone-500 mt-0.5">Susun kombinasi alat outdoor siap sewa (misal Paket 1, 2, atau 3) dengan diskon harga sewa harian</p>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                <strong class="block font-bold mb-1">Periksa kembali isian formulir:</strong>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.paket-bundling.store') }}" class="space-y-6">
            @csrf

            <!-- Kartu Info Utama Paket -->
            <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs p-6 space-y-5">
                <h2 class="text-sm font-extrabold text-[#0e1b15] border-b border-stone-100 pb-3 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-[#5edd5b]/20 text-stone-800 flex items-center justify-center text-xs font-black">1</span>
                    <span>Informasi & Identitas Paket</span>
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="nomor_paket" class="block text-[11px] font-bold text-stone-600 uppercase tracking-wider mb-1">
                            Nomor Paket <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" id="nomor_paket" name="nomor_paket" min="1" max="20"
                               value="{{ old('nomor_paket', 1) }}" required
                               class="w-full text-xs rounded-xl border-stone-200 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-2xs">
                        <span class="text-[10px] text-stone-400 mt-1 block">Contoh: 1 (Paket 1), 2, 3</span>
                    </div>

                    <div>
                        <label for="kode_paket" class="block text-[11px] font-bold text-stone-600 uppercase tracking-wider mb-1">
                            Kode Paket <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="kode_paket" name="kode_paket" value="{{ old('kode_paket', 'PKT-') }}" required
                               placeholder="PKT-01"
                               class="w-full text-xs font-mono uppercase rounded-xl border-stone-200 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-2xs">
                        <span class="text-[10px] text-stone-400 mt-1 block">Kode unik sistem</span>
                    </div>

                    <div>
                        <label for="kapasitas_orang" class="block text-[11px] font-bold text-stone-600 uppercase tracking-wider mb-1">
                            Kapasitas Orang <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="kapasitas_orang" name="kapasitas_orang" value="{{ old('kapasitas_orang', '1 Orang') }}" required
                               placeholder="1 Orang / 2-3 Orang / 4-6 Orang"
                               class="w-full text-xs rounded-xl border-stone-200 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-2xs">
                    </div>
                </div>

                <div>
                    <label for="nama" class="block text-[11px] font-bold text-stone-600 uppercase tracking-wider mb-1">
                        Nama Paket Bundling <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required
                           placeholder="Contoh: Solo Backpacker Explorer / Duo Mountain Adventure"
                           class="w-full text-xs rounded-xl border-stone-200 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-2xs">
                </div>

                <div>
                    <label for="deskripsi" class="block text-[11px] font-bold text-stone-600 uppercase tracking-wider mb-1">
                        Deskripsi / Highlight Keunggulan Paket
                    </label>
                    <textarea id="deskripsi" name="deskripsi" rows="3"
                              placeholder="Jelaskan untuk siapa paket ini cocok, kelengkapan utamanya, dan keuntungannya..."
                              class="w-full text-xs rounded-xl border-stone-200 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-2xs">{{ old('deskripsi') }}</textarea>
                </div>

                <!-- Tarif Sewa & Status -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label for="harga_paket_per_hari" class="block text-[11px] font-bold text-stone-600 uppercase tracking-wider mb-1">
                            Harga Sewa Paket / Hari (Rp) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs font-bold text-stone-400">Rp</span>
                            <input type="number" id="harga_paket_per_hari" name="harga_paket_per_hari"
                                   x-model.number="hargaPaket"
                                   min="0" step="500" required
                                   placeholder="79000"
                                   class="w-full pl-10 pr-4 py-2 text-xs rounded-xl border-stone-200 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-2xs font-bold text-stone-900">
                        </div>
                        <span class="text-[10px] text-stone-400 mt-1 block">Tarif bundling khusus yang ditawarkan ke pelanggan</span>
                    </div>

                    <div class="flex items-center gap-3 pt-6">
                        <label class="relative flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_aktif" value="1" {{ old('is_aktif', '1') ? 'checked' : '' }}
                                   class="rounded text-[#0e1b15] focus:ring-[#0e1b15] h-4 w-4">
                            <span class="text-xs font-bold text-stone-800">Aktifkan & Tampilkan di Katalog Publik</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Kartu Repeater Alat Bundling -->
            <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                    <h2 class="text-sm font-extrabold text-[#0e1b15] flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-[#5edd5b]/20 text-stone-800 flex items-center justify-center text-xs font-black">2</span>
                        <span>Komposisi Alat Dalam Paket</span>
                    </h2>

                    <button type="button" @click="addItem()"
                            class="px-3 py-1.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs font-bold transition inline-flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Tambah Alat</span>
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="p-3.5 rounded-xl bg-stone-50 border border-stone-200/70 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                            <div class="flex-1">
                                <label class="block text-[10px] font-bold text-stone-500 uppercase mb-1">Pilih Alat Outdoor</label>
                                <select :name="`items[${index}][alat_id]`" x-model="item.alat_id" required
                                        class="w-full text-xs rounded-xl border-stone-200 focus:border-[#0e1b15] focus:ring-[#0e1b15] bg-white">
                                    <option value="">-- Pilih Alat --</option>
                                    <template x-for="alat in masterAlat" :key="alat.id">
                                        <option :value="alat.id" x-text="`${alat.nama} (${alat.kategori}) — Rp ${formatRupiah(alat.harga)}/hari`"></option>
                                    </template>
                                </select>
                            </div>

                            <div class="w-full sm:w-28">
                                <label class="block text-[10px] font-bold text-stone-500 uppercase mb-1">Kuantitas</label>
                                <input type="number" :name="`items[${index}][jumlah]`" x-model.number="item.jumlah"
                                       min="1" max="50" required
                                       class="w-full text-xs rounded-xl border-stone-200 focus:border-[#0e1b15] focus:ring-[#0e1b15] bg-white text-center font-bold">
                            </div>

                            <div class="w-full sm:w-36 text-right sm:pt-4">
                                <div class="text-[11px] font-bold text-stone-800" x-text="`Rp ${formatRupiah(getSubtotal(item))}`"></div>
                                <span class="text-[9px] text-stone-400 block">subtotal / hari</span>
                            </div>

                            <div class="sm:pt-4 flex justify-end">
                                <button type="button" @click="removeItem(index)" :disabled="items.length <= 1"
                                        class="p-2 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 disabled:opacity-30 disabled:cursor-not-allowed transition"
                                        title="Hapus baris alat">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Live Summary Kalkulasi Diskon -->
                <div class="mt-4 p-4 rounded-xl bg-emerald-50/70 border border-emerald-200/80 grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                    <div>
                        <span class="block text-stone-500 text-[11px]">Total Harga Asli (Eceran):</span>
                        <span class="text-sm font-extrabold text-stone-800" x-text="`Rp ${formatRupiah(totalHargaAsli())}`"></span>
                    </div>
                    <div>
                        <span class="block text-stone-500 text-[11px]">Harga Paket Ditawarkan:</span>
                        <span class="text-sm font-extrabold text-emerald-800" x-text="`Rp ${formatRupiah(hargaPaket || 0)}`"></span>
                    </div>
                    <div>
                        <span class="block text-stone-500 text-[11px]">Penghematan Pelanggan:</span>
                        <span class="text-sm font-extrabold text-emerald-600" x-text="`Rp ${formatRupiah(hematNominal())} (${persenHemat()}%)`"></span>
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('admin.paket-bundling.index') }}"
                   class="px-5 py-2.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold transition">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-extrabold shadow-sm transition inline-flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Paket Bundling</span>
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function bundlingForm() {
            return {
                masterAlat: @json($alatList->map(fn($a) => [
                    'id' => $a->id,
                    'nama' => $a->nama,
                    'kode' => $a->kode_alat,
                    'harga' => (float)$a->harga_per_hari,
                    'kategori' => $a->kategori?->nama ?? '-'
                ])),
                hargaPaket: {{ old('harga_paket_per_hari', 0) }},
                items: [
                    { alat_id: '{{ old("items.0.alat_id", "") }}', jumlah: {{ old("items.0.jumlah", 1) }} }
                ],
                addItem() {
                    this.items.push({ alat_id: '', jumlah: 1 });
                },
                removeItem(idx) {
                    if (this.items.length > 1) {
                        this.items.splice(idx, 1);
                    }
                },
                getAlat(id) {
                    return this.masterAlat.find(a => a.id == id);
                },
                getSubtotal(item) {
                    const alat = this.getAlat(item.alat_id);
                    return alat ? (alat.harga * (item.jumlah || 1)) : 0;
                },
                totalHargaAsli() {
                    return this.items.reduce((sum, it) => sum + this.getSubtotal(it), 0);
                },
                hematNominal() {
                    const asli = this.totalHargaAsli();
                    return Math.max(0, asli - (this.hargaPaket || 0));
                },
                persenHemat() {
                    const asli = this.totalHargaAsli();
                    if (!asli || asli <= 0) return 0;
                    return Math.max(0, Math.round(((asli - (this.hargaPaket || 0)) / asli) * 100));
                },
                formatRupiah(angka) {
                    return Number(angka || 0).toLocaleString('id-ID');
                }
            }
        }
    </script>
    @endpush
</x-layouts.admin>

