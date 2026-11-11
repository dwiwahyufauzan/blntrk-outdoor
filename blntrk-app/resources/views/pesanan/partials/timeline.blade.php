                <!-- Progres Siklus Pesanan (Foresta Timeline) -->
                <div class="pt-6">
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-stone-400 block mb-3">Status Siklus Pemesanan</span>
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
                        <!-- Step 1: Dibuat -->
                        <div class="p-3.5 rounded-2xl border border-stone-200 bg-stone-50/70">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="w-5 h-5 rounded-full bg-[#0e1b15] text-[#5edd5b] flex items-center justify-center text-[10px] font-black">1</span>
                                <span class="font-bold text-stone-900">Reservasi Dibuat</span>
                            </div>
                            <span class="text-[11px] text-stone-500 font-mono block">{{ $pesanan->created_at->format('d/m/Y H:i') }} WIB</span>
                        </div>

                        <!-- Step 2: Pembayaran -->
                        @php
                            $isPaid = $adaPembayaran || in_array($pesanan->status->value, ['dibayar', 'disewa', 'dikembalikan', 'selesai']);
                        @endphp
                        <div class="p-3.5 rounded-2xl border {{ $isPaid ? 'border-[#5edd5b]/60 bg-[#5edd5b]/10' : 'border-stone-200 bg-stone-50/40 text-stone-400' }}">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="w-5 h-5 rounded-full {{ $isPaid ? 'bg-[#5edd5b] text-black font-black' : 'bg-stone-200 text-stone-600' }} flex items-center justify-center text-[10px]">2</span>
                                <span class="font-bold {{ $isPaid ? 'text-stone-900' : 'text-stone-500' }}">Pembayaran Toko</span>
                            </div>
                            @if($pembayaranUtama)
                                <span class="text-[11px] text-emerald-900 font-bold block">Rp{{ number_format($totalBayar, 0, ',', '.') }} (Lunas)</span>
                            @else
                                <span class="text-[11px] text-stone-400 italic block">Menunggu kasir toko</span>
                            @endif
                        </div>

                        <!-- Step 3: Diambil & Jaminan -->
                        @php
                            $isDiambil = $pesanan->diambil_pada || in_array($pesanan->status->value, ['disewa', 'dikembalikan', 'selesai', 'terlambat']);
                        @endphp
                        <div class="p-3.5 rounded-2xl border {{ $isDiambil ? 'border-sky-300 bg-sky-50' : 'border-stone-200 bg-stone-50/40 text-stone-400' }}">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="w-5 h-5 rounded-full {{ $isDiambil ? 'bg-sky-600 text-white font-black' : 'bg-stone-200 text-stone-600' }} flex items-center justify-center text-[10px]">3</span>
                                <span class="font-bold {{ $isDiambil ? 'text-sky-950' : 'text-stone-500' }}">Serah Terima Alat</span>
                            </div>
                            @if($pesanan->diambil_pada)
                                <span class="text-[11px] text-sky-900 font-medium block">Diambil: {{ $pesanan->diambil_pada->format('d/m/Y H:i') }}</span>
                            @else
                                <span class="text-[11px] text-stone-400 italic block">Unit belum diambil</span>
                            @endif
                        </div>

                        <!-- Step 4: Selesai / Status Akhir -->
                        @php
                            $isSelesai = $pesanan->status->value === 'dikembalikan';
                            $isBatal = $pesanan->status->value === 'dibatalkan';
                        @endphp
                        <div class="p-3.5 rounded-2xl border {{ $isBatal ? 'border-rose-300 bg-rose-50' : ($isSelesai ? 'border-emerald-300 bg-emerald-50' : 'border-stone-200 bg-stone-50/40') }}">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="w-5 h-5 rounded-full {{ $isBatal ? 'bg-rose-600 text-white' : ($isSelesai ? 'bg-emerald-600 text-white' : 'bg-stone-200 text-stone-600') }} flex items-center justify-center text-[10px] font-black">4</span>
                                <span class="font-bold text-stone-900">
                                    {{ $isBatal ? 'Dibatalkan' : ($isSelesai ? 'Dikembalikan' : 'Selesai Kembali') }}
                                </span>
                            </div>
                            @if($isBatal)
                                <span class="text-[11px] text-rose-700 block truncate">{{ $pesanan->alasan_batal }}</span>
                            @elseif($isSelesai)
                                <span class="text-[11px] text-emerald-800 font-bold block">Tuntas dikembalikan</span>
                            @else
                                <span class="text-[11px] text-stone-400 italic block">Maks {{ $pesanan->tanggal_selesai->format('d/m/Y') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
