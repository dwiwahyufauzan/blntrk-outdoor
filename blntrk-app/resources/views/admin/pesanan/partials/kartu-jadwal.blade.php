                <!-- Kartu Jadwal Sewa -->
                <div class="bg-white p-6 rounded-2xl border border-stone-200/90 shadow-xs">
                    <h2 class="text-sm font-extrabold text-[#14201a] bl-d mb-4 pb-2 border-b border-stone-100 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#5edd5b]"></span>
                        Waktu & Siklus Sewa
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                        <div class="p-3.5 rounded-xl bg-stone-50 border border-stone-200/80">
                            <span class="text-[10px] uppercase font-bold text-stone-400 block mb-1">Mulai Ambil</span>
                            <div class="font-bold text-stone-800 text-sm">{{ $pesanan->tanggal_mulai->format('d/m/Y') }}</div>
                            <span class="text-[11px] text-stone-500">Maks. 23:59 WIB</span>
                        </div>

                        <div class="p-3.5 rounded-xl bg-stone-50 border border-stone-200/80">
                            <span class="text-[10px] uppercase font-bold text-stone-400 block mb-1">Jatuh Tempo Kembali</span>
                            <div class="font-bold text-stone-800 text-sm">{{ $pesanan->tanggal_selesai->format('d/m/Y') }}</div>
                            <span class="text-[11px] text-stone-500">Pukul {{ $pesanan->jatuh_tempo?->format('H:i') ?? '12:00' }} WIB</span>
                        </div>

                        <div class="p-3.5 rounded-xl bg-stone-50 border border-stone-200/80">
                            <span class="text-[10px] uppercase font-bold text-stone-400 block mb-1">Total Durasi</span>
                            <div class="font-black text-sm text-[#0e1b15]">{{ $pesanan->durasi_hari }} Hari Sewa</div>
                            <span class="text-[11px] text-stone-500">x24 Jam Operasional</span>
                        </div>
                    </div>
                </div>
