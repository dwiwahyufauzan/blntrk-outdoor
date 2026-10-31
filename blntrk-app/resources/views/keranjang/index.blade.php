<x-layouts.publik>
    <x-slot:title>Keranjang Sewa Perlengkapan - BLNTRK Outdoor</x-slot:title>

    <div class="bg-[#f4f5f6] min-h-screen py-10 sm:py-14">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Breadcrumb & Header Halaman -->
            <div class="mb-8">
                <a href="{{ route('katalog.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-stone-500 hover:text-[#0e1b15] transition mb-3">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Lanjut Pilih Alat di Katalog</span>
                </a>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-widest bg-[#0e1b15] text-[#5edd5b] mb-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#5edd5b]"></span>
                            Keranjang Sewa Outdoor
                        </span>
                        <h1 class="text-2xl sm:text-4xl font-black text-[#0e1b15] font-urbanist tracking-tight">
                            Keranjang Perlengkapan Saya
                        </h1>
                        <p class="text-xs sm:text-sm text-stone-500 mt-1">
                            Atur daftar unit alat pendakian dan tentukan rentang tanggal sewa Anda sebelum checkout.
                        </p>
                    </div>

                    @if($items->isNotEmpty())
                        <div class="flex items-center gap-2">
                            <form method="POST" action="{{ route('keranjang.kosongkan') }}"
                                  data-konfirmasi="warning"
                                  data-judul="Kosongkan Keranjang Sewa"
                                  data-pesan="Apakah Anda yakin ingin mengosongkan seluruh perlengkapan outdoor di keranjang belanja Anda?"
                                  data-tombol="Ya, Kosongkan">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="px-3.5 py-2 rounded-xl bg-white hover:bg-rose-50 text-stone-500 hover:text-rose-600 border border-stone-200 text-xs font-bold transition inline-flex items-center gap-1.5 shadow-2xs cursor-pointer">
                                    <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    <span>Kosongkan Keranjang</span>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Flash Status Alert -->
            @if(session('status'))
                <div class="mb-6 p-4 rounded-2xl bg-[#0e1b15] text-[#5edd5b] text-xs font-bold border border-[#1e3a2c] shadow-xs flex items-center gap-3">
                    <svg class="w-5 h-5 text-[#5edd5b] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded-2xl bg-rose-50 text-rose-800 text-xs font-bold border border-rose-200 shadow-2xs flex items-center gap-3">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($items->isEmpty())
                <!-- EMPTY STATE KERANJANG -->
                <div class="bg-white rounded-3xl p-10 sm:p-14 border border-stone-200/90 shadow-xs text-center mb-12">
                    <div class="w-20 h-20 rounded-3xl bg-stone-100 text-stone-400 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg sm:text-xl font-black text-stone-900 font-urbanist">Keranjang Sewa Masih Kosong</h3>
                    <p class="text-xs sm:text-sm text-stone-500 mt-1.5 max-w-md mx-auto leading-relaxed">
                        Anda belum memasukkan perlengkapan apapun. Jelajahi katalog alat atau pilih Paket Bundling hemat agar petualangan Anda semakin praktis dan terjangkau!
                    </p>
                    <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                        <a href="{{ route('katalog.index') }}"
                           class="px-6 py-3 rounded-2xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-black transition shadow-xs inline-flex items-center gap-2">
                            <span>Jelajahi Katalog Alat</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                        <a href="{{ route('paket-bundling.index') }}"
                           class="px-6 py-3 rounded-2xl bg-[#0e1b15] hover:bg-[#1a3026] text-[#5edd5b] text-xs font-extrabold transition shadow-xs inline-flex items-center gap-2">
                            <span>Lihat Paket Bundling Hemat</span>
                            <svg class="w-4 h-4 text-[#5edd5b]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </a>
                    </div>
                </div>
            @else
                <!-- LAYOUT UTAMA: ITEM KERANJANG + SIDEBAR JADWAL & CHECKOUT -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start mb-12">
                    
                    <!-- DAFTAR ITEM KERANJANG (8 Kolom) -->
                    <div class="lg:col-span-8 space-y-4">
                        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-stone-200/90 shadow-xs">
                            <div class="flex items-center justify-between pb-4 border-b border-stone-100 mb-4">
                                <span class="text-xs font-extrabold uppercase tracking-wider text-stone-500">
                                    Daftar Alat Disewa ({{ $items->count() }} Jenis &bull; {{ $totalUnit }} Unit)
                                </span>
                                <span class="text-xs font-bold text-stone-500 font-mono">
                                    Durasi: {{ $jadwal['durasi'] }} Hari
                                </span>
                            </div>

                            <div class="divide-y divide-stone-100">
                                @foreach($items as $item)
                                    <div class="py-4 first:pt-0 last:pb-0 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                        <!-- Info Produk -->
                                        <div class="flex items-center gap-3.5 min-w-0">
                                            <div class="w-16 h-16 rounded-2xl bg-stone-50 border border-stone-200/80 p-1.5 shrink-0 overflow-hidden flex items-center justify-center">
                                                @if($item->foto_url)
                                                    <img src="{{ $item->foto_url }}" alt="{{ $item->nama }}" class="object-cover w-full h-full rounded-xl">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center text-stone-400">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="min-w-0">
                                                <a href="{{ route('katalog.show', $item->alat_id) }}" class="font-extrabold text-sm sm:text-base text-stone-900 hover:text-[#0e1b15] font-urbanist transition line-clamp-1">
                                                    {{ $item->nama }}
                                                </a>
                                                <div class="text-[11px] text-stone-400 font-mono mt-0.5">
                                                    {{ $item->alat->kode_alat }} &bull; {{ $item->kategori }}
                                                    @if($item->varian)
                                                        &bull; {{ $item->varian }}
                                                    @endif
                                                </div>
                                                <div class="mt-1 flex items-center gap-2">
                                                    <span class="font-bold text-xs text-[#0e1b15] font-mono">
                                                        Rp{{ number_format($item->harga_per_hari, 0, ',', '.') }}
                                                        <span class="text-[10px] text-stone-400 font-normal">/hari</span>
                                                    </span>
                                                    @if($item->cukup)
                                                        <span class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                            Tersedia
                                                        </span>
                                                    @else
                                                        <span class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-rose-50 text-rose-800 border border-rose-200">
                                                            Sisa stok: {{ $item->stok_tersedia }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Kontrol Kuantitas & Subtotal -->
                                        <div class="flex items-center justify-between sm:justify-end gap-5">
                                            <!-- Stepper Jumlah Unit -->
                                            <div class="flex items-center border border-stone-200 rounded-xl overflow-hidden bg-stone-50">
                                                <form method="POST" action="{{ route('keranjang.update', $item->alat_id) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="jumlah" value="{{ $item->jumlah - 1 }}">
                                                    <button type="submit" class="w-8 h-8 flex items-center justify-center text-stone-600 hover:bg-stone-200 transition font-bold text-sm cursor-pointer">
                                                        &minus;
                                                    </button>
                                                </form>

                                                <span class="w-10 text-center font-mono font-bold text-xs text-stone-900">
                                                    {{ $item->jumlah }}
                                                </span>

                                                <form method="POST" action="{{ route('keranjang.update', $item->alat_id) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="jumlah" value="{{ $item->jumlah + 1 }}">
                                                    <button type="submit" class="w-8 h-8 flex items-center justify-center text-stone-600 hover:bg-stone-200 transition font-bold text-sm cursor-pointer">
                                                        &#43;
                                                    </button>
                                                </form>
                                            </div>

                                            <!-- Subtotal Total Durasi -->
                                            <div class="text-right min-w-[100px]">
                                                <span class="text-[10px] text-stone-400 uppercase tracking-wider block">Subtotal</span>
                                                <span class="text-sm font-black font-urbanist text-[#0e1b15] font-mono">
                                                    Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                                                </span>
                                            </div>

                                            <!-- Tombol Hapus -->
                                            <form method="POST" action="{{ route('keranjang.hapus', $item->alat_id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 text-stone-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition cursor-pointer" title="Hapus dari keranjang">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Tombol Lanjut Belanja -->
                        <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                            <a href="{{ route('katalog.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-stone-600 hover:text-[#0e1b15] transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                <span>Tambah Peralatan Lain</span>
                            </a>

                            <a href="{{ route('paket-bundling.index') }}" class="inline-flex items-center gap-1.5 text-xs font-extrabold text-emerald-800 hover:underline">
                                <span>Lihat Paket Bundling 1, 2, 3</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>

                    <!-- SIDEBAR JADWAL & RINGKASAN CHECKOUT (4 Kolom) -->
                    <div class="lg:col-span-4 space-y-5">
                        
                        <!-- Card Penyesuaian Jadwal Sewa -->
                        <div class="bg-white rounded-3xl p-6 border border-stone-200/90 shadow-xs">
                            <h3 class="text-sm font-extrabold text-[#0e1b15] uppercase tracking-wider mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#5edd5b]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>Jadwal Waktu Sewa</span>
                            </h3>

                            <form method="GET" action="{{ route('keranjang.index') }}" class="space-y-3.5">
                                <div>
                                    <label for="mulai" class="block text-[11px] font-bold text-stone-600 uppercase tracking-wider mb-1">
                                        Tanggal Mulai Ambil
                                    </label>
                                    <input type="date" id="mulai" name="mulai" value="{{ $jadwal['mulai'] }}" min="{{ today()->toDateString() }}"
                                           class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs font-semibold py-2.5">
                                </div>

                                <div>
                                    <label for="selesai" class="block text-[11px] font-bold text-stone-600 uppercase tracking-wider mb-1">
                                        Batas Pengembalian (12:00 WIB)
                                    </label>
                                    <input type="date" id="selesai" name="selesai" value="{{ $jadwal['selesai'] }}" min="{{ $jadwal['mulai'] }}"
                                           class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs font-semibold py-2.5">
                                </div>

                                <button type="submit"
                                        class="w-full py-2.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs font-bold transition flex items-center justify-center gap-1.5 cursor-pointer">
                                    <svg class="w-3.5 h-3.5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    <span>Terapkan Perubahan Tanggal</span>
                                </button>
                            </form>
                        </div>

                        <!-- Card Rincian Estimasi Biaya & Tombol Checkout -->
                        <div class="bg-[#0e1b15] text-white rounded-3xl p-6 shadow-xl">
                            <span class="text-[10px] font-black uppercase tracking-widest text-[#5edd5b] block mb-1">
                                Estimasi Reservasi
                            </span>
                            <h3 class="text-xl font-black font-urbanist text-white mb-4">
                                Ringkasan Biaya
                            </h3>

                            <div class="space-y-2.5 text-xs text-stone-300 pb-4 border-b border-white/10">
                                <div class="flex justify-between">
                                    <span>Total Kuantitas Alat:</span>
                                    <span class="font-bold text-white">{{ $totalUnit }} Unit</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Tarif Sewa Harian:</span>
                                    <span class="font-mono font-bold text-white">Rp{{ number_format($totalPerHari, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Durasi Penggunaan:</span>
                                    <span class="font-bold text-[#5edd5b]">{{ $jadwal['durasi'] }} Hari (x24 Jam)</span>
                                </div>
                                @if($diskonPaket['diskon'] > 0)
                                    <div class="flex justify-between">
                                        <span>Subtotal Eceran:</span>
                                        <span class="font-mono font-bold text-white">Rp{{ number_format($subtotalKotor, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between text-[#5edd5b]">
                                        <span>Diskon {{ $diskonPaket['paket']->nama }}{{ $diskonPaket['jumlah_paket'] > 1 ? ' x'.$diskonPaket['jumlah_paket'] : '' }}:</span>
                                        <span class="font-mono font-bold">-Rp{{ number_format($diskonPaket['diskon'], 0, ',', '.') }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="pt-4 pb-5 flex items-baseline justify-between">
                                <span class="text-xs uppercase tracking-wider font-extrabold text-stone-400">Total Sewa:</span>
                                <div class="text-right">
                                    <span class="text-2xl font-black text-white font-urbanist font-mono block">
                                        Rp{{ number_format($grandTotal, 0, ',', '.') }}
                                    </span>
                                    <span class="text-[10px] text-stone-400">Bayar di toko saat pengambilan unit</span>
                                </div>
                            </div>

                            <!-- Tombol Checkout -->
                            <form method="POST" action="{{ route('keranjang.checkout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full py-3.5 px-4 rounded-2xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-sm font-black transition shadow-lg flex items-center justify-center gap-2 cursor-pointer font-urbanist hover:scale-[1.02]">
                                    <span>Lanjut ke Formulir Pesanan</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>
                            </form>

                            <div class="mt-4 pt-3 border-t border-white/10 text-[11px] text-stone-400 space-y-1">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-[#5edd5b] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Bisa bayar tunai / QRIS di kasir Subang</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-[#5edd5b] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Wajib membawa kartu identitas fisik asli</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            @endif

            <!-- SECTION REKOMENDASI PAKET BUNDLING HEMAT (PAKET 1, 2, 3) -->
            @if($paketBundling->isNotEmpty())
                <div class="mt-12 pt-10 border-t border-stone-200/90">
                    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6">
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#1e3a2c] block mb-1">Pilihan Praktis &amp; Hemat</span>
                            <h2 class="text-xl sm:text-2xl font-black text-[#0e1b15] font-urbanist tracking-tight">
                                Paket Bundling Outdoor Hemat (Paket 1, 2, 3)
                            </h2>
                            <p class="text-xs text-stone-500 mt-0.5">
                                Mau lebih praktis tanpa pilih alat satu per satu? Masukkan paket bundling langsung ke keranjang dengan harga diskon khusus.
                            </p>
                        </div>
                        <a href="{{ route('paket-bundling.index') }}" class="text-xs font-extrabold text-[#0e1b15] hover:text-[#5edd5b] inline-flex items-center gap-1">
                            <span>Lihat Semua Paket Bundling</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach($paketBundling as $p)
                            <div class="bg-white rounded-3xl border border-stone-200/90 p-5 sm:p-6 shadow-xs hover:shadow-lg transition flex flex-col justify-between group">
                                <div>
                                    <div class="flex items-center justify-between gap-2 mb-3">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-[#0e1b15] text-[#5edd5b]">
                                            Paket {{ $p->nomor_paket }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-rose-100 text-rose-800 border border-rose-200">
                                            Hemat Rp{{ number_format($p->hemat_per_hari, 0, ',', '.') }}/hari
                                        </span>
                                    </div>

                                    <h3 class="font-urbanist font-black text-base text-stone-900 group-hover:text-[#0e1b15] transition">
                                        {{ $p->nama }}
                                    </h3>
                                    <span class="text-[11px] font-bold text-stone-400 block mt-0.5">
                                        Kapasitas: {{ $p->kapasitas_orang }}
                                    </span>

                                    <!-- Daftar Kelengkapan -->
                                    <div class="mt-4 pt-3 border-t border-stone-100 space-y-1.5 text-xs text-stone-600">
                                        @foreach($p->items->take(4) as $it)
                                            <div class="flex items-center gap-2">
                                                <svg class="w-3.5 h-3.5 text-[#5edd5b] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                <span class="truncate">{{ $it->jumlah }}x {{ $it->alat?->nama ?? 'Alat' }}</span>
                                            </div>
                                        @endforeach
                                        @if($p->items->count() > 4)
                                            <span class="text-[11px] text-stone-400 italic block pl-5">+ {{ $p->items->count() - 4 }} alat pendukung lainnya</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="mt-6 pt-4 border-t border-stone-100">
                                    <div class="flex items-baseline justify-between mb-3">
                                        <div>
                                            <span class="text-[10px] text-stone-400 line-through block font-mono">Rp{{ number_format($p->harga_asli_per_hari, 0, ',', '.') }}</span>
                                            <span class="font-urbanist font-black text-lg text-stone-900 font-mono">
                                                Rp{{ number_format($p->harga_paket_per_hari, 0, ',', '.') }}
                                                <span class="text-[10px] text-stone-500 font-normal">/hari</span>
                                            </span>
                                        </div>
                                        <span class="text-[10px] font-extrabold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                            Diskon {{ $p->persen_hemat }}%
                                        </span>
                                    </div>

                                    <form method="POST" action="{{ route('keranjang.tambah-paket', $p->id) }}">
                                        @csrf
                                        <button type="submit"
                                                class="w-full py-2.5 rounded-xl bg-[#0e1b15] hover:bg-[#1a3026] text-[#5edd5b] text-xs font-black transition shadow-xs flex items-center justify-center gap-1.5 cursor-pointer">
                                            <svg class="w-4 h-4 text-[#5edd5b]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            <span>Masukkan Paket ke Keranjang</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-layouts.publik>

