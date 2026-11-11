                <!-- Banner Informasi Status Terkini -->
                <div class="mt-6">
                    @if($pesanan->status === \App\Enums\StatusPesanan::Menunggu)
                        <div class="p-4 sm:p-5 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-950 flex items-start sm:items-center gap-3">
                            <svg class="w-5 h-5 text-amber-700 shrink-0 mt-0.5 sm:mt-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <div class="flex-grow">
                                <span class="font-extrabold block">Menunggu Pembayaran</span>
                                @php $batasBayar = $pesanan->batas_bayar ?? $pesanan->batas_ambil; @endphp
                                <span class="text-amber-800 text-[11px]">Lunasi pembayaran sebelum <strong>{{ $batasBayar?->translatedFormat('d F Y, H:i') ?? 'Hari pertama sewa' }} WIB</strong> (tunai di toko BLNTRK Outdoor, atau transfer lalu unggah bukti di bawah). Jika melewati batas waktu, pesanan dibatalkan otomatis dan stok dilepas.</span>
                            </div>
                        </div>
                    @elseif($pesanan->status === \App\Enums\StatusPesanan::Dibayar)
                        <div class="p-4 sm:p-5 rounded-2xl bg-[#5edd5b]/15 border border-[#5edd5b]/30 text-xs text-[#0e1b15] flex items-center gap-3">
                            <svg class="w-5 h-5 text-emerald-800 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <span class="font-extrabold block">Pembayaran Dikonfirmasi Lunas</span>
                                <span class="text-stone-700 text-[11px]">Peralatan Anda telah disiapkan oleh staf toko. Mohon bawa <strong>Kartu Identitas Asli (KTP / SIM / Kartu Pelajar)</strong> saat mengambil perlengkapan.</span>
                            </div>
                        </div>
                    @elseif($pesanan->status === \App\Enums\StatusPesanan::Disewa)
                        <div class="p-4 sm:p-5 rounded-2xl bg-sky-50 border border-sky-200 text-xs text-sky-950 flex items-center gap-3">
                            <svg class="w-5 h-5 text-sky-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <span class="font-extrabold block">Peralatan Sedang Disewa</span>
                                <span class="text-sky-800 text-[11px]">Unit telah diserahterimakan pada {{ $pesanan->diambil_pada?->format('d/m/Y H:i') }} WIB. Wajib dikembalikan maksimal <strong>{{ $pesanan->tanggal_selesai->translatedFormat('d F Y') }} pukul 12:00 WIB</strong>.</span>
                            </div>
                        </div>
                    @elseif($pesanan->status === \App\Enums\StatusPesanan::Terlambat || ($infoKeterlambatan && $infoKeterlambatan['hari'] > 0))
                        @php
                            $hariTelat = $infoKeterlambatan['hari'] ?? 1;
                            $tarifPerHari = $infoKeterlambatan['tarif_harian'] ?? $pesanan->detail->sum(fn($d) => $d->harga_per_hari * $d->jumlah);
                            $nominalDendaTelat = $infoKeterlambatan['jumlah'] ?? ($hariTelat * $tarifPerHari);
                        @endphp
                        <div class="p-5 sm:p-6 rounded-3xl bg-rose-50 border-2 border-rose-300 text-xs text-rose-950 shadow-sm">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-2xl bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-xs mt-0.5">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </div>
                                <div class="flex-1">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 mb-2">
                                        <h3 class="text-sm sm:text-base font-black font-urbanist text-rose-900 tracking-tight">
                                            Peringatan: Masa Sewa Melewati Batas Waktu (Terlambat {{ $hariTelat }} Hari)
                                        </h3>
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-200 text-rose-900 border border-rose-300 font-mono self-start sm:self-auto animate-pulse">
                                            Denda Berjalan: Rp{{ number_format($nominalDendaTelat, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <p class="text-stone-700 leading-relaxed mb-3">
                                        Batas waktu pengembalian sewa Anda telah jatuh tempo pada <strong>{{ $pesanan->tanggal_selesai->translatedFormat('d F Y') }} pukul 12:00 WIB</strong>. Unit perlengkapan sewa saat ini tercatat belum diserahkan kembali ke toko BLNTRK Outdoor.
                                    </p>

                                    <!-- Grid Kalkulasi Denda Berjalan -->
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 mb-3 bg-white p-3.5 rounded-2xl border border-rose-200/80">
                                        <div>
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400 block">Durasi Keterlambatan</span>
                                            <span class="font-extrabold text-rose-900 text-sm">Terlambat {{ $hariTelat }} Hari</span>
                                            <span class="text-[10px] text-stone-500 block">lewat batas 12:00 WIB</span>
                                        </div>
                                        <div>
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400 block">Tarif Sewa Harian</span>
                                            <span class="font-mono font-bold text-stone-800 text-sm">Rp{{ number_format($tarifPerHari, 0, ',', '.') }}/hari</span>
                                            <span class="text-[10px] text-stone-500 block">total semua alat sewa</span>
                                        </div>
                                        <div>
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400 block">Estimasi Denda Berjalan</span>
                                            <span class="font-mono font-black text-rose-600 text-sm">Rp{{ number_format($nominalDendaTelat, 0, ',', '.') }}</span>
                                            <span class="text-[10px] text-rose-700 font-medium block">otomatis per 24 jam</span>
                                        </div>
                                    </div>

                                    <!-- SOP Transparan Ketentuan Denda -->
                                    <div class="p-3 bg-rose-100/60 rounded-xl border border-rose-200 text-[11px] text-rose-950 space-y-1">
                                        <div class="font-bold flex items-center gap-1.5 text-rose-900">
                                            <svg class="w-3.5 h-3.5 text-rose-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Informasi Ketentuan Denda & Jaminan Toko BLNTRK Outdoor:</span>
                                        </div>
                                        <ul class="list-disc list-inside space-y-0.5 text-stone-700 pl-1 leading-relaxed">
                                            <li><strong>Denda Keterlambatan:</strong> Terhitung otomatis sesuai tarif harian per 24 jam keterlambatan sampai barang diserahkan ke kasir toko.</li>
                                            <li><strong>Denda Kerusakan & Kehilangan:</strong> Diperiksa kelengkapannya secara fisik saat pengembalian di toko dan diinput manual oleh admin.</li>
                                            <li><strong>Jaminan Identitas (KTP/SIM):</strong> Ditahan sementara di toko dan baru dapat diserahkan kembali setelah seluruh denda dilunasi.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($pesanan->status === \App\Enums\StatusPesanan::Dibatalkan)
                        <div class="p-4 sm:p-5 rounded-2xl bg-stone-100 border border-stone-200 text-xs text-stone-700 flex items-center gap-3">
                            <svg class="w-5 h-5 text-stone-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <span class="font-extrabold block text-stone-900">Pesanan Dibatalkan</span>
                                <span class="text-[11px] text-stone-600">Alasan: "{{ $pesanan->alasan_batal ?? 'Dibatalkan' }}"</span>
                                @if($pesanan->total_dibayar > 0)
                                    <span class="text-[11px] text-stone-600 block mt-0.5">
                                        Dana Rp{{ number_format($pesanan->total_dibayar, 0, ',', '.') }}:
                                        @if($pesanan->total_refund > 0)
                                            dikembalikan Rp{{ number_format($pesanan->total_refund, 0, ',', '.') }}{{ $pesanan->refund->count() === 1 ? ' via '.$pesanan->refund->first()->metode->value : '' }}.
                                        @endif
                                        @if($pesanan->dana_hangus > 0)
                                            Rp{{ number_format($pesanan->dana_hangus, 0, ',', '.') }} tidak dikembalikan. Hubungi toko bila ada pertanyaan.
                                        @endif
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($pesanan->status === \App\Enums\StatusPesanan::Menunggu)
                        <div class="mt-3 p-4 sm:p-5 rounded-2xl bg-white border border-stone-200 text-xs text-stone-800">
                            <span class="font-extrabold block mb-1">Bukti Transfer</span>
                            @if($pesanan->bukti_transfer)
                                <p class="text-[11px] text-stone-600 mb-2">
                                    Terunggah {{ $pesanan->bukti_diunggah_pada?->format('d/m/Y H:i') }} WIB &mdash; menunggu verifikasi admin.
                                    <a href="{{ route('pesanan.bukti', $pesanan->no_resi) }}" target="_blank" rel="noopener" class="font-bold underline">Lihat</a>
                                </p>
                            @else
                                <p class="text-[11px] text-stone-600 mb-2">Bayar lewat transfer? Unggah bukti agar admin dapat memverifikasi lebih cepat (JPG/PNG/WEBP/PDF, maks. 4 MB).</p>
                            @endif
                            @can('uploadBukti', $pesanan)
                                <form method="POST" action="{{ route('pesanan.bukti.upload', $pesanan->no_resi) }}" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-2">
                                    @csrf
                                    <input type="file" name="bukti" accept=".jpg,.jpeg,.png,.webp,.pdf" required
                                           class="flex-1 text-xs rounded-xl border border-stone-300 bg-white file:mr-3 file:py-2 file:px-3 file:border-0 file:bg-stone-100 file:font-bold">
                                    <button type="submit" class="px-4 py-2 rounded-xl bg-[#0e1b15] hover:bg-[#1e3a2c] text-[#5edd5b] text-xs font-black transition">
                                        {{ $pesanan->bukti_transfer ? 'Ganti Bukti' : 'Unggah Bukti' }}
                                    </button>
                                </form>
                            @endcan
                        </div>
                    @endif

                    @can('ubahJadwal', $pesanan)
                        <details class="mt-3 p-4 sm:p-5 rounded-2xl bg-white border border-stone-200 text-xs text-stone-800" {{ $errors->has('tanggal_mulai') || $errors->has('tanggal_selesai') ? 'open' : '' }}>
                            <summary class="font-extrabold cursor-pointer">Ubah Jadwal Sewa</summary>
                            <form method="POST" action="{{ route('pesanan.jadwal', $pesanan->no_resi) }}" class="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-2 items-end">
                                @csrf
                                @method('PATCH')
                                <div>
                                    <label class="block text-[10px] font-bold text-stone-500 uppercase mb-1" for="jadwal_mulai">Mulai</label>
                                    <input type="date" id="jadwal_mulai" name="tanggal_mulai" required min="{{ today()->toDateString() }}" value="{{ old('tanggal_mulai', $pesanan->tanggal_mulai->toDateString()) }}"
                                           class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15]">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-stone-500 uppercase mb-1" for="jadwal_selesai">Selesai</label>
                                    <input type="date" id="jadwal_selesai" name="tanggal_selesai" required value="{{ old('tanggal_selesai', $pesanan->tanggal_selesai->toDateString()) }}"
                                           class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15]">
                                </div>
                                <button type="submit" class="px-4 py-2.5 rounded-xl bg-[#0e1b15] hover:bg-[#1e3a2c] text-[#5edd5b] text-xs font-black transition">Simpan Jadwal</button>
                                <p class="sm:col-span-3 text-[11px] text-stone-500">Total sewa dihitung ulang sesuai durasi baru dan ketersediaan alat dicek ulang.</p>
                            </form>
                        </details>
                    @endcan

                </div>
