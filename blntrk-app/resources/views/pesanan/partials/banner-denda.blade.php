            <!-- Banner Peringatan Jaminan Ditahan (Jika Ada Denda Belum Lunas atau Sedang Terlambat) -->
            @if($grandTotalDenda > 0)
                <div class="mb-6 p-5 rounded-3xl bg-rose-50 border-2 border-rose-300 text-rose-950 shadow-sm">
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-2xl bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div class="flex-1 text-xs">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 mb-1.5">
                                <h3 class="text-sm font-black font-urbanist text-rose-900 tracking-tight">
                                    Pemberitahuan: Kartu Identitas Jaminan Anda Ditahan Sementara
                                </h3>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-200 text-rose-900 self-start sm:self-auto font-mono">
                                    Total Denda: Rp{{ number_format($grandTotalDenda, 0, ',', '.') }}
                                </span>
                            </div>
                            <p class="text-stone-700 leading-relaxed">
                                Sesuai SOP ketentuan toko sewa BLNTRK Outdoor, kartu identitas fisik (KTP/SIM) yang Anda titipkan sebagai jaminan saat ini <strong>ditahan sementara di kasir toko</strong>
                                @if($estimasiDendaBerjalan > 0 && $totalDendaBelumLunas > 0)
                                    karena masa sewa melewati batas waktu (denda berjalan Rp{{ number_format($estimasiDendaBerjalan, 0, ',', '.') }}) serta terdapat denda fisik tercatat sebesar Rp{{ number_format($totalDendaBelumLunas, 0, ',', '.') }} yang belum dilunasi.
                                @elseif($estimasiDendaBerjalan > 0)
                                    karena masa sewa telah melewati batas waktu pengembalian (terlambat {{ $infoKeterlambatan['hari'] }} hari) dengan estimasi denda berjalan Rp{{ number_format($estimasiDendaBerjalan, 0, ',', '.') }}.
                                @else
                                    karena masih terdapat kewajiban denda / biaya ganti rugi perlengkapan sebesar Rp{{ number_format($totalDendaBelumLunas, 0, ',', '.') }} yang belum dilunasi.
                                @endif
                            </p>
                            <div class="mt-2.5 pt-2 border-t border-rose-200/80 flex flex-wrap items-center gap-2 text-[11px] text-stone-600">
                                <span class="font-bold text-rose-800">Petunjuk Pelunasan:</span>
                                <span>Silakan selesaikan pembayaran denda di kasir toko saat serah terima pengembalian agar kartu identitas jaminan Anda dapat segera diserahkan kembali.</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
