                    <!-- Tombol Resi & Pembatalan di Bawah Card -->
                    <div class="space-y-2 pt-2">
                        <a href="{{ route('pesanan.resi', $pesanan->no_resi) }}"
                           class="w-full py-3 rounded-2xl bg-[#0e1b15] hover:bg-[#1e3a2c] text-[#5edd5b] text-xs font-black transition flex items-center justify-center gap-2 shadow-xs">
                            <svg class="w-4 h-4 text-[#5edd5b]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Buka Lembar Resi Resmi</span>
                        </a>

                        @can('batal', $pesanan)
                            <button type="button" @click="showModalBatal = true"
                                    class="w-full py-2.5 rounded-2xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold transition flex items-center justify-center gap-1.5 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Batalkan Pesanan Ini</span>
                            </button>
                        @endcan
                    </div>
