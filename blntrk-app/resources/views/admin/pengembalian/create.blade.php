<x-layouts.admin>
    <x-slot:title>Proses Pengembalian #{{ $pesanan->no_resi }} - BLNTRK Outdoor</x-slot:title>

    <div x-data="pengembalianForm({
        pesananId: {{ $pesanan->id }},
        waktuKembaliAwal: '{{ $waktuSekarang->format('Y-m-d\TH:i') }}',
        tarifHarianTotal: {{ (float) $pesanan->detail->sum(fn ($d) => $d->harga_per_hari * $d->jumlah) }},
        items: [
            @foreach($pesanan->detail as $item)
            {
                id: {{ $item->id }},
                nama: '{{ addslashes($item->alat->nama) }}',
                jumlah: {{ $item->jumlah }},
                hargaPerHari: {{ $item->harga_per_hari }},
                hargaBeli: {{ (float) ($item->alat->harga_beli ?? 0) }},
                baik: {{ (int) old('baris.'.$item->id.'.baik', $item->jumlah) }},
                rusakRingan: {{ (int) old('baris.'.$item->id.'.rusak_ringan', 0) }},
                rusakBerat: {{ (int) old('baris.'.$item->id.'.rusak_berat', 0) }},
                hilang: {{ (int) old('baris.'.$item->id.'.hilang', 0) }},
                kategoriDendaId: '{{ old('baris.'.$item->id.'.kategori_denda_id', '') }}',
                biaya: {{ (float) old('baris.'.$item->id.'.biaya', 0) }},
                catatan: '{{ addslashes(old('baris.'.$item->id.'.catatan', '')) }}'
            },
            @endforeach
        ],
        dendaAwal: {{ (float) $telat['jumlah'] }},
        hariTelatAwal: {{ (int) $telat['hari'] }}
    })" class="max-w-5xl mx-auto pb-12">

        <!-- Navigasi Kembali -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('admin.pesanan.show', $pesanan->id) }}" class="text-xs font-bold text-stone-500 hover:text-[#0e1b15] transition inline-flex items-center gap-1.5 mb-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali ke Detail Pesanan</span>
                </a>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-[#14201a] tracking-tight bl-d">
                        Form Pengembalian Alat
                    </h1>
                    <span class="px-2.5 py-1 rounded-full text-xs font-mono font-bold bg-[#0e1b15] text-[#5edd5b]">
                        {{ $pesanan->no_resi }}
                    </span>
                </div>
                <p class="text-xs text-stone-500 mt-1">
                    Penyewa: <strong class="text-stone-800">{{ $pesanan->pelanggan->nama_lengkap }}</strong> &bull; Jatuh tempo: {{ $pesanan->jatuh_tempo->format('d/m/Y H:i') }} WIB
                </p>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 text-rose-800 border border-rose-200 text-xs">
                <div class="font-bold mb-1 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Terdapat kesalahan pada input pengembalian:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 pl-1">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.pesanan.pengembalian.store', $pesanan->id) }}" @submit="return validasiSebelumKirim($event)" class="space-y-6">
            @csrf

            <!-- Panel Waktu Pengembalian & Preview Denda Keterlambatan -->
            <div class="bg-white p-6 rounded-2xl border border-stone-200/90 shadow-xs">
                <h2 class="text-sm font-extrabold text-[#14201a] bl-d mb-4 pb-2 border-b border-stone-100 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#5edd5b]"></span>
                    Waktu Pengembalian & Estimasi Keterlambatan
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label for="dikembalikan_pada" class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">
                            Waktu Diterima di Toko <span class="text-rose-500">*</span>
                        </label>
                        <input type="datetime-local" id="dikembalikan_pada" name="dikembalikan_pada"
                               x-model="waktuKembali"
                               @change="cekKeterlambatan()"
                               required
                               class="w-full text-xs font-mono rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs">
                        <span class="text-[10px] text-stone-400 mt-1 block">Toleransi pengembalian: 60 menit dari jatuh tempo.</span>
                    </div>

                    <div class="p-3.5 rounded-xl border" :class="dendaTelat > 0 ? 'border-rose-200 bg-rose-50/50' : 'border-stone-200 bg-stone-50/50'">
                        <span class="text-[10px] uppercase font-bold text-stone-400 block mb-1">Status Keterlambatan</span>
                        <div class="font-bold text-sm" :class="dendaTelat > 0 ? 'text-rose-800' : 'text-emerald-700'" x-text="hariTelat > 0 ? 'Terlambat ' + hariTelat + ' Hari' : 'Tepat Waktu / Dalam Toleransi'"></div>
                        <span class="text-[10px] text-stone-500 mt-0.5 block">Tarif per hari: Rp{{ number_format($pesanan->detail->sum(fn ($d) => $d->harga_per_hari * $d->jumlah), 0, ',', '.') }}</span>
                    </div>

                    <div class="p-3.5 rounded-xl border" :class="dendaTelat > 0 ? 'border-rose-200 bg-rose-50/50' : 'border-stone-200 bg-stone-50/50'">
                        <span class="text-[10px] uppercase font-bold text-stone-400 block mb-1">Denda Keterlambatan</span>
                        <div class="font-mono font-black text-lg" :class="dendaTelat > 0 ? 'text-rose-700' : 'text-stone-800'" x-text="formatRupiah(dendaTelat)"></div>
                        <span class="text-[10px] text-stone-400 mt-0.5 block">Otomatis terhitung dari sistem</span>
                    </div>
                </div>
            </div>

            <!-- Tabel Pengecekan Kondisi Fisik Alat (A13) -->
            <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-stone-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-extrabold text-[#14201a] bl-d flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#5edd5b]"></span>
                            Pemeriksaan Kondisi Fisik Perlengkapan
                        </h2>
                        <p class="text-xs text-stone-500 mt-0.5">Pastikan jumlah total kondisi (Baik + Rusak + Hilang) sama dengan jumlah unit yang disewa</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-stone-50/80 text-stone-500 uppercase text-[10px] font-bold border-b border-stone-200 tracking-wider">
                            <tr>
                                <th class="py-3 px-4 w-40">Nama Alat</th>
                                <th class="py-3 px-2 text-center w-14">Disewa</th>
                                <th class="py-3 px-2 text-center w-16">Baik</th>
                                <th class="py-3 px-2 text-center w-16">R. Ringan</th>
                                <th class="py-3 px-2 text-center w-16">R. Berat</th>
                                <th class="py-3 px-2 text-center w-16">Hilang</th>
                                <th class="py-3 px-3 w-44">Kategori Denda</th>
                                <th class="py-3 px-3 w-28 text-right">Biaya Kerusakan</th>
                                <th class="py-3 px-4 w-36">Catatan Fisik</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            <template x-for="(item, index) in items" :key="item.id">
                                <tr :class="itemTotalKondisi(item) !== item.jumlah ? 'bg-rose-50/40' : 'hover:bg-stone-50/50'">
                                    <!-- Nama Alat -->
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-stone-900 bl-d text-xs" x-text="item.nama"></div>
                                        <div class="text-[10px] text-stone-400 font-mono">Tarif: <span x-text="formatRupiah(item.hargaPerHari)"></span>/hari</div>
                                    </td>

                                    <!-- Jumlah Disewa -->
                                    <td class="py-3.5 px-2 text-center font-black text-stone-800 text-xs">
                                        <span x-text="item.jumlah"></span>
                                    </td>

                                    <!-- Baik -->
                                    <td class="py-3.5 px-2">
                                        <input type="number" :name="'baris[' + item.id + '][baik]'" min="0" :max="item.jumlah"
                                               x-model.number="item.baik"
                                               class="w-full text-center text-xs py-1.5 px-1 rounded-lg border-stone-300 focus:border-[#0e1b15] font-bold text-emerald-800 bg-emerald-50/40">
                                    </td>

                                    <!-- Rusak Ringan -->
                                    <td class="py-3.5 px-2">
                                        <input type="number" :name="'baris[' + item.id + '][rusak_ringan]'" min="0" :max="item.jumlah"
                                               x-model.number="item.rusakRingan"
                                               class="w-full text-center text-xs py-1.5 px-1 rounded-lg border-stone-300 focus:border-[#0e1b15] font-bold text-amber-800 bg-amber-50/40">
                                    </td>

                                    <!-- Rusak Berat -->
                                    <td class="py-3.5 px-2">
                                        <input type="number" :name="'baris[' + item.id + '][rusak_berat]'" min="0" :max="item.jumlah"
                                               x-model.number="item.rusakBerat"
                                               class="w-full text-center text-xs py-1.5 px-1 rounded-lg border-stone-300 focus:border-[#0e1b15] font-bold text-orange-800 bg-orange-50/40">
                                    </td>

                                    <!-- Hilang -->
                                    <td class="py-3.5 px-2">
                                        <input type="number" :name="'baris[' + item.id + '][hilang]'" min="0" :max="item.jumlah"
                                               x-model.number="item.hilang"
                                               @input="onUbahHilang(item)"
                                               class="w-full text-center text-xs py-1.5 px-1 rounded-lg border-stone-300 focus:border-[#0e1b15] font-bold text-rose-800 bg-rose-50/40">
                                        <template x-if="item.hargaBeli > 0">
                                            <span class="text-[9px] text-stone-400 font-mono block text-center mt-0.5 truncate" :title="'Harga beli: Rp' + Number(item.hargaBeli).toLocaleString('id-ID')">
                                                @<span x-text="formatRupiah(item.hargaBeli).replace('Rp', '')"></span>
                                            </span>
                                        </template>
                                    </td>

                                    <!-- Kategori Denda -->
                                    <td class="py-3.5 px-3">
                                        <select :name="'baris[' + item.id + '][kategori_denda_id]'"
                                                x-model="item.kategoriDendaId"
                                                @change="ubahKategori(item)"
                                                class="w-full text-[11px] py-1.5 px-2 rounded-lg border-stone-300 focus:border-[#0e1b15] bg-white">
                                            <option value="">-- Pilih Kategori --</option>
                                            @foreach($kategoriDendaList as $kat)
                                                <option value="{{ $kat->id }}">
                                                    {{ $kat->nama }} ({{ $kat->kode === 'HILANG' ? 'Sesuai Harga Beli' : ($kat->tarif_standar > 0 ? 'Rp'.number_format($kat->tarif_standar, 0, ',', '.') : 'Fleksibel') }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <!-- Biaya Kerusakan / Ganti Rugi -->
                                    <td class="py-3.5 px-3">
                                        <input type="number" :name="'baris[' + item.id + '][biaya]'" min="0" step="1000"
                                               x-model.number="item.biaya"
                                               placeholder="0"
                                               class="w-full text-right text-xs py-1.5 px-2 font-mono rounded-lg border-stone-300 focus:border-[#0e1b15]">
                                        <template x-if="item.hilang > 0 && item.hargaBeli > 0">
                                            <span class="text-[9px] text-rose-600 font-bold block text-right mt-0.5">
                                                Sesuai harga beli
                                            </span>
                                        </template>
                                    </td>

                                    <!-- Catatan -->
                                    <td class="py-3.5 px-4">
                                        <input type="text" :name="'baris[' + item.id + '][catatan]'"
                                               x-model="item.catatan"
                                               placeholder="Contoh: Frame bengkok..."
                                               class="w-full text-xs py-1.5 px-2 rounded-lg border-stone-300 focus:border-[#0e1b15]">
                                        <!-- Error hint jika jumlah kondisi beda -->
                                        <div x-show="itemTotalKondisi(item) !== item.jumlah" class="text-[10px] text-rose-600 font-bold mt-1">
                                            Total kondisi: <span x-text="itemTotalKondisi(item)"></span> / <span x-text="item.jumlah"></span>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Ringkasan Tagihan & Catatan Umum -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-stone-200/90 shadow-xs">
                    <label for="catatan_umum" class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">
                        Catatan Umum Pengembalian (Opsional)
                    </label>
                    <textarea id="catatan_umum" name="catatan" rows="3"
                              placeholder="Keterangan proses serah terima pengembalian, kondisi kebersihan alat, atau kesepakatan ganti rugi..."
                              class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs">{{ old('catatan') }}</textarea>
                </div>

                <!-- Kartu Total Tagihan Tambahan -->
                <div class="bg-[#0e1b15] text-white p-6 rounded-2xl shadow-xs flex flex-col justify-between">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-[#5edd5b] block mb-2">Ringkasan Biaya Tambahan</span>
                        
                        <div class="space-y-2 text-xs border-b border-[#1e3a2c] pb-3 mb-3">
                            <div class="flex justify-between">
                                <span class="text-stone-300">Denda Keterlambatan:</span>
                                <span class="font-mono font-bold" x-text="formatRupiah(dendaTelat)"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-stone-300">Biaya Kerusakan/Hilang:</span>
                                <span class="font-mono font-bold" x-text="formatRupiah(totalBiayaKerusakan())"></span>
                            </div>
                        </div>

                        <div class="flex justify-between items-baseline mb-1">
                            <span class="text-xs font-bold text-stone-300">Total Tagihan Baru:</span>
                            <span class="text-xl font-mono font-black text-[#5edd5b]" x-text="formatRupiah(dendaTelat + totalBiayaKerusakan())"></span>
                        </div>
                        <span class="text-[10px] text-stone-400 block mb-3">Denda belum lunas dapat dilunasi tunai/transfer di menu Denda.</span>

                        <!-- Notice Penahanan Jaminan (Foresta Notice) -->
                        <div class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/30 text-[11px] text-amber-200">
                            <div class="font-bold flex items-center gap-1.5 text-amber-400 mb-1">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span>Aturan Penahanan Jaminan</span>
                            </div>
                            <span class="leading-relaxed">Jika terdapat tagihan denda keterlambatan/kehilangan/kerusakan yang belum diselesaikan, kartu identitas jaminan (KTP/SIM) penyewa akan <strong>otomatis ditahan oleh sistem</strong> sampai seluruh denda dilunasi.</span>
                        </div>
                    </div>

                    <div class="pt-5 flex items-center gap-2">
                        <button type="submit"
                                :disabled="!isSemuaKondisiValid()"
                                class="w-full py-3 px-4 rounded-xl font-extrabold text-xs text-center transition flex items-center justify-center gap-2 shadow-sm cursor-pointer"
                                :class="isSemuaKondisiValid() ? 'bg-[#5edd5b] hover:bg-[#4ed04b] text-black' : 'bg-stone-700 text-stone-400 cursor-not-allowed'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan & Selesaikan Pengembalian</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function pengembalianForm(config) {
            return {
                pesananId: config.pesananId,
                waktuKembali: config.waktuKembaliAwal,
                items: config.items,
                dendaTelat: config.dendaAwal,
                hariTelat: config.hariTelatAwal,
                kategoriList: [
                    @foreach($kategoriDendaList as $k)
                    { id: '{{ $k->id }}', kode: '{{ $k->kode }}', nama: '{{ addslashes($k->nama) }}', tarif: {{ (float) $k->tarif_standar }} },
                    @endforeach
                ],

                onUbahHilang(item) {
                    if (item.hilang > 0) {
                        const katHilang = this.kategoriList.find(k => k.kode === 'HILANG');
                        if (katHilang) {
                            item.kategoriDendaId = katHilang.id;
                        }
                        if (item.hargaBeli > 0) {
                            item.biaya = item.hilang * item.hargaBeli;
                        }
                    } else if (item.hilang === 0 && item.rusakRingan === 0 && item.rusakBerat === 0) {
                        item.biaya = 0;
                        item.kategoriDendaId = '';
                    }
                },

                ubahKategori(item) {
                    if (item.kategoriDendaId) {
                        const kat = this.kategoriList.find(k => k.id == item.kategoriDendaId);
                        if (kat) {
                            if (kat.kode === 'HILANG' && item.hargaBeli > 0) {
                                const qty = item.hilang > 0 ? item.hilang : 1;
                                item.biaya = qty * item.hargaBeli;
                            } else if (kat.tarif > 0 && (!item.biaya || item.biaya === 0)) {
                                item.biaya = kat.tarif;
                            }
                        }
                    }
                },

                itemTotalKondisi(item) {
                    return (parseInt(item.baik) || 0) +
                           (parseInt(item.rusakRingan) || 0) +
                           (parseInt(item.rusakBerat) || 0) +
                           (parseInt(item.hilang) || 0);
                },

                isSemuaKondisiValid() {
                    return this.items.every(it => this.itemTotalKondisi(it) === it.jumlah);
                },

                totalBiayaKerusakan() {
                    return this.items.reduce((acc, it) => acc + (parseFloat(it.biaya) || 0), 0);
                },

                cekKeterlambatan() {
                    const url = `/admin/pesanan/${this.pesananId}/hitung-denda?waktu=` + encodeURIComponent(this.waktuKembali);
                    fetch(url)
                        .then(res => res.json())
                        .then(data => {
                            this.dendaTelat = data.jumlah;
                            this.hariTelat = data.hari;
                        })
                        .catch(err => console.error('Gagal menghitung denda:', err));
                },

                validasiSebelumKirim(e) {
                    if (!this.isSemuaKondisiValid()) {
                        e.preventDefault();
                        alert('Jumlah kondisi alat pada satu atau lebih item belum sesuai dengan jumlah yang disewa!');
                        return false;
                    }
                    return true;
                },

                formatRupiah(val) {
                    return 'Rp' + (Math.round(val) || 0).toLocaleString('id-ID');
                }
            };
        }
    </script>
    @endpush
</x-layouts.admin>
