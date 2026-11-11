                <!-- Kartu Denda & Ganti Rugi Perlengkapan (Dapat Dikelola Kapan Saja) -->
                <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs overflow-hidden">
                    <div class="p-5 border-b border-stone-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full {{ $pesanan->denda->where('status_bayar', 'belum_lunas')->isNotEmpty() || ($infoKeterlambatan && $infoKeterlambatan['hari'] > 0) ? 'bg-rose-500 animate-ping' : 'bg-[#5edd5b]' }}"></span>
                            <div>
                                <h2 class="text-sm font-extrabold text-[#14201a] bl-d">Tagihan Denda & Ganti Rugi Perlengkapan</h2>
                                <span class="text-[11px] text-stone-500">Keterlambatan, barang hilang, atau kerusakan fisik perlengkapan</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            @php
                                $totalDendaTercatat = $pesanan->denda->sum('jumlah');
                                $totalDendaDenganBerjalan = $totalDendaTercatat + ($infoKeterlambatan && !$pesanan->pengembalian ? (float)$infoKeterlambatan['jumlah'] : 0);
                            @endphp

                            @if($totalDendaDenganBerjalan > 0)
                                <span class="text-xs font-mono font-black text-rose-700 bg-rose-50 px-2.5 py-1 rounded-lg border border-rose-200">
                                    Total: Rp{{ number_format($totalDendaDenganBerjalan, 0, ',', '.') }}
                                </span>
                            @endif

                            @can('tambahDenda', $pesanan)
                                <button type="button" @click="showModalTambahDenda = true"
                                        class="px-3 py-1.5 rounded-xl bg-[#0e1b15] hover:bg-[#1a2e24] text-[#5edd5b] text-xs font-bold transition flex items-center gap-1.5 shadow-2xs cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                    <span>Tambah Denda Manual</span>
                                </button>
                            @endcan
                        </div>
                    </div>

                    <!-- Box Peringatan Denda Keterlambatan Berjalan (Otomatis) -->
                    @if($infoKeterlambatan && $infoKeterlambatan['hari'] > 0 && !$pesanan->pengembalian)
                        <div class="p-5 border-b border-rose-200 bg-rose-50/70">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-200 text-rose-900 border border-rose-300 animate-pulse font-mono">
                                            TERLAMBAT {{ $infoKeterlambatan['hari'] }} HARI
                                        </span>
                                        <span class="text-xs font-extrabold text-rose-950 font-urbanist">Denda Keterlambatan Sedang Berjalan (Otomatis)</span>
                                    </div>
                                    <p class="text-xs text-stone-600">Unit belum diserahkan ke kasir toko. Batas jadwal sewa: {{ $pesanan->tanggal_selesai->format('d/m/Y') }} 12:00 WIB.</p>
                                </div>
                                <div class="text-left sm:text-right">
                                    <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Estimasi Denda Berjalan</span>
                                    <span class="text-xl font-black font-mono text-rose-600">Rp{{ number_format($infoKeterlambatan['jumlah'], 0, ',', '.') }}</span>
                                    <span class="text-[10px] text-stone-400 block font-mono">Rp{{ number_format($infoKeterlambatan['tarif_harian'], 0, ',', '.') }}/hari</span>
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-t border-rose-200/60 flex flex-wrap items-center justify-between gap-3 text-xs">
                                <span class="text-stone-600 text-[11px]">*Denda keterlambatan akan dibukukan resmi saat form pengembalian diproses. Denda kerusakan & kelengkapan hilang diinput manual saat cek fisik.</span>
                                <a href="{{ route('admin.pesanan.pengembalian.create', $pesanan->id) }}"
                                   class="px-3.5 py-1.5 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-extrabold transition shadow-2xs inline-flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    <span>Proses Pengembalian Sekarang</span>
                                </a>
                            </div>
                        </div>
                    @endif

                    @if($pesanan->denda->isNotEmpty())
                        <div class="p-5 space-y-3 bg-stone-50/40">
                            @foreach($pesanan->denda as $d)
                                <div class="p-4 rounded-xl bg-white border border-stone-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs shadow-2xs">
                                    <div class="space-y-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <!-- Badge Kategori Denda -->
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider {{ $d->jenis->value === 'kehilangan' ? 'bg-rose-100 text-rose-900 border border-rose-200' : ($d->jenis->value === 'keterlambatan' ? 'bg-amber-100 text-amber-900 border border-amber-200' : 'bg-orange-100 text-orange-900 border border-orange-200') }}">
                                                {{ $d->kategoriDenda?->nama ?? $d->jenis->label() }}
                                            </span>
                                            <span class="font-bold text-stone-900 font-mono text-sm">Rp{{ number_format($d->jumlah, 0, ',', '.') }}</span>
                                            <span class="text-[10px] text-stone-400 font-mono">Dicatat: {{ $d->created_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                        <div class="text-stone-700 text-xs">
                                            <strong class="text-stone-900">Informasi Denda:</strong> {{ $d->keterangan }}
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 shrink-0">
                                        @if($d->status_bayar === 'lunas')
                                            <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-1">
                                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                <span>Lunas{{ $d->metode_bayar ? ' via '.$d->metode_bayar->value : '' }} ({{ $d->dilunasi_pada?->format('d/m H:i') }})</span>
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-rose-100 text-rose-800 border border-rose-200">
                                                Belum Lunas
                                            </span>
                                            <form method="POST" action="{{ route('admin.denda.lunas', $d->id) }}"
                                                  class="flex items-center gap-1.5"
                                                  data-konfirmasi="primary"
                                                  data-judul="Konfirmasi Pelunasan Denda"
                                                  data-pesan="Tandai lunas denda Rp{{ number_format($d->jumlah, 0, ',', '.') }}?"
                                                  data-tombol="Ya, Tandai Lunas">
                                                @csrf
                                                @method('PATCH')
                                                <label for="metode_bayar_{{ $d->id }}" class="sr-only">Metode pembayaran denda</label>
                                                <select id="metode_bayar_{{ $d->id }}" name="metode_bayar" required
                                                        class="text-[11px] rounded-lg border-stone-300 py-1.5 pr-7 focus:border-[#0e1b15] focus:ring-[#0e1b15]">
                                                    @foreach(\App\Enums\MetodePembayaran::cases() as $m)
                                                        <option value="{{ $m->value }}">{{ $m->value === 'tunai' ? 'Tunai' : 'Transfer' }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-[11px] font-extrabold transition shadow-2xs cursor-pointer inline-flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                    <span>Tandai Lunas</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-6 text-center text-xs text-stone-400">
                            <svg class="w-8 h-8 mx-auto text-stone-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p>Tidak ada catatan denda atau ganti rugi pada pesanan ini.</p>
                        </div>
                    @endif
                </div>
