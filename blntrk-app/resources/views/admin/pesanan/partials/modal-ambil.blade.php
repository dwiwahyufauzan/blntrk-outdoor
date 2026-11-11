        <!-- =================== MODAL: SERAH TERIMA & JAMINAN =================== -->
        <div x-show="showModalAmbil"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.away="showModalAmbil = false"
                 class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-stone-200/90 relative">
                
                <div class="flex items-center justify-between pb-4 border-b border-stone-100 mb-5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-sky-100 text-sky-800 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-[#14201a] bl-d">Konfirmasi Pengambilan & Jaminan</h3>
                            <span class="text-[11px] text-stone-500">Serah terima unit alat untuk resi {{ $pesanan->no_resi }}</span>
                        </div>
                    </div>
                    <button type="button" @click="showModalAmbil = false" class="text-stone-400 hover:text-stone-700 p-1 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.pesanan.ambil', $pesanan->id) }}" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-2">Jenis Kartu Identitas Fisik yang Dititipkan</label>
                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                            @foreach([
                                'ktp' => 'KTP',
                                'sim' => 'SIM',
                                'kartu_pelajar' => 'K. Pelajar',
                                'lainnya' => 'Lainnya',
                                'deposit' => 'Deposit Uang',
                            ] as $val => $lbl)
                                <label class="border rounded-xl p-2.5 cursor-pointer flex flex-col items-center justify-center text-center transition"
                                       :class="jenisJaminan === '{{ $val }}' ? 'border-[#0e1b15] bg-[#5edd5b]/15 text-[#0e1b15] font-extrabold' : 'border-stone-200 bg-white text-stone-600 hover:bg-stone-50'">
                                    <input type="radio" name="jenis_jaminan" value="{{ $val }}" x-model="jenisJaminan" class="sr-only">
                                    <span class="text-xs">{{ $lbl }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div x-show="jenisJaminan === 'deposit'" x-cloak>
                        <label for="nominal_deposit" class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1">
                            Nominal Deposit (Rp) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" id="nominal_deposit" name="nominal_deposit" min="1000" step="1000" :disabled="jenisJaminan !== 'deposit'" :required="jenisJaminan === 'deposit'"
                               value="{{ old('nominal_deposit') }}" placeholder="Contoh: 200000"
                               class="w-full text-xs font-mono rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs">
                        <p class="mt-1 text-[10px] text-stone-500">Uang dititipkan sebagai pengganti kartu identitas dan ditahan selama masih ada denda.</p>
                    </div>

                    <div x-show="jenisJaminan !== 'deposit'">
                        <label for="nomor_identitas" class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1">
                            Nomor Kartu Identitas Fisik <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="nomor_identitas" name="nomor_identitas" :required="jenisJaminan !== 'deposit'" :disabled="jenisJaminan === 'deposit'" placeholder="Contoh: 3204123456780001"
                               class="w-full text-xs font-mono rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs">
                        <div class="mt-1.5 p-2.5 rounded-xl bg-amber-50 border border-amber-200/70 text-[10px] text-amber-900 flex items-start gap-2">
                            <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Privasi NF09: Sistem hanya akan menyimpan 4 digit terakhir nomor identitas secara tersamar (contoh: <strong>****0001</strong>).</span>
                        </div>
                    </div>

                    <div>
                        <label for="keterangan_jaminan" class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1">Keterangan / Kondisi Jaminan (Opsional)</label>
                        <input type="text" id="keterangan_jaminan" name="keterangan" placeholder="Contoh: KTP atas nama Dwi, fisik mulus disimpan di box kasir..."
                               class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs">
                    </div>

                    <div class="pt-3 flex items-center justify-end gap-2 border-t border-stone-100">
                        <button type="button" @click="showModalAmbil = false"
                                class="px-4 py-2.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold transition">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-5 py-2.5 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-extrabold shadow-sm transition">
                            Serahkan Alat & Simpan Jaminan
                        </button>
                    </div>
                </form>
            </div>
        </div>
