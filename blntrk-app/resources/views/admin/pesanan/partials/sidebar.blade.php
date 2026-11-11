                <!-- Profil Pelanggan -->
                <div class="bg-white p-6 rounded-2xl border border-stone-200/90 shadow-xs">
                    <h2 class="text-sm font-extrabold text-[#14201a] bl-d mb-4 pb-2 border-b border-stone-100 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#5edd5b]"></span>
                        Informasi Penyewa
                    </h2>

                    <div class="space-y-3 text-xs">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-stone-400 block">Nama Lengkap</span>
                            <div class="font-bold text-stone-900 text-sm bl-d">{{ $pesanan->pelanggan->nama_lengkap }}</div>
                            @if($pesanan->pelanggan->is_walk_in)
                                <span class="inline-block mt-0.5 px-2 py-0.5 rounded-md bg-stone-100 text-[10px] font-bold text-stone-600">Pelanggan Walk-in</span>
                            @else
                                <span class="inline-block mt-0.5 px-2 py-0.5 rounded-md bg-[#5edd5b]/20 text-[10px] font-bold text-[#0e1b15]">Akun Daring</span>
                            @endif
                        </div>

                        <div>
                            <span class="text-[10px] uppercase font-bold text-stone-400 block">Kontak WhatsApp</span>
                            <div class="font-mono text-stone-800 font-bold">{{ $pesanan->pelanggan->no_hp }}</div>
                        </div>

                        <div>
                            <span class="text-[10px] uppercase font-bold text-stone-400 block">Alamat Domisili</span>
                            <div class="text-stone-600 leading-relaxed">{{ $pesanan->pelanggan->alamat }}</div>
                        </div>

                        <div class="pt-3 border-t border-stone-100">
                            <a href="{{ route('admin.pelanggan.show', $pesanan->pelanggan->id) }}"
                               class="text-xs font-bold text-[#4e7a5a] hover:text-[#1e3a2c] transition inline-flex items-center gap-1">
                                <span>Lihat Riwayat Pelanggan Ini</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Info Kontak Toko & Status Transaksi -->
                <div class="bg-[#0e1b15] p-6 rounded-2xl text-white shadow-xs">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#5edd5b] block mb-1">Status Transaksi Saat Ini</span>
                    <div class="text-lg font-black font-urbanist text-white">
                        {{ $pesanan->status->label() }}
                    </div>
                    @if($pesanan->status === \App\Enums\StatusPesanan::Menunggu)
                        <p class="text-xs text-stone-300 mt-2">
                            Penyewa belum melunasi biaya sewa. Silakan konfirmasi pembayaran di kasir toko sebelum menyerahkan alat.
                            @if($pesanan->batas_bayar)
                                Batas pembayaran {{ $pesanan->batas_bayar->format('d/m/Y H:i') }} WIB.
                            @endif
                        </p>
                    @elseif($pesanan->status === \App\Enums\StatusPesanan::Dibayar)
                        <p class="text-xs text-[#5edd5b] mt-2 font-bold">
                            Pembayaran telah lunas. Lakukan serah terima alat dan catat kartu identitas sebagai jaminan.
                        </p>
                    @elseif($pesanan->status === \App\Enums\StatusPesanan::Disewa)
                        <p class="text-xs text-sky-200 mt-2">
                            Unit perlengkapan sedang dibawa oleh penyewa. Jadwal kembali maksimal {{ $pesanan->tanggal_selesai->format('d/m/Y') }} pukul {{ $pesanan->jatuh_tempo?->format('H:i') ?? '12:00' }} WIB.
                        </p>
                    @elseif($pesanan->status === \App\Enums\StatusPesanan::Dibatalkan)
                        <p class="text-xs text-rose-300 mt-2">
                            Pesanan ini telah dibatalkan. Alasan: "{{ $pesanan->alasan_batal }}".
                            @if($pesanan->total_dibayar > 0)
                                Dana Rp{{ number_format($pesanan->total_dibayar, 0, ',', '.') }}:
                                dikembalikan Rp{{ number_format($pesanan->total_refund, 0, ',', '.') }},
                                hangus Rp{{ number_format($pesanan->dana_hangus, 0, ',', '.') }}.
                            @endif
                        </p>
                    @endif
                </div>
