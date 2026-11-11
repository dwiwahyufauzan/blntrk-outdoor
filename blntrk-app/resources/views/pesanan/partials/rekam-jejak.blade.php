                    <!-- Rekam Jejak Transaksi Fisik (Pembayaran, Jaminan, Pengembalian, Denda) -->
                    @if($adaPembayaran || $pesanan->jaminan || $pesanan->pengembalian || $pesanan->denda->isNotEmpty() || ($infoKeterlambatan && $infoKeterlambatan['hari'] > 0))
                        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-stone-200/90 shadow-sm space-y-5">
                            <h2 class="text-base font-extrabold text-[#0e1b15] font-urbanist">
                                Rekam Jejak Transaksi & Fisik
                            </h2>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                                <!-- Pembayaran -->
                                @if($adaPembayaran && $pembayaranUtama)
                                    <div class="bg-stone-50 rounded-2xl p-4 border border-stone-200/80">
                                        <div class="flex items-center justify-between pb-2 mb-2 border-b border-stone-200/60">
                                            <span class="font-bold text-stone-900">Pembayaran Sewa</span>
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-[#5edd5b] text-black">
                                                LUNAS
                                            </span>
                                        </div>
                                        <div class="space-y-1 text-stone-600">
                                            <div class="flex justify-between">
                                                <span class="text-stone-400">Nominal:</span>
                                                <span class="font-bold text-stone-900 font-mono">Rp{{ number_format($totalBayar, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-stone-400">Metode:</span>
                                                <span class="font-semibold text-stone-800">{{ $pembayaranUtama->metode->label() }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-stone-400">Waktu Bayar:</span>
                                                <span class="font-mono text-stone-700">{{ $pembayaranUtama->dibayar_pada?->format('d/m/Y H:i') }} WIB</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Jaminan -->
                                @if($pesanan->jaminan)
                                    <div class="bg-stone-50 rounded-2xl p-4 border border-stone-200/80">
                                        <div class="flex items-center justify-between pb-2 mb-2 border-b border-stone-200/60">
                                            <span class="font-bold text-stone-900">Jaminan Identitas</span>
                                            @if($pesanan->jaminan->dikembalikan_pada)
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-emerald-100 text-emerald-800">
                                                    DIKEMBALIKAN
                                                </span>
                                            @elseif($grandTotalDenda > 0)
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-rose-100 text-rose-800 border border-rose-300 animate-pulse">
                                                    DITAHAN (DENDA)
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-amber-100 text-amber-900">
                                                    DITITIPKAN
                                                </span>
                                            @endif
                                        </div>
                                        <div class="space-y-1 text-stone-600">
                                            <div class="flex justify-between">
                                                <span class="text-stone-400">Jenis Kartu:</span>
                                                <span class="font-bold text-stone-900">{{ $pesanan->jaminan->label_jenis }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-stone-400">No. Samar:</span>
                                                <span class="font-mono text-stone-800">{{ $pesanan->jaminan->nomor_identitas_samar ?? $pesanan->jaminan->nomor_identitas }}</span>
                                            </div>
                                            @if($pesanan->jaminan->dikembalikan_pada)
                                                <div class="flex justify-between text-[11px] text-emerald-700 font-medium pt-1">
                                                    <span>Waktu Kembali:</span>
                                                    <span>{{ $pesanan->jaminan->dikembalikan_pada->format('d/m/Y H:i') }}</span>
                                                </div>
                                            @elseif($grandTotalDenda > 0)
                                                <div class="text-[10px] text-rose-700 font-bold pt-1">
                                                    *Ditahan di kasir toko hingga denda (Rp{{ number_format($grandTotalDenda, 0, ',', '.') }}) dilunasi.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <!-- Kartu Denda Keterlambatan Berjalan (Jika sedang terlambat & belum ada form pengembalian) -->
                                @if($infoKeterlambatan && $infoKeterlambatan['hari'] > 0 && !$pesanan->pengembalian)
                                    <div class="bg-rose-50/90 rounded-2xl p-4 border border-rose-200 sm:col-span-2">
                                        <div class="flex items-center justify-between pb-2 mb-2 border-b border-rose-200/80">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <span class="font-bold text-rose-950 font-urbanist">Denda Keterlambatan Berjalan (Otomatis)</span>
                                            </div>
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-rose-200 text-rose-900 border border-rose-300 animate-pulse">
                                                TERLAMBAT {{ $infoKeterlambatan['hari'] }} HARI
                                            </span>
                                        </div>
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-stone-600">
                                            <div>
                                                <span class="text-stone-500 text-[11px] block">Tarif Sewa Harian:</span>
                                                <span class="font-mono font-bold text-stone-800">Rp{{ number_format($infoKeterlambatan['tarif_harian'], 0, ',', '.') }}/hari &times; {{ $infoKeterlambatan['hari'] }} hari</span>
                                            </div>
                                            <div class="text-left sm:text-right">
                                                <span class="text-stone-500 text-[11px] block">Estimasi Total Denda:</span>
                                                <span class="font-mono font-black text-rose-600 text-base">Rp{{ number_format($infoKeterlambatan['jumlah'], 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                        <p class="text-[10px] text-stone-500 mt-2 pt-2 border-t border-rose-200/60">
                                            *Nominal dihitung per 24 jam keterlambatan. Denda kerusakan/kehilangan kelengkapan akan diperiksa fisik saat kembali di toko dan diinput manual oleh admin.
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <!-- Rekam Pengembalian Alat & Kondisi -->
                            @if($pesanan->pengembalian)
                                <div class="bg-stone-50 rounded-2xl p-4 border border-stone-200/80">
                                    <div class="flex items-center justify-between pb-2 mb-2 border-b border-stone-200/60">
                                        <span class="font-bold text-stone-900">Kondisi Alat Saat Pengembalian</span>
                                        <span class="text-[11px] font-mono text-stone-500">
                                            {{ $pesanan->pengembalian->dikembalikan_pada?->format('d/m/Y H:i') }} WIB
                                        </span>
                                    </div>
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-left text-xs">
                                            <thead class="text-[9px] uppercase tracking-wider text-stone-400 font-bold border-b border-stone-200">
                                                <tr>
                                                    <th class="py-1">Alat</th>
                                                    <th class="py-1 text-center">Baik</th>
                                                    <th class="py-1 text-center">Rusak Ringan</th>
                                                    <th class="py-1 text-center">Rusak Berat</th>
                                                    <th class="py-1 text-center">Hilang</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-stone-100">
                                                @foreach($pesanan->pengembalian->detail as $pd)
                                                    <tr>
                                                        <td class="py-1.5 font-semibold text-stone-800">{{ $pd->pesananDetail?->alat?->nama ?? 'Alat' }}</td>
                                                        <td class="py-1.5 text-center text-emerald-700 font-bold">{{ $pd->jml_baik ?? 0 }}</td>
                                                        <td class="py-1.5 text-center text-amber-700 font-bold">{{ $pd->jml_rusak_ringan ?? 0 }}</td>
                                                        <td class="py-1.5 text-center text-rose-700 font-bold">{{ $pd->jml_rusak_berat ?? 0 }}</td>
                                                        <td class="py-1.5 text-center text-rose-900 font-black">{{ $pd->jml_hilang ?? 0 }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            <!-- Rincian Denda & Ganti Rugi Perlengkapan -->
                            @if($pesanan->denda->isNotEmpty())
                                <div class="bg-rose-50/70 rounded-2xl p-5 border border-rose-200/90 shadow-2xs">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-3 mb-3 border-b border-rose-200/80 gap-2">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            <span class="font-extrabold text-rose-950 font-urbanist text-sm">Catatan Denda & Ganti Rugi Perlengkapan</span>
                                        </div>
                                        @php
                                            $semuaLunas = $pesanan->denda->every(fn($d) => $d->status_bayar === 'lunas');
                                        @endphp
                                        <div class="flex items-center gap-2 self-start sm:self-auto">
                                            <span class="font-mono font-black text-rose-700 text-xs">Total: Rp{{ number_format($pesanan->denda->sum('jumlah'), 0, ',', '.') }}</span>
                                            <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider {{ $semuaLunas ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-rose-200 text-rose-900 border border-rose-300' }}">
                                                {{ $semuaLunas ? 'Lunas' : 'Belum Lunas' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="space-y-3">
                                        @foreach($pesanan->denda as $d)
                                            <div class="p-3 bg-white rounded-xl border border-rose-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 text-xs shadow-2xs">
                                                <div class="space-y-1">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider {{ $d->jenis->value === 'kehilangan' ? 'bg-rose-100 text-rose-900 border border-rose-200' : ($d->jenis->value === 'keterlambatan' ? 'bg-amber-100 text-amber-900 border border-amber-200' : 'bg-orange-100 text-orange-900 border border-orange-200') }}">
                                                            {{ $d->kategoriDenda?->nama ?? $d->jenis->label() }}
                                                        </span>
                                                        <span class="font-mono font-black text-rose-800 text-xs">Rp{{ number_format($d->jumlah, 0, ',', '.') }}</span>
                                                    </div>
                                                    <p class="text-stone-700 leading-relaxed text-[11px]">
                                                        <strong class="text-stone-900">Keterangan:</strong> {{ $d->keterangan }}
                                                    </p>
                                                </div>

                                                <div class="shrink-0 text-right">
                                                    @if($d->status_bayar === 'lunas')
                                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-1 rounded-lg border border-emerald-200">
                                                            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                            <span>Lunas {{ $d->dilunasi_pada ? '('.$d->dilunasi_pada->format('d/m/Y').')' : '' }}</span>
                                                        </span>
                                                    @else
                                                        <span class="inline-block text-[10px] font-bold text-rose-700 bg-rose-100 px-2.5 py-1 rounded-lg border border-rose-200">
                                                            Selesaikan di Toko
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @if(!$semuaLunas)
                                        <p class="text-[10px] text-rose-800 font-medium mt-3 pt-2 border-t border-rose-200/60 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Silakan tunjukkan nomor resi pesanan ini di meja kasir untuk melunasi denda dan mengambil kembali kartu identitas jaminan Anda.</span>
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif
