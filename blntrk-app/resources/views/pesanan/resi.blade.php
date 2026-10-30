<x-layouts.publik>
    <x-slot:title>Resi Sewa #{{ $pesanan->no_resi }} - BLNTRK Outdoor</x-slot:title>

    <div x-data="{ showModalBatal: false }" class="bg-stone-100 min-h-screen py-8 sm:py-12 print:bg-white print:py-0 print:m-0">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 print:p-0 print:max-w-none">

            <!-- ACTION TOOLBAR (Hanya tampil di layar web, sembunyi saat cetak) -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden">
                <a href="{{ route('pesanan.show', $pesanan->no_resi) }}"
                   class="inline-flex items-center gap-1.5 text-xs font-bold text-stone-600 hover:text-stone-900 transition bg-white px-3.5 py-2 rounded-xl border border-stone-200/90 shadow-2xs">
                    <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali ke Detail Pesanan</span>
                </a>

                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" onclick="window.print()"
                            class="px-4 py-2 rounded-xl bg-white hover:bg-stone-50 text-stone-800 border border-stone-300 text-xs font-bold transition inline-flex items-center gap-2 shadow-2xs cursor-pointer">
                        <svg class="w-4 h-4 text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Bukti Resi</span>
                    </button>

                    <a href="{{ route('pesanan.pdf', $pesanan->no_resi) }}"
                       class="px-4 py-2 rounded-xl bg-white hover:bg-stone-50 text-stone-800 border border-stone-300 text-xs font-bold transition inline-flex items-center gap-2 shadow-2xs">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Unduh PDF Resi</span>
                    </a>

                    @php
                        $waToko = preg_replace('/\D/', '', \App\Models\Pengaturan::ambil('no_wa_toko', '6281234567890'));
                        $pesanWa = rawurlencode("Halo BLNTRK Outdoor, saya ingin konfirmasi pesanan sewa dengan nomor resi " . $pesanan->no_resi . " atas nama " . $pesanan->pelanggan->nama_lengkap . ".");
                    @endphp
                    <a href="https://wa.me/{{ $waToko }}?text={{ $pesanWa }}" target="_blank"
                       class="px-4 py-2 rounded-xl bg-[#0e1b15] hover:bg-[#1a3327] text-[#5edd5b] text-xs font-extrabold transition inline-flex items-center gap-2 shadow-xs">
                        <svg class="w-4 h-4 text-[#5edd5b]" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.173.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.159.57 4.184 1.564 5.938l-1.564 5.714 5.861-1.537c1.7 1.006 3.684 1.585 5.807 1.585 6.627 0 12-5.373 12-12 0-6.627-5.373-12-12-12z"/></svg>
                        <span>Konfirmasi WhatsApp</span>
                    </a>

                    @can('batal', $pesanan)
                        <button type="button" @click="showModalBatal = true"
                                class="px-4 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold transition inline-flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span>Batalkan Pesanan</span>
                        </button>
                    @endcan
                </div>
            </div>

            @if(session('status'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 text-emerald-800 text-xs font-bold border border-emerald-200 shadow-2xs flex items-center gap-3 print:hidden">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- LEMBAR DOKUMEN CETAK RESMI (A4 PREVIEW) -->
            <div class="bg-white rounded-none sm:rounded-lg shadow-xl sm:border sm:border-stone-200/90 p-6 sm:p-10 text-stone-900 print:shadow-none print:border-none print:p-0 print:m-0 font-sans">
                
                <!-- KOP SURAT RESMI RESI -->
                <div class="border-b-[3px] border-double border-[#0e1b15] pb-4 mb-5 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                    <div>
                        <div class="text-xl sm:text-2xl font-black text-[#0e1b15] tracking-tight uppercase font-urbanist">
                            {{ \App\Models\Pengaturan::ambil('nama_toko', 'BLNTRK Outdoor') }}
                        </div>
                        <div class="text-xs text-stone-600 font-medium mt-1 leading-relaxed">
                            {{ \App\Models\Pengaturan::ambil('alamat_toko', 'Jl. Raya Jalancagak No. 12, Subang, Jawa Barat') }}
                        </div>
                        <div class="text-[11px] text-stone-500 mt-0.5">
                            WhatsApp: {{ \App\Models\Pengaturan::ambil('no_wa_toko', '0812-3456-7890') }} &bull; Email: blntrkoutdoor@gmail.com &bull; Buka 24 Jam
                        </div>
                    </div>

                    <div class="text-left sm:text-right shrink-0">
                        <div class="text-[10px] font-extrabold uppercase tracking-wider text-stone-500">
                            Bukti Reservasi Sewa & Resi Transaksi
                        </div>
                        <div class="text-lg sm:text-xl font-mono font-black text-[#0e1b15] tracking-wider mt-0.5">
                            #{{ $pesanan->no_resi }}
                        </div>
                        <div class="mt-1 flex items-center sm:justify-end gap-2">
                            @php
                                $statusBadge = match($pesanan->status->value) {
                                    'dibayar' => 'bg-emerald-50 text-emerald-800 border-emerald-300',
                                    'disewa' => 'bg-sky-50 text-sky-800 border-sky-300',
                                    'selesai' => 'bg-teal-50 text-teal-800 border-teal-300',
                                    'menunggu' => 'bg-amber-50 text-amber-900 border-amber-300',
                                    'terlambat' => 'bg-rose-50 text-rose-900 border-rose-300',
                                    default => 'bg-stone-100 text-stone-700 border-stone-300',
                                };
                            @endphp
                            <span class="inline-block px-2.5 py-0.5 rounded text-[10px] font-extrabold uppercase tracking-wider border {{ $statusBadge }}">
                                {{ $pesanan->status->label() }}
                            </span>
                        </div>
                        <div class="text-[10px] text-stone-400 font-mono mt-1">
                            Tgl Reservasi: {{ $pesanan->created_at->format('d/m/Y H:i') }} WIB
                        </div>
                    </div>
                </div>

                <!-- DUA KOLOM INFORMASI: IDENTITAS & JADWAL SEWA -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4 text-xs">
                    <!-- Identitas Penyewa -->
                    <div class="p-3.5 bg-stone-50 border border-stone-200 rounded-md">
                        <div class="text-[10px] font-extrabold uppercase tracking-wider text-stone-500 border-b border-stone-200 pb-1 mb-2">
                            IDENTITAS PENYEWA (PIHAK KEDUA)
                        </div>
                        <div class="text-sm font-bold text-stone-900 font-urbanist">
                            {{ $pesanan->pelanggan->nama_lengkap }}
                        </div>
                        <div class="font-mono text-stone-700 mt-0.5">
                            No. WhatsApp: {{ $pesanan->pelanggan->no_hp }}
                        </div>
                        <div class="text-stone-600 mt-1 leading-relaxed">
                            {{ $pesanan->pelanggan->alamat }}
                        </div>
                    </div>

                    <!-- Jadwal & Ketentuan Waktu -->
                    <div class="p-3.5 bg-stone-50 border border-stone-200 rounded-md">
                        <div class="text-[10px] font-extrabold uppercase tracking-wider text-stone-500 border-b border-stone-200 pb-1 mb-2">
                            JADWAL & WAKTU SEWA
                        </div>
                        <div class="space-y-1">
                            <div class="flex justify-between">
                                <span class="text-stone-500">Tgl Pengambilan:</span>
                                <span class="font-bold text-stone-900">{{ $pesanan->tanggal_mulai->translatedFormat('d F Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-stone-500">Batas Pengembalian:</span>
                                <span class="font-bold text-stone-900">{{ $pesanan->tanggal_selesai->translatedFormat('d F Y') }} (12:00 WIB)</span>
                            </div>
                            <div class="flex justify-between border-t border-stone-200 pt-1 mt-1">
                                <span class="text-stone-500">Durasi Sewa:</span>
                                <span class="font-bold text-[#0e1b15]">{{ $pesanan->durasi_hari }} Hari Penuh</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STATUS PEMBAYARAN SEWA & JAMINAN FISIK (JIKA ADA) -->
                @php
                    $adaBayar = $pesanan->pembayaran && $pesanan->pembayaran->isNotEmpty();
                    $bayarPertama = $adaBayar ? $pesanan->pembayaran->first() : null;
                    $totalBayar = $adaBayar ? $pesanan->pembayaran->sum('jumlah') : 0;
                @endphp
                @if($adaBayar || $pesanan->jaminan)
                    <div class="grid grid-cols-1 {{ $adaBayar && $pesanan->jaminan ? 'sm:grid-cols-2' : '' }} gap-3 mb-4 text-xs">
                        @if($adaBayar)
                            <div class="p-3 bg-emerald-50/60 border border-emerald-200 rounded-md">
                                <div class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-800 border-b border-emerald-200 pb-1 mb-1.5 flex items-center justify-between">
                                    <span>PEMBAYARAN BIAYA SEWA</span>
                                    <span class="text-[9px] font-black bg-emerald-100 text-emerald-900 px-1.5 py-0.2 rounded border border-emerald-300">LUNAS</span>
                                </div>
                                <div class="flex justify-between items-center text-stone-700">
                                    <span>Total Terbayar:</span>
                                    <span class="font-mono font-bold text-emerald-900">Rp{{ number_format($totalBayar, 0, ',', '.') }}</span>
                                </div>
                                <div class="text-[11px] text-stone-500 mt-1">
                                    Metode: <strong>{{ $bayarPertama->metode->label() }}</strong> &bull; {{ $bayarPertama->dibayar_pada?->format('d/m/Y H:i') }} WIB
                                </div>
                            </div>
                        @endif

                        @if($pesanan->jaminan)
                            <div class="p-3 bg-sky-50/60 border border-sky-200 rounded-md">
                                <div class="text-[10px] font-extrabold uppercase tracking-wider text-sky-800 border-b border-sky-200 pb-1 mb-1.5 flex items-center justify-between">
                                    <span>JAMINAN IDENTITAS FISIK</span>
                                    <span class="text-[9px] font-black {{ $pesanan->jaminan->dikembalikan_pada ? 'bg-teal-100 text-teal-900 border-teal-300' : 'bg-amber-100 text-amber-900 border-amber-300' }} px-1.5 py-0.2 rounded border">
                                        {{ $pesanan->jaminan->dikembalikan_pada ? 'DIKEMBALIKAN' : 'DITAHAN' }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center text-stone-700">
                                    <span>Kartu: <strong>{{ is_string($pesanan->jaminan->jenis) ? strtoupper($pesanan->jaminan->jenis) : $pesanan->jaminan->jenis->label() }} (Asli)</strong></span>
                                    <span class="font-mono font-bold text-stone-800">{{ $pesanan->jaminan->nomor_identitas_samar ?? $pesanan->jaminan->nomor_identitas }}</span>
                                </div>
                                <div class="text-[11px] text-stone-500 mt-1">
                                    Status: {{ $pesanan->jaminan->dikembalikan_pada ? 'Telah diserahkan kembali pada ' . $pesanan->jaminan->dikembalikan_pada->format('d/m/Y H:i') : 'Disimpan di kasir toko sebagai jaminan alat' }}
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- TABEL RINCIAN ALAT YANG DISEWA -->
                <div class="mb-4">
                    <div class="text-[11px] font-extrabold uppercase tracking-wider text-stone-700 mb-1.5">
                        Rincian Perlengkapan yang Disewa:
                    </div>
                    <div class="overflow-x-auto border border-stone-200 rounded-md">
                        <table class="w-full text-xs text-left border-collapse">
                            <thead class="bg-[#0e1b15] text-white text-[10px] font-bold uppercase tracking-wider">
                                <tr>
                                    <th class="py-2 px-3 text-center w-12 border-r border-[#1a3327]">No</th>
                                    <th class="py-2 px-3 border-r border-[#1a3327]">Nama Perlengkapan Outdoor</th>
                                    <th class="py-2 px-3 text-center w-20 border-r border-[#1a3327]">Jumlah</th>
                                    <th class="py-2 px-3 text-right w-28 border-r border-[#1a3327]">Tarif / Hari</th>
                                    <th class="py-2 px-3 text-right w-32">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-200 text-stone-800">
                                @foreach($pesanan->detail as $index => $item)
                                    <tr class="{{ $index % 2 === 1 ? 'bg-stone-50' : 'bg-white' }}">
                                        <td class="py-2 px-3 text-center font-mono text-stone-500 border-r border-stone-200">{{ $index + 1 }}</td>
                                        <td class="py-2 px-3 border-r border-stone-200">
                                            <div class="font-bold text-stone-900 font-urbanist">{{ $item->alat?->nama ?? 'Alat Outdoor' }}</div>
                                            <div class="text-[10px] text-stone-500 font-mono">Kode: {{ $item->alat?->kode_alat }} &bull; {{ $item->alat?->kategori?->nama }}</div>
                                        </td>
                                        <td class="py-2 px-3 text-center font-bold text-stone-900 border-r border-stone-200">{{ $item->jumlah }} unit</td>
                                        <td class="py-2 px-3 text-right font-mono text-stone-700 border-r border-stone-200">Rp{{ number_format($item->harga_per_hari, 0, ',', '.') }}</td>
                                        <td class="py-2 px-3 text-right font-mono font-bold text-stone-900">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-stone-100 border-t-2 border-stone-300 font-bold text-stone-900">
                                <tr>
                                    <td colspan="4" class="py-2 px-3 text-right uppercase tracking-wider text-[11px] border-r border-stone-300">
                                        TOTAL BIAYA SEWA ({{ $pesanan->durasi_hari }} HARI):
                                    </td>
                                    <td class="py-2 px-3 text-right font-mono font-black text-sm text-[#0e1b15]">
                                        Rp{{ number_format($pesanan->total_sewa, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- TABEL RINCIAN DENDA / GANTI RUGI (JIKA ADA) -->
                @if($pesanan->denda && $pesanan->denda->isNotEmpty())
                    @php
                        $totalDendaPesanan = $pesanan->denda->sum('jumlah');
                        $totalDendaLunas = $pesanan->denda->where('status.value', 'lunas')->sum('jumlah');
                        $sisaDenda = $totalDendaPesanan - $totalDendaLunas;
                    @endphp
                    <div class="mb-4">
                        <div class="text-[11px] font-extrabold uppercase tracking-wider text-rose-800 mb-1.5 flex items-center gap-1.5">
                            <span>Rincian Denda & Penggantian Kerusakan / Kehilangan:</span>
                        </div>
                        <div class="overflow-x-auto border border-rose-200 rounded-md">
                            <table class="w-full text-xs text-left border-collapse">
                                <thead class="bg-[#742a2a] text-white text-[10px] font-bold uppercase tracking-wider">
                                    <tr>
                                        <th class="py-2 px-3 text-center w-12 border-r border-rose-800">No</th>
                                        <th class="py-2 px-3 border-r border-rose-800 w-44">Kategori Denda</th>
                                        <th class="py-2 px-3 border-r border-rose-800">Keterangan / Kondisi</th>
                                        <th class="py-2 px-3 text-center w-28 border-r border-rose-800">Status</th>
                                        <th class="py-2 px-3 text-right w-32">Nominal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-rose-100 text-stone-800">
                                    @foreach($pesanan->denda as $idxDenda => $d)
                                        <tr class="{{ $idxDenda % 2 === 1 ? 'bg-rose-50/40' : 'bg-white' }}">
                                            <td class="py-2 px-3 text-center font-mono text-stone-500 border-r border-rose-100">{{ $idxDenda + 1 }}</td>
                                            <td class="py-2 px-3 font-bold text-stone-900 border-r border-rose-100">
                                                {{ $d->kategoriDenda?->nama ?? $d->jenis->label() }}
                                            </td>
                                            <td class="py-2 px-3 text-stone-600 border-r border-rose-100 leading-relaxed">{{ $d->keterangan ?? '-' }}</td>
                                            <td class="py-2 px-3 text-center border-r border-rose-100">
                                                @if($d->status->value === 'lunas')
                                                    <span class="inline-block px-2 py-0.5 rounded text-[9px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300">LUNAS</span>
                                                @else
                                                    <span class="inline-block px-2 py-0.5 rounded text-[9px] font-black bg-rose-100 text-rose-800 border border-rose-300">BELUM LUNAS</span>
                                                @endif
                                            </td>
                                            <td class="py-2 px-3 text-right font-mono font-bold text-rose-700">Rp{{ number_format($d->jumlah, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-rose-50 border-t-2 border-rose-200 font-bold text-rose-950">
                                    <tr>
                                        <td colspan="4" class="py-2 px-3 text-right uppercase tracking-wider text-[11px] border-r border-rose-200">
                                            TOTAL DENDA / GANTI RUGI:
                                        </td>
                                        <td class="py-2 px-3 text-right font-mono font-black text-rose-800">
                                            Rp{{ number_format($totalDendaPesanan, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    @if($sisaDenda > 0)
                                        <tr class="bg-rose-100 text-rose-900">
                                            <td colspan="4" class="py-2 px-3 text-right uppercase tracking-wider text-[11px] border-r border-rose-200">
                                                SISA DENDA WAJIB DIBAYAR (JAMINAN DITAHAN):
                                            </td>
                                            <td class="py-2 px-3 text-right font-mono font-black text-rose-900">
                                                Rp{{ number_format($sisaDenda, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endif
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- REKAPITULASI BIAYA & SOP PENGEMBALIAN -->
                @php
                    $grandTotal = $pesanan->total_sewa + ($pesanan->denda ? $pesanan->denda->sum('jumlah') : 0);
                    $totalTerbayarSemua = $totalBayar + ($pesanan->denda ? $pesanan->denda->where('status.value', 'lunas')->sum('jumlah') : 0);
                    $totalKurangBayar = max(0, $grandTotal - $totalTerbayarSemua);
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <!-- SOP KETENTUAN -->
                    <div class="p-3.5 bg-stone-50 border border-stone-200 rounded-md text-xs text-stone-600">
                        <div class="text-[10px] font-extrabold uppercase tracking-wider text-[#0e1b15] mb-1.5 flex items-center gap-1.5">
                            <span>Ketentuan Rental & Pengembalian:</span>
                        </div>
                        <ol class="list-decimal list-inside space-y-1 pl-1 text-[11px] leading-relaxed">
                            <li>Tunjukkan lembar resi ini (cetak atau layar handphone) saat serah terima alat.</li>
                            <li>Wajib meninggalkan kartu identitas fisik asli (KTP/SIM) yang ditahan selama masa sewa.</li>
                            <li>Batas pengembalian alat maksimal pukul <strong>12:00 WIB</strong> pada tanggal selesai. Keterlambatan dikenakan tarif sewa harian.</li>
                            <li>Alat yang rusak atau hilang wajib diganti rugi sesuai harga beli/servis sebelum jaminan dikembalikan.</li>
                        </ol>
                    </div>

                    <!-- KOTAK REKAPITULASI GRAND TOTAL -->
                    <div class="border border-stone-200 rounded-md overflow-hidden text-xs">
                        <table class="w-full">
                            <tr class="border-b border-stone-100">
                                <td class="py-2 px-3 text-stone-600">Biaya Sewa Alat ({{ $pesanan->durasi_hari }} Hari):</td>
                                <td class="py-2 px-3 text-right font-mono font-bold text-stone-900">Rp{{ number_format($pesanan->total_sewa, 0, ',', '.') }}</td>
                            </tr>
                            @if($pesanan->denda && $pesanan->denda->isNotEmpty())
                                <tr class="border-b border-stone-100 bg-rose-50/30">
                                    <td class="py-2 px-3 text-rose-700">Total Denda / Penggantian:</td>
                                    <td class="py-2 px-3 text-right font-mono font-bold text-rose-700">Rp{{ number_format($pesanan->denda->sum('jumlah'), 0, ',', '.') }}</td>
                                </tr>
                            @endif
                            <tr class="border-b border-stone-200 bg-stone-100 font-bold">
                                <td class="py-2.5 px-3 uppercase text-[11px] text-[#0e1b15]">TOTAL KESELURUHAN:</td>
                                <td class="py-2.5 px-3 text-right font-mono font-black text-sm text-[#0e1b15]">Rp{{ number_format($grandTotal, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="border-b border-stone-100">
                                <td class="py-2 px-3 text-emerald-800">Total Pembayaran Diterima:</td>
                                <td class="py-2 px-3 text-right font-mono font-bold text-emerald-800">Rp{{ number_format($totalTerbayarSemua, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="{{ $totalKurangBayar > 0 ? 'bg-rose-50 text-rose-900' : 'bg-emerald-50 text-emerald-900' }} font-bold">
                                <td class="py-2.5 px-3 uppercase text-[11px]">
                                    {{ $totalKurangBayar > 0 ? 'SISA TAGIHAN WAJIB DIBAYAR:' : 'STATUS PEMBAYARAN:' }}
                                </td>
                                <td class="py-2.5 px-3 text-right font-mono font-black text-sm">
                                    {{ $totalKurangBayar > 0 ? 'Rp' . number_format($totalKurangBayar, 0, ',', '.') : 'LUNAS' }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- TANDA TANGAN RESMI DUA PIHAK -->
                <div class="pt-4 border-t border-stone-200 grid grid-cols-2 text-center text-xs">
                    <div>
                        <div class="text-stone-600">Penyewa (Pihak Kedua),</div>
                        <div class="h-16"></div>
                        <div class="font-bold underline text-stone-900 font-urbanist text-sm">
                            {{ $pesanan->pelanggan->nama_lengkap }}
                        </div>
                        <div class="text-[10px] text-stone-500 mt-0.5">Tanda Tangan & Nama Terang</div>
                    </div>
                    <div>
                        <div class="text-stone-600">
                            Subang, {{ now()->translatedFormat('d F Y') }}<br>
                            Petugas Kasir Toko (Pihak Pertama),
                        </div>
                        <div class="h-12"></div>
                        <div class="font-bold underline text-stone-900 font-urbanist text-sm">
                            {{ $petugas ?? 'Admin Kasir BLNTRK' }}
                        </div>
                        <div class="text-[10px] text-stone-500 mt-0.5">BLNTRK Outdoor Subang</div>
                    </div>
                </div>

            </div>

        </div>

        <!-- =================== MODAL: BATALKAN OLEH PELANGGAN =================== -->
        @can('batal', $pesanan)
            <div x-show="showModalBatal"
                 x-cloak
                 class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 print:hidden">
                <div @click.away="showModalBatal = false"
                     class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-stone-200/90 relative">
                    
                    <div class="flex items-center justify-between pb-4 border-b border-stone-100 mb-5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-[#14201a]">Batalkan Pesanan</h3>
                                <span class="text-[11px] text-stone-500">Resi #{{ $pesanan->no_resi }}</span>
                            </div>
                        </div>
                        <button type="button" @click="showModalBatal = false" class="text-stone-400 hover:text-stone-700 p-1 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('pesanan.batal', $pesanan->no_resi) }}" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        
                        <p class="text-xs text-stone-600 leading-relaxed">
                            Apakah Anda yakin ingin membatalkan pesanan ini? Stok alat yang telah Anda pesan akan dilepaskan kembali ke katalog publik.
                        </p>

                        <div>
                            <label for="alasan_batal_pelanggan" class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1">
                                Alasan Pembatalan <span class="text-rose-500">*</span>
                            </label>
                            <textarea id="alasan_batal_pelanggan" name="alasan_batal" rows="3" required placeholder="Tuliskan alasan pembatalan Anda..."
                                      class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs">{{ old('alasan_batal') }}</textarea>
                        </div>

                        <div class="pt-3 flex items-center justify-end gap-2 border-t border-stone-100">
                            <button type="button" @click="showModalBatal = false"
                                    class="px-4 py-2.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold transition">
                                Kembali
                            </button>
                            <button type="submit"
                                    class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-extrabold shadow-sm transition">
                                Ya, Batalkan Pesanan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endcan

    </div>

    <!-- Script auto-print jika terdapat parameter cetak=1 pada query string -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('cetak') === '1') {
                window.print();
            }
        });
    </script>
</x-layouts.publik>