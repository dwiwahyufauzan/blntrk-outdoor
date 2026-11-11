                    <!-- Card Jaminan Identitas -->
                    @php
                        $jaminan = $pesanan->jaminan;
                        $estimasiTelat = $infoKeterlambatan ? (float) $infoKeterlambatan['jumlah'] : 0.0;
                        $adaDendaBelumLunas = $pesanan->denda()->where('status_bayar', 'belum_lunas')->exists() || $estimasiTelat > 0;
                        $nominalDendaTertunggak = $pesanan->denda()->where('status_bayar', 'belum_lunas')->sum('jumlah') + $estimasiTelat;
                    @endphp
                    <div class="bg-white p-5 rounded-2xl border border-stone-200/90 shadow-xs">
                        <div class="flex items-center justify-between pb-3 mb-3 border-b border-stone-100">
                            <h3 class="text-xs font-bold text-[#14201a] uppercase tracking-wider flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                                <span>Jaminan Kartu Identitas</span>
                            </h3>
                            @if($jaminan)
                                @if($jaminan->status === 'dititipkan')
                                    @if($adaDendaBelumLunas)
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-100 text-rose-800 border border-rose-300 animate-pulse">
                                            DITAHAN (DENDA AKTIF)
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-sky-100 text-sky-800">
                                            DITITIPKAN DI KASIR
                                        </span>
                                    @endif
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        DIKEMBALIKAN
                                    </span>
                                @endif
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-stone-100 text-stone-500">BELUM DITERIMA</span>
                            @endif
                        </div>

                        @if($jaminan)
                            <div class="space-y-2 text-xs">
                                <div class="flex justify-between py-1 border-b border-stone-100">
                                    <span class="text-stone-500">Jenis Jaminan:</span>
                                    <span class="font-bold text-stone-900">{{ $jaminan->label_jenis }}</span>
                                </div>
                                @unless($jaminan->is_deposit)
                                <div class="flex justify-between py-1 border-b border-stone-100">
                                    <span class="text-stone-500">No. Identitas:</span>
                                    <span class="font-mono font-black text-stone-800 tracking-wider">{{ $jaminan->nomor_identitas_samar }}</span>
                                </div>
                                @endunless
                                <div class="flex justify-between py-1 border-b border-stone-100">
                                    <span class="text-stone-500">Diterima Pada:</span>
                                    <span class="font-mono text-stone-800">{{ $jaminan->diterima_pada->format('d/m/Y H:i') }} WIB</span>
                                </div>
                                <div class="flex justify-between py-1">
                                    <span class="text-stone-500">Diterima Oleh:</span>
                                    <span class="font-bold text-[#14201a]">{{ $jaminan->penerima?->nama ?? 'Admin' }}</span>
                                </div>
                                @if($jaminan->keterangan)
                                    <div class="pt-2 text-[11px] text-stone-500 italic">
                                        Keterangan: "{{ $jaminan->keterangan }}"
                                    </div>
                                @endif

                                @if($jaminan->status === 'dititipkan')
                                    <div class="pt-3 border-t border-stone-100">
                                        @if($adaDendaBelumLunas)
                                            <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-[11px] leading-relaxed mb-2.5">
                                                <div class="font-extrabold flex items-center gap-1.5 mb-1 text-rose-900">
                                                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                    <span>Jaminan Ditahan (SOP Toko)</span>
                                                </div>
                                                <span>Terdapat denda aktif tertunggak sebesar <strong>Rp{{ number_format($nominalDendaTertunggak, 0, ',', '.') }}</strong>. Kartu identitas jaminan ditahan sampai seluruh denda dilunasi pelanggan.</span>
                                            </div>
                                            <button type="button" disabled
                                                    class="w-full px-3 py-2 rounded-xl bg-stone-100 text-stone-400 text-[11px] font-bold border border-stone-200 cursor-not-allowed flex items-center justify-center gap-1.5 opacity-80"
                                                    title="Lunasi denda terlebih dahulu">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                <span>Jaminan Terkunci (Denda Belum Lunas)</span>
                                            </button>
                                        @else
                                            <form method="POST" action="{{ route('admin.pesanan.jaminan.kembali', $pesanan->id) }}"
                                                  data-konfirmasi="primary"
                                                  data-judul="Kembalikan Kartu Identitas Jaminan"
                                                  data-pesan="Konfirmasi penyerahan kembali kartu identitas jaminan (KTP/SIM) kepada penyewa? Pastikan seluruh kewajiban telah terpenuhi."
                                                  data-tombol="Ya, Serahkan Kembali">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="w-full px-3 py-2 rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-800 text-[11px] font-bold border border-sky-200 transition cursor-pointer flex items-center justify-center gap-1.5">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    <span>Kembalikan Kartu Identitas</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @else
                                    <div class="pt-2 text-[10px] text-emerald-700 font-bold flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        <span>Dikembalikan: {{ $jaminan->dikembalikan_pada?->format('d/m/Y H:i') }}</span>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="py-4 text-center text-xs text-stone-400">
                                Jaminan fisik kartu identitas akan dicatat saat serah terima barang.
                            </div>
                        @endif
                    </div>
