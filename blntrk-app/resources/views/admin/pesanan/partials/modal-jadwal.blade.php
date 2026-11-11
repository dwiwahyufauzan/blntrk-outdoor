        <!-- =================== MODAL: UBAH JADWAL =================== -->
        @can('ubahJadwal', $pesanan)
        <div x-show="showModalJadwal" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.away="showModalJadwal = false"
                 class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-stone-200/90 relative">
                <h3 class="text-base font-extrabold text-[#14201a] bl-d">Ubah Jadwal Sewa</h3>
                <span class="text-[11px] text-stone-500 block mb-4">Resi {{ $pesanan->no_resi }} &bull; ketersediaan stok dicek ulang untuk tanggal baru</span>
                <form method="POST" action="{{ route('admin.pesanan.jadwal', $pesanan->id) }}" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="jadwal_mulai" class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1">Tanggal Mulai</label>
                            <input type="date" id="jadwal_mulai" name="tanggal_mulai" required min="{{ today()->toDateString() }}" value="{{ old('tanggal_mulai', $pesanan->tanggal_mulai->toDateString()) }}"
                                   class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs">
                        </div>
                        <div>
                            <label for="jadwal_selesai" class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1">Tanggal Selesai</label>
                            <input type="date" id="jadwal_selesai" name="tanggal_selesai" required value="{{ old('tanggal_selesai', $pesanan->tanggal_selesai->toDateString()) }}"
                                   class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs">
                        </div>
                    </div>
                    @if($pesanan->status === \App\Enums\StatusPesanan::Dibayar)
                        <p class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-[11px] text-amber-900">Pesanan sudah dibayar, sehingga durasi harus tetap {{ $pesanan->durasi_hari }} hari.</p>
                    @else
                        <p class="text-[11px] text-stone-500">Total sewa dihitung ulang sesuai durasi baru.</p>
                    @endif
                    <div class="pt-3 flex items-center justify-end gap-2 border-t border-stone-100">
                        <button type="button" @click="showModalJadwal = false" class="px-4 py-2.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold transition">Tutup</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-extrabold shadow-sm transition">Simpan Jadwal</button>
                    </div>
                </form>
            </div>
        </div>
        @endcan
