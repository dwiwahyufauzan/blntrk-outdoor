        <!-- =================== MODAL: PERPANJANG SEWA =================== -->
        @can('perpanjang', $pesanan)
        <div x-show="showModalPerpanjang" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.away="showModalPerpanjang = false"
                 class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-stone-200/90 relative">
                <h3 class="text-base font-extrabold text-[#14201a] bl-d">Perpanjang Sewa</h3>
                <span class="text-[11px] text-stone-500 block mb-4">Resi {{ $pesanan->no_resi }} &bull; selesai saat ini {{ $pesanan->tanggal_selesai->format('d/m/Y') }}, tarif harian Rp{{ number_format($pesanan->tarif_harian, 0, ',', '.') }}</span>
                <form method="POST" action="{{ route('admin.pesanan.perpanjang', $pesanan->id) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="perpanjang_selesai" class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1">Tanggal Selesai Baru</label>
                        <input type="date" id="perpanjang_selesai" name="tanggal_selesai" required min="{{ $pesanan->tanggal_selesai->copy()->addDay()->toDateString() }}" value="{{ old('tanggal_selesai') }}"
                               class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs">
                    </div>
                    <div>
                        <label for="perpanjang_metode" class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1">Pembayaran Tambahan</label>
                        <select id="perpanjang_metode" name="metode" required class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs">
                            <option value="tunai">Tunai di Toko</option>
                            <option value="transfer">Transfer Bank Manual</option>
                        </select>
                        <p class="mt-1 text-[10px] text-stone-500">Selisih biaya dihitung otomatis (termasuk diskon paket) dan dicatat sebagai pembayaran perpanjangan. Stok dicek pada hari tambahan.</p>
                    </div>
                    <div>
                        <label for="perpanjang_catatan" class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1">Catatan (Opsional)</label>
                        <input type="text" id="perpanjang_catatan" name="catatan" maxlength="255" value="{{ old('catatan') }}"
                               class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs">
                    </div>
                    <div class="pt-3 flex items-center justify-end gap-2 border-t border-stone-100">
                        <button type="button" @click="showModalPerpanjang = false" class="px-4 py-2.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold transition">Tutup</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-extrabold shadow-sm transition">Perpanjang & Catat Pembayaran</button>
                    </div>
                </form>
            </div>
        </div>
        @endcan
