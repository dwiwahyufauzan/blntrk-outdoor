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

                    @if($pesanan->status === \App\Enums\StatusPesanan::Dibayar && $pesanan->pembayaran->isNotEmpty())
                        <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200/80 space-y-3">
                            <div class="text-[11px] text-amber-900 leading-relaxed">
                                <strong>Pesanan ini sudah dibayar Rp{{ number_format($pesanan->total_dibayar, 0, ',', '.') }}.</strong>
                                Tentukan nasib dana pelanggan agar laporan pendapatan sesuai dengan kas toko.
                            </div>

                            <fieldset>
                                <legend class="sr-only">Penanganan dana</legend>
                                <div class="grid grid-cols-2 gap-2">
                                    <label class="border rounded-xl p-2.5 cursor-pointer text-center text-xs transition"
                                           :class="penangananDana === 'refund' ? 'border-[#0e1b15] bg-white font-bold shadow-xs' : 'border-amber-200 bg-white/60 text-stone-600'">
                                        <input type="radio" name="penanganan_dana" value="refund" x-model="penangananDana" class="sr-only" required>
                                        Dikembalikan (Refund)
                                    </label>
                                    <label class="border rounded-xl p-2.5 cursor-pointer text-center text-xs transition"
                                           :class="penangananDana === 'hangus' ? 'border-[#0e1b15] bg-white font-bold shadow-xs' : 'border-amber-200 bg-white/60 text-stone-600'">
                                        <input type="radio" name="penanganan_dana" value="hangus" x-model="penangananDana" class="sr-only" required>
                                        Hangus (Tidak Dikembalikan)
                                    </label>
                                </div>
                                @error('penanganan_dana') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </fieldset>

                            <div x-show="penangananDana === 'refund'" x-cloak class="space-y-3">
                                <div>
                                    <label for="jumlah_refund_batal" class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1">Jumlah Dikembalikan (Rp)</label>
                                    <input type="number" id="jumlah_refund_batal" name="jumlah_refund" min="1" max="{{ (int) $pesanan->total_dibayar }}" step="1"
                                           value="{{ old('jumlah_refund', (int) $pesanan->total_dibayar) }}"
                                           :required="penangananDana === 'refund'"
                                           class="w-full text-xs font-mono font-bold rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs">
                                    <span class="text-[10px] text-stone-500 mt-0.5 block">Selisih dari Rp{{ number_format($pesanan->total_dibayar, 0, ',', '.') }} dianggap hangus.</span>
                                    @error('jumlah_refund') <span class="text-xs text-rose-600 font-semibold">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="metode_refund_batal" class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1">Dikembalikan Lewat</label>
                                    <select id="metode_refund_batal" name="metode_refund" :required="penangananDana === 'refund'"
                                            class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs">
                                        @foreach(\App\Enums\MetodePembayaran::cases() as $m)
                                            <option value="{{ $m->value }}" @selected(old('metode_refund', 'tunai') === $m->value)>{{ $m->label() }}</option>
                                        @endforeach
                                    </select>
                                    @error('metode_refund') <span class="text-xs text-rose-600 font-semibold">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="catatan_refund_batal" class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1">Catatan Refund (Opsional)</label>
                                    <input type="text" id="catatan_refund_batal" name="catatan_refund" maxlength="255" value="{{ old('catatan_refund') }}"
                                           placeholder="Contoh: Transfer balik ke rekening pelanggan"
                                           class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs">
                                </div>
                            </div>
                        </div>
                    @endif

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
