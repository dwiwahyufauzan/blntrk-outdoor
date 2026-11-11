                    <!-- Rincian Perlengkapan -->
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-stone-200/90 shadow-sm">
                        <div class="flex items-center justify-between pb-4 mb-4 border-b border-stone-100">
                            <h2 class="text-base font-extrabold text-[#0e1b15] font-urbanist">
                                Rincian Perlengkapan yang Dipesan
                            </h2>
                            <span class="text-xs text-stone-400 font-medium">
                                {{ $pesanan->detail->sum('jumlah') }} Unit Total
                            </span>
                        </div>

                        <div class="divide-y divide-stone-100">
                            @foreach($pesanan->detail as $item)
                                <div class="py-4 first:pt-0 last:pb-0 flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3.5">
                                        <div class="w-12 h-12 rounded-2xl bg-stone-100 border border-stone-200/80 flex items-center justify-center text-stone-500 shrink-0">
                                            @if($item->alat->foto)
                                                <img src="{{ $item->alat->foto_url }}" alt="{{ $item->alat->nama }}" class="w-full h-full object-cover rounded-2xl">
                                            @else
                                                <svg class="w-6 h-6 text-stone-400" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M14 6l4 8h-3l2 4H7l4-7 2 3 1-2v-6zm0-4L9 11l-3-5L1 18h22L14 2z"/>
                                                </svg>
                                            @endif
                                        </div>
                                        <div>
                                            <h4 class="font-extrabold text-stone-900 text-sm font-urbanist">{{ $item->alat->nama }}</h4>
                                            <span class="text-[11px] text-stone-400 font-mono">
                                                {{ $item->alat->kode_alat }} &bull; {{ $item->alat->kategori?->nama }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <div class="text-sm font-black text-stone-900 font-mono">
                                            Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                                        </div>
                                        <span class="text-[11px] text-stone-500">
                                            {{ $item->jumlah }} unit &times; Rp{{ number_format($item->harga_per_hari, 0, ',', '.') }}/hari
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Subtotal & Total -->
                        <div class="mt-6 pt-4 border-t-2 border-stone-100 space-y-2 text-xs">
                            <div class="flex justify-between items-center text-stone-500">
                                <span>Durasi Sewa:</span>
                                <span class="font-bold text-stone-800">{{ $pesanan->durasi_hari }} Hari (24 Jam &times; {{ $pesanan->durasi_hari }})</span>
                            </div>
@if($pesanan->diskon_paket > 0)
                            <div class="flex justify-between items-center text-[#2f8f2c] font-bold">
                                <span>Diskon {{ $pesanan->paketBundling?->nama ?? 'Paket Bundling' }}{{ $pesanan->jumlah_paket > 1 ? ' x'.$pesanan->jumlah_paket : '' }}:</span>
                                <span>-Rp{{ number_format($pesanan->diskon_paket, 0, ',', '.') }}</span>
                            </div>
                            @endif
                                                        <div class="flex justify-between items-center pt-2 border-t border-stone-100">
                                <span class="font-extrabold text-stone-900 text-sm font-urbanist">Total Biaya Sewa:</span>
                                <span class="text-xl sm:text-2xl font-black font-urbanist text-[#0e1b15]">
                                    Rp{{ number_format($pesanan->total_sewa, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        @if($pesanan->catatan)
                            <div class="mt-5 p-3.5 rounded-2xl bg-stone-50 border border-stone-200 text-xs text-stone-600">
                                <strong class="text-stone-800 block mb-0.5">Catatan Tambahan:</strong>
                                <span>{{ $pesanan->catatan }}</span>
                            </div>
                        @endif
                    </div>
