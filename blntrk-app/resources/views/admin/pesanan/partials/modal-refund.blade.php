@can('catatRefund', $pesanan)
        {{-- Mencatat refund atas pesanan yang sudah dibatalkan (mis. no-show yang dananya semula dinyatakan hangus) --}}
        <div x-show="showModalRefund"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.away="showModalRefund = false"
                 class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-stone-200/90 relative">

                <div class="flex items-center justify-between pb-4 border-b border-stone-100 mb-5">
                    <div>
                        <h3 class="text-base font-extrabold text-[#14201a] bl-d">Catat Refund Pesanan</h3>
                        <span class="text-[11px] text-stone-500">Resi {{ $pesanan->no_resi }}</span>
                    </div>
                    <button type="button" @click="showModalRefund = false" class="text-stone-400 hover:text-stone-700 p-1 rounded-lg" aria-label="Tutup">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.pesanan.refund', $pesanan->id) }}" class="space-y-4">
                    @csrf

                    <div class="p-3 rounded-xl bg-stone-50 border border-stone-200 text-xs text-stone-700 space-y-1">
                        <div class="flex justify-between"><span>Total dibayar</span><span class="font-mono font-bold">Rp{{ number_format($pesanan->total_dibayar, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between"><span>Sudah dikembalikan</span><span class="font-mono font-bold">Rp{{ number_format($pesanan->total_refund, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between border-t border-stone-200 pt-1"><span>Dana hangus saat ini</span><span class="font-mono font-black">Rp{{ number_format($pesanan->dana_hangus, 0, ',', '.') }}</span></div>
                    </div>

                    <div>
                        <label for="jumlah_refund" class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1">Jumlah Dikembalikan (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" id="jumlah_refund" name="jumlah_refund" required min="1" max="{{ (int) $pesanan->dana_hangus }}" step="1"
                               value="{{ old('jumlah_refund', (int) $pesanan->dana_hangus) }}"
                               class="w-full text-xs font-mono font-bold rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs">
                        @error('jumlah_refund') <span class="text-xs text-rose-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="metode_refund" class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1">Dikembalikan Lewat <span class="text-rose-500">*</span></label>
                        <select id="metode_refund" name="metode_refund" required
                                class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs">
                            @foreach(\App\Enums\MetodePembayaran::cases() as $m)
                                <option value="{{ $m->value }}" @selected(old('metode_refund', 'tunai') === $m->value)>{{ $m->label() }}</option>
                            @endforeach
                        </select>
                        @error('metode_refund') <span class="text-xs text-rose-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="catatan_refund" class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1">Catatan (Opsional)</label>
                        <input type="text" id="catatan_refund" name="catatan_refund" maxlength="255" value="{{ old('catatan_refund') }}"
                               class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs">
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-2 border-t border-stone-100">
                        <button type="button" @click="showModalRefund = false"
                                class="px-4 py-2.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold transition">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-5 py-2.5 rounded-xl bg-[#0e1b15] hover:bg-[#1a2e24] text-[#5edd5b] text-xs font-extrabold shadow-sm transition">
                            Simpan Refund
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endcan
