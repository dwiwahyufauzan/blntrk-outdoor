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
