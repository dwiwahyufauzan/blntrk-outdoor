                    <!-- Card Jadwal Sewa -->
                    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-stone-200/90 shadow-sm">
                        <h3 class="text-sm font-extrabold text-[#0e1b15] font-urbanist uppercase tracking-wider mb-4 pb-2 border-b border-stone-100">
                            Jadwal & Waktu Sewa
                        </h3>

                        <div class="space-y-3 text-xs">
                            <div>
                                <span class="text-stone-400 block text-[11px] mb-0.5">Tanggal Mulai Ambil:</span>
                                <div class="font-extrabold text-stone-900 text-sm">
                                    {{ $pesanan->tanggal_mulai->translatedFormat('l, d F Y') }}
                                </div>
                            </div>

                            <div>
                                <span class="text-stone-400 block text-[11px] mb-0.5">Batas Pengembalian:</span>
                                <div class="font-extrabold text-stone-900 text-sm">
                                    {{ $pesanan->tanggal_selesai->translatedFormat('l, d F Y') }}
                                </div>
                                <span class="text-rose-600 font-bold text-[11px] block mt-0.5">Maksimal pukul 12:00 WIB</span>
                            </div>

                            <div class="pt-2 border-t border-stone-100 flex justify-between items-center">
                                <span class="text-stone-500">Durasi Penyewaan:</span>
                                <span class="font-extrabold text-[#0e1b15] bg-[#5edd5b]/20 px-2.5 py-1 rounded-xl">
                                    {{ $pesanan->durasi_hari }} Hari
                                </span>
                            </div>
                        </div>
                    </div>
