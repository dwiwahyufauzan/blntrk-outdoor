        <!-- =================== MODAL: BATALKAN PESANAN =================== -->
        <div x-show="showModalBatal"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.away="showModalBatal = false"
                 class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-stone-200/90 relative">
                
                <div class="flex items-center justify-between pb-4 border-b border-stone-100 mb-5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-[#14201a] bl-d">Batalkan Pesanan</h3>
                            <span class="text-[11px] text-stone-500 font-mono">Resi {{ $pesanan->no_resi }}</span>
                        </div>
                    </div>
                    <button type="button" @click="showModalBatal = false" class="text-stone-400 hover:text-stone-700 p-1 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('pesanan.batal', $pesanan->no_resi) }}" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    
                    <p class="text-xs text-stone-600 leading-relaxed">
                        Apakah Anda yakin ingin membatalkan pesanan ini? Kuota unit alat yang telah Anda pesan akan dilepaskan kembali ke katalog publik agar dapat disewa oleh pengguna lain.
                    </p>

                    <div>
                        <label for="alasan_batal_pelanggan" class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1">
                            Alasan Pembatalan <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="alasan_batal_pelanggan" name="alasan_batal" rows="3" required placeholder="Tuliskan alasan pembatalan Anda..."
                                  class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs">{{ old('alasan_batal') }}</textarea>
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-2 border-t border-stone-100">
                        <button type="button" @click="showModalBatal = false"
                                class="px-4 py-2.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold transition">
                            Kembali
                        </button>
                        <button type="submit"
                                class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-extrabold shadow-sm transition">
                            Ya, Batalkan Pesanan
                        </button>
                    </div>
                </form>
            </div>
        </div>
