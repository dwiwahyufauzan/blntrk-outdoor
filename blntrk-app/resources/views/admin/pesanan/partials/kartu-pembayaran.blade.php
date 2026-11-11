                    <!-- Card Pembayaran -->
                    <div class="bg-white p-5 rounded-2xl border border-stone-200/90 shadow-xs">
                        <div class="flex items-center justify-between pb-3 mb-3 border-b border-stone-100">
                            <h3 class="text-xs font-bold text-[#14201a] uppercase tracking-wider flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span>Informasi Pembayaran</span>
                            </h3>
                            @if($pembayaran)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">LUNAS</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">BELUM DIBAYAR</span>
                            @endif
                        </div>

                        @if($pesanan->pembayaran->isNotEmpty())
                            @php $totalDibayar = (float) $pesanan->pembayaran->sum('jumlah'); @endphp
                            <div class="space-y-2 text-xs">
                                @foreach($pesanan->pembayaran as $bayar)
                                    <div class="py-1 border-b border-stone-100">
                                        <div class="flex justify-between">
                                            <span class="text-stone-500">{{ ($bayar->jenis ?? 'sewa') === 'perpanjangan' ? 'Perpanjangan' : 'Pembayaran Sewa' }}:</span>
                                            <span class="font-bold text-stone-900 font-mono">Rp{{ number_format($bayar->jumlah, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between text-[11px] text-stone-500 mt-0.5">
                                            <span>{{ $bayar->metode->label() }} &bull; {{ $bayar->dibayar_pada->format('d/m/Y H:i') }} WIB</span>
                                            <span>{{ $bayar->pencatat?->nama ?? 'Admin' }}</span>
                                        </div>
                                        @if($bayar->catatan)
                                            <div class="text-[11px] text-stone-500 italic">"{{ $bayar->catatan }}"</div>
                                        @endif
                                    </div>
                                @endforeach
                                @if($pesanan->pembayaran->count() > 1)
                                    <div class="flex justify-between pt-1">
                                        <span class="text-stone-500">Total Diterima:</span>
                                        <span class="font-black text-stone-900 font-mono">Rp{{ number_format($totalDibayar, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="py-4 text-center text-xs text-stone-400">
                                Pembayaran belum dicatat oleh kasir toko.
                            </div>
                        @endif

                        @if($pesanan->status === \App\Enums\StatusPesanan::Menunggu && $pesanan->batas_bayar)
                            <div class="mt-3 p-3 rounded-xl border text-[11px] {{ $pesanan->batas_bayar->isPast() ? 'bg-rose-50 border-rose-200 text-rose-900' : 'bg-amber-50 border-amber-200 text-amber-900' }}">
                                Batas pembayaran: <strong>{{ $pesanan->batas_bayar->format('d/m/Y H:i') }} WIB</strong>.
                                Lewat batas ini pesanan dibatalkan otomatis dan stok dilepas{{ $pesanan->bukti_transfer ? ' (kecuali bukti transfer sudah diunggah)' : '' }}.
                            </div>
                        @endif

                        @if($pesanan->refund->isNotEmpty() || $pesanan->status === \App\Enums\StatusPesanan::Dibatalkan && $pesanan->total_dibayar > 0)
                            <div class="mt-3 pt-3 border-t border-stone-100 space-y-1.5 text-xs">
                                @foreach($pesanan->refund as $refund)
                                    <div>
                                        <div class="flex justify-between">
                                            <span class="text-rose-700">Refund dikembalikan:</span>
                                            <span class="font-bold text-rose-700 font-mono">-Rp{{ number_format($refund->jumlah, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between text-[11px] text-stone-500 mt-0.5">
                                            <span>{{ $refund->metode->label() }} &bull; {{ $refund->direfund_pada->format('d/m/Y H:i') }} WIB</span>
                                            <span>{{ $refund->pencatat?->nama ?? 'Admin' }}</span>
                                        </div>
                                        @if($refund->catatan)
                                            <div class="text-[11px] text-stone-500 italic">"{{ $refund->catatan }}"</div>
                                        @endif
                                    </div>
                                @endforeach
                                @if($pesanan->status === \App\Enums\StatusPesanan::Dibatalkan && $pesanan->total_dibayar > 0)
                                    <div class="flex justify-between pt-1 border-t border-stone-100">
                                        <span class="text-stone-500">Dana hangus (tetap pendapatan):</span>
                                        <span class="font-black text-stone-900 font-mono">Rp{{ number_format($pesanan->dana_hangus, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                @can('catatRefund', $pesanan)
                                    <button type="button" @click="showModalRefund = true"
                                            class="mt-2 w-full px-3 py-2 rounded-xl bg-stone-50 hover:bg-stone-100 text-stone-800 text-[11px] font-bold border border-stone-200 transition cursor-pointer">
                                        Catat Refund
                                    </button>
                                @endcan
                            </div>
                        @endif

                        @if($pesanan->bukti_transfer)
                            <div class="mt-3 p-3 rounded-xl bg-sky-50 border border-sky-200 text-[11px] text-sky-900 flex items-center justify-between gap-2">
                                <span>Bukti transfer diunggah {{ $pesanan->bukti_diunggah_pada?->format('d/m/Y H:i') }} WIB</span>
                                <a href="{{ route('admin.pesanan.bukti', $pesanan->id) }}" target="_blank" rel="noopener"
                                   class="px-2.5 py-1 rounded-lg bg-sky-700 hover:bg-sky-800 text-white font-bold">Lihat Bukti</a>
                            </div>
                        @elseif($pesanan->status === \App\Enums\StatusPesanan::Menunggu)
                            <div class="mt-3 text-[11px] text-stone-400 text-center">Pelanggan belum mengunggah bukti transfer.</div>
                        @endif
                    </div>
