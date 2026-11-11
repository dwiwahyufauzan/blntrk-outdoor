            <!-- Navigasi Balik & Aksi Utama Atas -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <a href="{{ route('pesanan.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-stone-500 hover:text-[#0e1b15] transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali ke Daftar Pesanan</span>
                </a>

                <div class="flex flex-wrap items-center gap-2">
                    <!-- Tombol Lihat Lembar Resi -->
                    <a href="{{ route('pesanan.resi', $pesanan->no_resi) }}"
                       class="px-4 py-2.5 rounded-xl bg-white hover:bg-stone-50 text-stone-800 border border-stone-200 text-xs font-extrabold transition inline-flex items-center gap-2 shadow-2xs">
                        <svg class="w-4 h-4 text-[#0e1b15]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Lihat Lembar Resi</span>
                    </a>

                    <!-- Tombol Unduh PDF Resi -->
                    <a href="{{ route('pesanan.pdf', $pesanan->no_resi) }}"
                       class="px-3.5 py-2.5 rounded-xl bg-white hover:bg-stone-50 text-stone-700 border border-stone-200 text-xs font-bold transition inline-flex items-center gap-1.5 shadow-2xs">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span class="hidden sm:inline">Unduh PDF</span>
                    </a>

                    <!-- Tombol Batalkan Pesanan (Khusus Status Menunggu) -->
                    @can('batal', $pesanan)
                        <button type="button" @click="showModalBatal = true"
                                class="px-4 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-extrabold transition inline-flex items-center gap-1.5 shadow-2xs cursor-pointer">
                            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span>Batalkan Pesanan</span>
                        </button>
                    @endcan

                    <!-- WhatsApp Toko -->
                    @php
                        $waToko = \App\Support\PengaturanToko::noWaDigit();
                        $pesanWa = rawurlencode("Halo BLNTRK Outdoor, saya ingin konfirmasi pesanan sewa dengan nomor resi " . $pesanan->no_resi . " atas nama " . $pesanan->pelanggan->nama_lengkap . ".");
                    @endphp
                    <a href="https://wa.me/{{ $waToko }}?text={{ $pesanWa }}" target="_blank"
                       class="px-4 py-2.5 rounded-xl bg-[#0e1b15] hover:bg-[#1e3a2c] text-[#5edd5b] text-xs font-extrabold transition inline-flex items-center gap-1.5 shadow-xs">
                        <svg class="w-4 h-4 text-[#5edd5b]" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.173.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.159.57 4.184 1.564 5.938l-1.564 5.714 5.861-1.537c1.7 1.006 3.684 1.585 5.807 1.585 6.627 0 12-5.373 12-12 0-6.627-5.373-12-12-12z"/></svg>
                        <span>WhatsApp Toko</span>
                    </a>
                </div>
            </div>
