        <!-- Header & Action -->
        <div class="mb-7 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('admin.pesanan.index') }}" class="text-xs font-bold text-stone-500 hover:text-[#0e1b15] transition inline-flex items-center gap-1.5 mb-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali ke Daftar Pesanan</span>
                </a>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-[#14201a] tracking-tight bl-d font-mono">
                        {{ $pesanan->no_resi }}
                    </h1>
                    @php
                        $badge = match($pesanan->status->value) {
                            'menunggu' => 'bg-amber-100 text-amber-900 border border-amber-300 font-extrabold',
                            'dibayar' => 'bg-[#5edd5b]/20 text-stone-900 border border-[#5edd5b]/40 font-extrabold',
                            'disewa' => 'bg-sky-100 text-sky-900 border border-sky-300 font-extrabold',
                            'dikembalikan', 'selesai' => 'bg-emerald-100 text-emerald-900 border border-emerald-300 font-bold',
                            'terlambat' => 'bg-rose-100 text-rose-900 border border-rose-300 font-extrabold animate-pulse',
                            'dibatalkan' => 'bg-stone-100 text-stone-600 border border-stone-200 font-bold',
                            default => 'bg-stone-100 text-stone-600 font-bold'
                        };
                    @endphp
                    <span class="px-3 py-1 rounded-full text-xs uppercase tracking-wider {{ $badge }}">
                        {{ $pesanan->status->label() }}
                    </span>
                </div>
                <p class="text-xs text-stone-500 mt-1">Dibuat pada {{ $pesanan->created_at->format('d/m/Y H:i') }} WIB oleh {{ $pesanan->pembuat?->nama ?? 'Sistem Reservasi Daring' }}</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <!-- Tombol WhatsApp Pelanggan (T14) -->
                @if($pesanan->status === \App\Enums\StatusPesanan::Terlambat || ($infoKeterlambatan && $infoKeterlambatan['hari'] > 0))
                    <a href="{{ wa_link($pesanan->pelanggan->no_hp, \App\Services\WhatsAppService::pesanPeringatanTerlambat($pesanan, $infoKeterlambatan['hari'] ?? 1, $infoKeterlambatan['jumlah'] ?? 0)) }}" target="_blank"
                       class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-900 border border-rose-300 text-xs font-bold transition inline-flex items-center gap-1.5 shadow-2xs">
                        <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.173.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.159.57 4.184 1.564 5.938l-1.564 5.714 5.861-1.537c1.7 1.006 3.684 1.585 5.807 1.585 6.627 0 12-5.373 12-12 0-6.627-5.373-12-12-12z"/></svg>
                        <span>Peringatan Terlambat WA (Rp{{ number_format($infoKeterlambatan['jumlah'] ?? 0, 0, ',', '.') }})</span>
                    </a>
                @elseif($dendaBelumLunas > 0)
                    <a href="{{ wa_link($pesanan->pelanggan->no_hp, \App\Services\WhatsAppService::pesanTagihanDenda($pesanan, $dendaBelumLunas)) }}" target="_blank"
                       class="px-3.5 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 text-xs font-bold transition inline-flex items-center gap-1.5 shadow-2xs">
                        <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.173.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.159.57 4.184 1.564 5.938l-1.564 5.714 5.861-1.537c1.7 1.006 3.684 1.585 5.807 1.585 6.627 0 12-5.373 12-12 0-6.627-5.373-12-12-12z"/></svg>
                        <span>Kirim Tagihan Denda WA</span>
                    </a>
                @elseif(in_array($pesanan->status->value, ['disewa']))
                    <a href="{{ wa_link($pesanan->pelanggan->no_hp, \App\Services\WhatsAppService::pesanPengingatJatuhTempo($pesanan)) }}" target="_blank"
                       class="px-3.5 py-2 rounded-xl bg-white hover:bg-stone-50 text-stone-800 border border-stone-200 text-xs font-bold transition inline-flex items-center gap-1.5 shadow-2xs">
                        <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.173.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.159.57 4.184 1.564 5.938l-1.564 5.714 5.861-1.537c1.7 1.006 3.684 1.585 5.807 1.585 6.627 0 12-5.373 12-12 0-6.627-5.373-12-12-12z"/></svg>
                        <span>Kirim Pengingat Jatuh Tempo</span>
                    </a>
                @else
                    <a href="{{ wa_link($pesanan->pelanggan->no_hp, \App\Services\WhatsAppService::pesanKonfirmasiBooking($pesanan)) }}" target="_blank"
                       class="px-3.5 py-2 rounded-xl bg-white hover:bg-stone-50 text-stone-800 border border-stone-200 text-xs font-bold transition inline-flex items-center gap-1.5 shadow-2xs">
                        <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.173.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.159.57 4.184 1.564 5.938l-1.564 5.714 5.861-1.537c1.7 1.006 3.684 1.585 5.807 1.585 6.627 0 12-5.373 12-12 0-6.627-5.373-12-12-12z"/></svg>
                        <span>Hubungi WA</span>
                    </a>
                @endif

                <!-- Cetak Resi & Unduh PDF -->
                <a href="{{ route('pesanan.resi', $pesanan->no_resi) }}" target="_blank"
                   class="px-3.5 py-2 rounded-xl bg-white hover:bg-stone-50 text-stone-800 border border-stone-200 text-xs font-bold transition inline-flex items-center gap-1.5 shadow-2xs"
                   title="Buka tampilan lembar dokumen resi siap cetak">
                    <svg class="w-4 h-4 text-[#0e1b15]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Lihat Resi Dokumen</span>
                </a>

                <a href="{{ route('admin.pesanan.pdf', $pesanan->id) }}"
                   class="px-3.5 py-2 rounded-xl bg-white hover:bg-stone-50 text-stone-700 border border-stone-200 text-xs font-bold transition inline-flex items-center gap-1.5 shadow-2xs"
                   title="Unduh file dokumen PDF resi resmi">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Unduh PDF</span>
                </a>

                <a href="{{ route('pesanan.resi', $pesanan->no_resi) }}?cetak=1" target="_blank"
                   class="px-3.5 py-2 rounded-xl bg-white hover:bg-stone-50 text-stone-700 border border-stone-200 text-xs font-bold transition inline-flex items-center gap-1.5 shadow-2xs"
                   title="Cetak langsung lembar dokumen resi fisik">
                    <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Resi</span>
                </a>

                <!-- Tombol Aksi Panel Sesuai Status (F05) -->
                @if($pesanan->status === \App\Enums\StatusPesanan::Menunggu)
                    <button type="button" @click="showModalBayar = true"
                            class="px-4 py-2 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-extrabold shadow-sm transition inline-flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Catat Pembayaran</span>
                    </button>
                @endif

                @if($pesanan->status === \App\Enums\StatusPesanan::Dibayar)
                    <button type="button" @click="showModalAmbil = true"
                            class="px-4 py-2 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-extrabold shadow-sm transition inline-flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Konfirmasi Serah Terima (Ambil)</span>
                    </button>
                @endif

                @if(in_array($pesanan->status, [\App\Enums\StatusPesanan::Disewa, \App\Enums\StatusPesanan::Terlambat]))
                    <a href="{{ route('admin.pesanan.pengembalian.create', $pesanan->id) }}"
                       class="px-4 py-2 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-extrabold shadow-sm transition inline-flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Proses Pengembalian</span>
                    </a>
                @endif

                @can('perpanjang', $pesanan)
                    <button type="button" @click="showModalPerpanjang = true"
                            class="px-3.5 py-2 rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-800 border border-sky-200 text-xs font-bold transition inline-flex items-center gap-1 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Perpanjang Sewa</span>
                    </button>
                @endcan

                @can('ubahJadwal', $pesanan)
                    <button type="button" @click="showModalJadwal = true"
                            class="px-3.5 py-2 rounded-xl bg-stone-50 hover:bg-stone-100 text-stone-700 border border-stone-200 text-xs font-bold transition inline-flex items-center gap-1 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Ubah Jadwal</span>
                    </button>
                @endcan

                @can('batal', $pesanan)
                    <button type="button" @click="showModalBatal = true"
                            class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold transition inline-flex items-center gap-1 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>Batalkan Pesanan</span>
                    </button>
                @endcan
            </div>
        </div>
