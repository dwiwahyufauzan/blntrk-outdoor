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
