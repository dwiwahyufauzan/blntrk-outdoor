        <!-- Timeline Status Transaksi Modern (Foresta Style) -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-stone-200/90 shadow-xs mb-6">
            <h2 class="text-xs font-extrabold uppercase tracking-widest text-stone-400 mb-4 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#5edd5b]"></span>
                <span>Progres Siklus Pesanan</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 relative">
                <!-- Tahap 1: Dibuat -->
                <div class="p-3.5 rounded-xl border border-stone-200 bg-stone-50/70">
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="w-5 h-5 rounded-full bg-[#0e1b15] text-[#5edd5b] flex items-center justify-center text-[10px] font-bold">1</span>
                        <span class="text-xs font-bold text-stone-900">Pesanan Dibuat</span>
                    </div>
                    <div class="text-[11px] text-stone-600 font-medium">{{ $pesanan->created_at->format('d/m/Y H:i') }} WIB</div>
                    <span class="text-[10px] text-stone-400 font-mono block mt-0.5">Oleh: {{ $pesanan->pembuat?->nama ?? 'Sistem Daring' }}</span>
                </div>

                <!-- Tahap 2: Pembayaran -->
                @php
                    $isPaid = (bool) $pembayaran || in_array($pesanan->status->value, ['dibayar', 'disewa', 'dikembalikan', 'selesai']);
                @endphp
                <div class="p-3.5 rounded-xl border {{ $isPaid ? 'border-[#5edd5b]/60 bg-[#5edd5b]/10' : 'border-stone-200 bg-stone-50/40 text-stone-400' }}">
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="w-5 h-5 rounded-full {{ $isPaid ? 'bg-[#5edd5b] text-black' : 'bg-stone-200 text-stone-600' }} flex items-center justify-center text-[10px] font-bold">2</span>
                        <span class="text-xs font-bold {{ $isPaid ? 'text-stone-900' : 'text-stone-500' }}">Pembayaran</span>
                    </div>
                    @if($pembayaran)
                        <div class="text-[11px] text-stone-800 font-bold font-mono">Rp{{ number_format($pembayaran->jumlah, 0, ',', '.') }} ({{ ucfirst($pembayaran->metode->value) }})</div>
                        <span class="text-[10px] text-stone-500 font-mono block mt-0.5">{{ $pembayaran->dibayar_pada->format('d/m/Y H:i') }} WIB</span>
                    @else
                        <div class="text-[11px] text-stone-400 italic">Menunggu pembayaran</div>
                    @endif
                </div>

                <!-- Tahap 3: Diambil / Disewa -->
                @php
                    $isDiambil = $pesanan->diambil_pada || in_array($pesanan->status->value, ['disewa', 'dikembalikan', 'selesai', 'terlambat']);
                @endphp
                <div class="p-3.5 rounded-xl border {{ $isDiambil ? 'border-sky-300 bg-sky-50' : 'border-stone-200 bg-stone-50/40 text-stone-400' }}">
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="w-5 h-5 rounded-full {{ $isDiambil ? 'bg-sky-600 text-white' : 'bg-stone-200 text-stone-600' }} flex items-center justify-center text-[10px] font-bold">3</span>
                        <span class="text-xs font-bold {{ $isDiambil ? 'text-stone-900' : 'text-stone-500' }}">Serah Terima Alat</span>
                    </div>
                    @if($pesanan->diambil_pada)
                        <div class="text-[11px] text-sky-950 font-bold">Unit Telah Diambil</div>
                        <span class="text-[10px] text-stone-500 font-mono block mt-0.5">{{ $pesanan->diambil_pada->format('d/m/Y H:i') }} WIB</span>
                    @else
                        <div class="text-[11px] text-stone-400 italic">Belum diserahterimakan</div>
                    @endif
                </div>

                <!-- Tahap 4: Selesai / Status Akhir -->
                <div class="p-3.5 rounded-xl border {{ $pesanan->status->value === 'dibatalkan' ? 'border-rose-300 bg-rose-50' : ($pesanan->status->value === 'dikembalikan' ? 'border-emerald-300 bg-emerald-50' : 'border-stone-200 bg-stone-50/40') }}">
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="w-5 h-5 rounded-full {{ $pesanan->status->value === 'dibatalkan' ? 'bg-rose-600 text-white' : ($pesanan->status->value === 'dikembalikan' ? 'bg-emerald-600 text-white' : 'bg-stone-200 text-stone-600') }} flex items-center justify-center text-[10px] font-bold">4</span>
                        <span class="text-xs font-bold text-stone-900">
                            {{ $pesanan->status->value === 'dibatalkan' ? 'Dibatalkan' : ($pesanan->status->value === 'dikembalikan' ? 'Selesai / Kembali' : 'Pengembalian') }}
                        </span>
                    </div>
                    @if($pesanan->status->value === 'dibatalkan')
                        <div class="text-[11px] text-rose-800 font-bold">{{ $pesanan->dibatalkan_pada?->format('d/m/Y H:i') }} WIB</div>
                        <span class="text-[10px] text-rose-600 line-clamp-1 block mt-0.5">{{ $pesanan->alasan_batal }}</span>
                    @elseif($pesanan->status->value === 'dikembalikan')
                        <div class="text-[11px] text-emerald-800 font-bold">Alat Telah Dikembalikan</div>
                    @else
                        <div class="text-[11px] text-stone-400 italic">Tempo: {{ $pesanan->tanggal_selesai->format('d/m/Y') }}</div>
                    @endif
                </div>
            </div>
        </div>
