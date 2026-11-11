                    <!-- Card Identitas Penyewa -->
                    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-stone-200/90 shadow-sm">
                        <h3 class="text-sm font-extrabold text-[#0e1b15] font-urbanist uppercase tracking-wider mb-4 pb-2 border-b border-stone-100">
                            Data Penyewa
                        </h3>

                        <div class="space-y-2 text-xs">
                            <div>
                                <span class="text-stone-400 block text-[11px]">Nama Lengkap:</span>
                                <div class="font-bold text-stone-900 text-sm font-urbanist">{{ $pesanan->pelanggan->nama_lengkap }}</div>
                            </div>
                            <div>
                                <span class="text-stone-400 block text-[11px]">Nomor WhatsApp:</span>
                                <div class="font-mono text-stone-800 font-semibold">{{ $pesanan->pelanggan->no_hp }}</div>
                            </div>
                            <div>
                                <span class="text-stone-400 block text-[11px]">Alamat:</span>
                                <div class="text-stone-600 leading-relaxed">{{ $pesanan->pelanggan->alamat }}</div>
                            </div>
                        </div>
                    </div>
