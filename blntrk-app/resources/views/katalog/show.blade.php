<x-layouts.publik title="{{ $alat->nama }} - BLNTRK Outdoor">
    <!-- Breadcrumb -->
    <div class="bg-white border-b border-stone-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5">
            <nav class="flex items-center gap-2 text-xs text-stone-500 font-medium">
                <a href="{{ route('beranda') }}" class="hover:text-[#0e1b15] transition">Beranda</a>
                <span>/</span>
                <a href="{{ route('katalog.index') }}" class="hover:text-[#0e1b15] transition">Katalog</a>
                <span>/</span>
                <a href="{{ route('katalog.index', ['kategori' => $alat->kategori?->slug]) }}" class="hover:text-[#0e1b15] transition">{{ $alat->kategori?->nama }}</a>
                <span>/</span>
                <span class="text-[#14201a] font-bold truncate max-w-xs">{{ $alat->nama }}</span>
            </nav>
        </div>
    </div>

    <!-- Detail Konten Alat -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Kolom Kiri: Foto & Deskripsi Alat (7 Kolom) -->
            <div class="lg:col-span-7 space-y-6">
                <!-- Foto Utama -->
                <div class="bg-white rounded-3xl border border-stone-200/90 shadow-sm overflow-hidden relative group">
                    <div class="aspect-4/3 bg-stone-50 flex items-center justify-center p-6">
                        @if($alat->foto)
                            <img src="{{ asset('storage/' . $alat->foto) }}" alt="{{ $alat->nama }}"
                                class="w-full h-full object-contain drop-shadow-md group-hover:scale-105 transition duration-300">
                        @else
                            @php
                                $kategoriNama = strtolower($alat->kategori?->nama ?? '');
                                $dummyImg = 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?auto=format&fit=crop&w=1000&q=80';
                                if (str_contains($kategoriNama, 'tenda')) {
                                    $dummyImg = 'https://images.unsplash.com/photo-1510312305653-8ed496efae75?auto=format&fit=crop&w=1000&q=80';
                                } elseif (str_contains($kategoriNama, 'carrier') || str_contains($kategoriNama, 'tas')) {
                                    $dummyImg = 'https://images.unsplash.com/photo-1622560480605-d83c853bc5c3?auto=format&fit=crop&w=1000&q=80';
                                } elseif (str_contains($kategoriNama, 'tidur') || str_contains($kategoriNama, 'matras')) {
                                    $dummyImg = 'https://images.unsplash.com/photo-1520095972714-909e91b038e5?auto=format&fit=crop&w=1000&q=80';
                                } elseif (str_contains($kategoriNama, 'masak')) {
                                    $dummyImg = 'https://images.unsplash.com/photo-1470246973918-29a93221c455?auto=format&fit=crop&w=1000&q=80';
                                }
                            @endphp
                            <div class="w-full h-full relative overflow-hidden rounded-2xl">
                                <img src="{{ $dummyImg }}" alt="{{ $alat->nama }}" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent flex items-end p-4">
                                    <span class="text-xs font-bold text-white tracking-wider uppercase">BLNTRK Outdoor Gear</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Badge Kategori & Kode -->
                    <div class="absolute top-4 left-4 flex items-center gap-2">
                        <span class="px-3 py-1 rounded-full bg-[#0e1b15]/90 backdrop-blur-xs text-[#5edd5b] text-[11px] font-extrabold uppercase tracking-wider shadow-xs">
                            {{ $alat->kategori?->nama ?? 'Perlengkapan' }}
                        </span>
                        <span class="px-2.5 py-1 rounded-full bg-white/95 backdrop-blur-xs text-[#14201a] text-[11px] font-mono font-bold shadow-xs border border-stone-200">
                            {{ $alat->kode_alat }}
                        </span>
                    </div>
                </div>

                <!-- Tab Informasi & Spesifikasi -->
                <div class="bg-white rounded-3xl border border-stone-200/90 shadow-sm p-6 sm:p-8">
                    <h2 class="text-lg font-black text-[#14201a] tracking-tight mb-3">Deskripsi & Kelengkapan</h2>
                    <div class="prose prose-stone text-xs sm:text-sm text-stone-600 leading-relaxed">
                        @if($alat->deskripsi)
                            <p class="whitespace-pre-line">{{ $alat->deskripsi }}</p>
                        @else
                            <p>Perlengkapan outdoor pilihan yang dirawat secara berkala di markas BLNTRK Outdoor Kumpay, Jalancagak. Seluruh unit dicuci bersih, dikeringkan optimal, dan dicek kelengkapannya sebelum diserahkan kepada penyewa.</p>
                        @endif
                    </div>

                    <!-- Poin Keunggulan dengan SVG Icons -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6 pt-6 border-t border-stone-100">
                        <div class="flex items-start gap-3">
                            <span class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <div>
                                <h4 class="text-xs font-bold text-[#14201a]">Higienis & Bersih</h4>
                                <p class="text-[11px] text-stone-500 mt-0.5">Dicuci bersih dan disinfeksi setelah setiap trip.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-100">
                                <svg class="w-5 h-5 fill-amber-400 stroke-amber-500" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                            </span>
                            <div>
                                <h4 class="text-xs font-bold text-[#14201a]">Kondisi Siap Pakai</h4>
                                <p class="text-[11px] text-stone-500 mt-0.5">Frame, resleting, dan pasak dicek lengkap.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="w-9 h-9 rounded-xl bg-[#0e1b15]/10 text-[#0e1b15] flex items-center justify-center text-xs font-black shrink-0 border border-[#0e1b15]/15">
                                24H
                            </span>
                            <div>
                                <h4 class="text-xs font-bold text-[#14201a]">Buka 24 Jam</h4>
                                <p class="text-[11px] text-stone-500 mt-0.5">Ambil atau kembalikan kapan saja di toko.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Card Cek Ketersediaan & Estimasi Harga (5 Kolom) -->
            <div class="lg:col-span-5 space-y-6 lg:sticky lg:top-24">
                <div class="bg-white rounded-3xl border border-stone-200/90 shadow-lg p-6 sm:p-7 relative overflow-hidden">
                    <!-- Accent Green Glow -->
                    <div class="absolute -top-12 -right-12 w-40 h-40 bg-[#5edd5b]/20 rounded-full blur-2xl pointer-events-none"></div>

                    <!-- Header Harga -->
                    <div class="mb-6 pb-5 border-b border-stone-100">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-stone-400 block mb-1">Tarif Sewa Harian</span>
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl sm:text-4xl font-extrabold text-[#0e1b15]">
                                Rp {{ number_format($alat->harga_per_hari, 0, ',', '.') }}
                            </span>
                            <span class="text-xs font-semibold text-stone-500">/ 24 Jam</span>
                        </div>
                        <div class="flex items-center gap-3 mt-3 text-xs text-stone-500">
                            <span class="inline-flex items-center gap-1.5 font-medium">
                                <span class="w-2 h-2 rounded-full bg-[#5edd5b]"></span>
                                Total Stok Fisik: <strong class="text-[#14201a]">{{ $alat->stok_total }} unit</strong>
                            </span>
                            @if($alat->stok_perawatan > 0)
                                <span class="text-amber-600 font-semibold">({{ $alat->stok_perawatan }} servis)</span>
                            @endif
                        </div>
                    </div>

                    <!-- Form Pengecekan Ketersediaan -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-extrabold uppercase tracking-wider text-[#14201a] flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#0e1b15]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Cek Jadwal Ketersediaan
                            </h3>
                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">Real-Time Sync</span>
                        </div>

                        <!-- Input Tanggal Mulai & Selesai -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="tanggal_mulai" class="block text-[11px] font-bold text-stone-700 uppercase tracking-wider mb-1">
                                    Tgl Ambil
                                </label>
                                <input type="date" id="tanggal_mulai" min="{{ date('Y-m-d') }}" value="{{ $tMulaiDef }}"
                                    class="w-full text-xs font-semibold rounded-xl border-stone-300 focus:border-[#5edd5b] focus:ring-[#5edd5b] shadow-2xs">
                            </div>

                            <div>
                                <label for="tanggal_selesai" class="block text-[11px] font-bold text-stone-700 uppercase tracking-wider mb-1">
                                    Maks. Kembali
                                </label>
                                <input type="date" id="tanggal_selesai" min="{{ date('Y-m-d', strtotime('+1 day')) }}" value="{{ $tSelesaiDef }}"
                                    class="w-full text-xs font-semibold rounded-xl border-stone-300 focus:border-[#5edd5b] focus:ring-[#5edd5b] shadow-2xs">
                            </div>
                        </div>

                        <!-- Jumlah Unit -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label for="jumlah_unit" class="block text-[11px] font-bold text-stone-700 uppercase tracking-wider">
                                    Jumlah Unit
                                </label>
                                <span id="label-tersedia" class="text-[11px] font-bold text-stone-500">Maks: {{ $alat->stok_total }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <button type="button" onclick="ubahJumlah(-1)" class="w-10 h-10 rounded-xl bg-stone-100 hover:bg-stone-200 text-[#14201a] font-bold text-base flex items-center justify-center transition cursor-pointer">−</button>
                                <input type="number" id="jumlah_unit" min="1" max="{{ $alat->stok_total }}" value="1"
                                    class="w-full text-center text-sm font-bold rounded-xl border-stone-300 focus:border-[#5edd5b] focus:ring-[#5edd5b] shadow-2xs">
                                <button type="button" onclick="ubahJumlah(1)" class="w-10 h-10 rounded-xl bg-stone-100 hover:bg-stone-200 text-[#14201a] font-bold text-base flex items-center justify-center transition cursor-pointer">+</button>
                            </div>
                        </div>

                        <!-- Status Ketersediaan Live Box -->
                        <div id="status-box" class="p-4 rounded-2xl border transition-all duration-300 bg-stone-50 border-stone-200">
                            <div id="status-loading" class="hidden flex items-center gap-2 text-xs font-semibold text-stone-600">
                                <svg class="animate-spin h-4 w-4 text-[#0e1b15]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span>Menghubungi kalender toko...</span>
                            </div>

                            <div id="status-content">
                                <div class="flex items-center gap-2">
                                    <span id="status-dot" class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                                    <span id="status-judul" class="text-xs font-black text-[#14201a]">Siap Dicek</span>
                                </div>
                                <p id="status-pesan" class="text-xs text-stone-500 mt-1">
                                    Pilih rentang tanggal penyewaan untuk melihat sisa ketersediaan unit.
                                </p>
                            </div>
                        </div>

                        <!-- Rincian Estimasi Biaya Sewa -->
                        <div id="estimasi-box" class="pt-3 border-t border-stone-100 space-y-2">
                            <div class="flex items-center justify-between text-xs text-stone-500">
                                <span>Durasi Sewa:</span>
                                <span id="estimasi-durasi" class="font-bold text-[#14201a]">1 Hari (24 Jam)</span>
                            </div>
                            <div class="flex items-center justify-between text-xs text-stone-500">
                                <span>Subtotal (<span id="estimasi-unit-count">1</span> unit):</span>
                                <span id="estimasi-subtotal" class="font-bold text-[#14201a]">Rp {{ number_format($alat->harga_per_hari, 0, ',', '.') }}</span>
                            </div>
                            <div class="pt-2 border-t border-stone-100 flex items-center justify-between">
                                <span class="text-xs font-black uppercase tracking-wider text-[#14201a]">Total Estimasi:</span>
                                <span id="estimasi-total" class="text-xl font-extrabold text-[#0e1b15]">
                                    Rp {{ number_format($alat->harga_per_hari, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <!-- Tombol Aksi Sewa (Foresta Neon Lime) -->
                        <div class="pt-2">
                            @auth
                                <a id="btn-sewa" href="{{ route('pesanan.create', ['alat_id' => $alat->id]) }}"
                                    class="w-full py-3.5 px-4 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black font-extrabold text-xs uppercase tracking-wider transition shadow-md hover:shadow-lg flex items-center justify-center gap-2 cursor-pointer font-urbanist">
                                    <span>Lanjut Pesan Sekarang</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            @else
                                <a id="btn-sewa" href="{{ route('login') }}"
                                    class="w-full py-3.5 px-4 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black font-extrabold text-xs uppercase tracking-wider transition shadow-md hover:shadow-lg flex items-center justify-center gap-2 cursor-pointer font-urbanist">
                                    <span>Masuk untuk Menyewa</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            @endauth

                            <p class="text-[11px] text-center text-stone-400 mt-2.5 flex items-center justify-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-stone-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <span>Jaminan KTP/SIM asli diserahkan saat pengambilan alat di toko fisik.</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rekomendasi Perlengkapan Terkait -->
        @if($alatTerkait->count() > 0)
            <div class="mt-16 pt-10 border-t border-stone-200/80">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-extrabold text-[#14201a]">Perlengkapan Terkait</h3>
                        <p class="text-xs text-stone-500 mt-0.5">Alat lain dalam kategori {{ $alat->kategori?->nama }}</p>
                    </div>
                    <a href="{{ route('katalog.index', ['kategori' => $alat->kategori?->slug]) }}"
                        class="text-xs font-bold text-[#0e1b15] hover:text-[#5edd5b] inline-flex items-center gap-1 transition">
                        <span>Lihat Semua</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                    @foreach($alatTerkait as $rel)
                        @php
                            $relKategori = strtolower($rel->kategori?->nama ?? '');
                            $relDummy = 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?auto=format&fit=crop&w=600&q=80';
                            if (str_contains($relKategori, 'tenda')) {
                                $relDummy = 'https://images.unsplash.com/photo-1510312305653-8ed496efae75?auto=format&fit=crop&w=600&q=80';
                            } elseif (str_contains($relKategori, 'carrier') || str_contains($relKategori, 'tas')) {
                                $relDummy = 'https://images.unsplash.com/photo-1622560480605-d83c853bc5c3?auto=format&fit=crop&w=600&q=80';
                            } elseif (str_contains($relKategori, 'tidur') || str_contains($relKategori, 'matras')) {
                                $relDummy = 'https://images.unsplash.com/photo-1520095972714-909e91b038e5?auto=format&fit=crop&w=600&q=80';
                            } elseif (str_contains($relKategori, 'masak')) {
                                $relDummy = 'https://images.unsplash.com/photo-1470246973918-29a93221c455?auto=format&fit=crop&w=600&q=80';
                            }
                        @endphp
                        <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs hover:shadow-md transition p-3 sm:p-4 flex flex-col justify-between group">
                            <div>
                                <div class="aspect-square bg-stone-50 rounded-xl overflow-hidden mb-3 relative flex items-center justify-center p-2">
                                    @if($rel->foto)
                                        <img src="{{ asset('storage/' . $rel->foto) }}" alt="{{ $rel->nama }}" class="w-full h-full object-contain group-hover:scale-105 transition duration-300">
                                    @else
                                        <img src="{{ $relDummy }}" alt="{{ $rel->nama }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300 rounded-lg">
                                    @endif
                                </div>
                                <h4 class="text-xs sm:text-sm font-bold text-stone-900 text-center line-clamp-2 min-h-[36px]">
                                    <a href="{{ route('katalog.show', $rel->id) }}" class="hover:text-emerald-700">{{ $rel->nama }}</a>
                                </h4>
                                <span class="text-xs sm:text-sm font-extrabold text-[#0e1b15] block text-center mt-1">
                                    Rp {{ number_format($rel->harga_per_hari, 0, ',', '.') }} <span class="text-[11px] font-normal text-stone-500">/hari</span>
                                </span>
                            </div>
                            <div class="mt-3 pt-2 border-t border-stone-100">
                                <a href="{{ route('katalog.show', $rel->id) }}"
                                    class="w-full py-2 rounded-xl bg-stone-100 hover:bg-[#5edd5b] hover:text-black text-[#14201a] text-xs font-bold transition flex items-center justify-center">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </section>

    <!-- Script Live Availability & Calculator -->
    <script>
        const HARGA_PER_HARI = {{ (float) $alat->harga_per_hari }};
        const ALAT_ID = {{ $alat->id }};
        let stokTersediaSekarang = {{ max(0, $alat->stok_total - $alat->stok_perawatan) }};

        const inputMulai = document.getElementById('tanggal_mulai');
        const inputSelesai = document.getElementById('tanggal_selesai');
        const inputJumlah = document.getElementById('jumlah_unit');
        const statusBox = document.getElementById('status-box');
        const statusDot = document.getElementById('status-dot');
        const statusJudul = document.getElementById('status-judul');
        const statusPesan = document.getElementById('status-pesan');
        const statusLoading = document.getElementById('status-loading');
        const statusContent = document.getElementById('status-content');
        const labelTersedia = document.getElementById('label-tersedia');
        const btnSewa = document.getElementById('btn-sewa');

        const estimasiDurasi = document.getElementById('estimasi-durasi');
        const estimasiUnitCount = document.getElementById('estimasi-unit-count');
        const estimasiSubtotal = document.getElementById('estimasi-subtotal');
        const estimasiTotal = document.getElementById('estimasi-total');

        function formatRupiah(num) {
            return 'Rp ' + Number(num).toLocaleString('id-ID');
        }

        function ubahJumlah(delta) {
            let val = parseInt(inputJumlah.value) || 1;
            val += delta;
            if (val < 1) val = 1;
            if (stokTersediaSekarang > 0 && val > stokTersediaSekarang) val = stokTersediaSekarang;
            inputJumlah.value = val;
            kalkulasiEstimasi();
        }

        async function cekKetersediaan() {
            const mulai = inputMulai.value;
            const selesai = inputSelesai.value;

            if (!mulai || !selesai) return;

            // Pastikan tanggal selesai > tanggal mulai
            if (new Date(selesai) <= new Date(mulai)) {
                statusBox.className = 'p-4 rounded-2xl border transition-all duration-300 bg-rose-50 border-rose-200';
                statusDot.className = 'w-2.5 h-2.5 rounded-full bg-rose-600 shrink-0';
                statusJudul.className = 'text-xs font-black text-rose-900 bl-d';
                statusJudul.textContent = 'Tanggal Tidak Valid';
                statusPesan.textContent = 'Tanggal selesai sewa harus minimal 1 hari setelah tanggal mulai sewa.';
                if (btnSewa) btnSewa.classList.add('opacity-50', 'pointer-events-none');
                return;
            }

            statusLoading.classList.remove('hidden');
            statusContent.classList.add('hidden');

            try {
                const response = await fetch(`/katalog/${ALAT_ID}/ketersediaan?mulai=${mulai}&selesai=${selesai}`);
                const data = await response.json();

                statusLoading.classList.add('hidden');
                statusContent.classList.remove('hidden');

                if (data.sukses) {
                    stokTersediaSekarang = data.tersedia;
                    labelTersedia.textContent = `Tersedia: ${data.tersedia} unit`;

                    if (data.tersedia <= 0) {
                        statusBox.className = 'p-4 rounded-2xl border transition-all duration-300 bg-rose-50 border-rose-200';
                        statusDot.className = 'w-2.5 h-2.5 rounded-full bg-rose-600 shrink-0';
                        statusJudul.className = 'text-xs font-black text-rose-900 bl-d';
                        statusJudul.textContent = 'Stok Habis pada Tanggal Ini';
                        statusPesan.textContent = 'Seluruh unit alat sudah terpesan oleh penyewa lain pada rentang tanggal tersebut. Silakan pilih tanggal lain.';
                        if (btnSewa) btnSewa.classList.add('opacity-50', 'pointer-events-none');
                    } else {
                        statusBox.className = 'p-4 rounded-2xl border transition-all duration-300 bg-emerald-50 border-emerald-200';
                        statusDot.className = 'w-2.5 h-2.5 rounded-full bg-emerald-600 shrink-0';
                        statusJudul.className = 'text-xs font-black text-emerald-900 bl-d';
                        statusJudul.textContent = `Tersedia ${data.tersedia} Unit`;
                        statusPesan.textContent = `Alat siap disewa dari ${data.tanggal_mulai} s.d. ${data.tanggal_selesai} (${data.durasi_hari} hari).`;
                        if (btnSewa) btnSewa.classList.remove('opacity-50', 'pointer-events-none');

                        // Batasi max input unit jika melebihi stok yang tersedia
                        if (parseInt(inputJumlah.value) > data.tersedia) {
                            inputJumlah.value = data.tersedia;
                        }
                    }
                } else {
                    statusBox.className = 'p-4 rounded-2xl border transition-all duration-300 bg-amber-50 border-amber-200';
                    statusDot.className = 'w-2.5 h-2.5 rounded-full bg-amber-600 shrink-0';
                    statusJudul.className = 'text-xs font-black text-amber-900 bl-d';
                    statusJudul.textContent = 'Perhatian';
                    statusPesan.textContent = data.pesan || 'Tidak dapat memverifikasi jadwal.';
                    if (btnSewa) btnSewa.classList.add('opacity-50', 'pointer-events-none');
                }
            } catch (err) {
                statusLoading.classList.add('hidden');
                statusContent.classList.remove('hidden');
                console.error(err);
            }

            kalkulasiEstimasi();
        }

        function kalkulasiEstimasi() {
            const mulai = new Date(inputMulai.value);
            const selesai = new Date(inputSelesai.value);
            const unit = parseInt(inputJumlah.value) || 1;

            if (selesai > mulai) {
                const diffTime = Math.abs(selesai - mulai);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                const total = HARGA_PER_HARI * diffDays * unit;

                estimasiDurasi.textContent = `${diffDays} Hari (${diffDays * 24} Jam)`;
                estimasiUnitCount.textContent = unit;
                estimasiSubtotal.textContent = formatRupiah(total);
                estimasiTotal.textContent = formatRupiah(total);

                if (btnSewa && btnSewa.getAttribute('href').includes('pesanan/buat')) {
                    btnSewa.href = `{{ route('pesanan.create') }}?alat_id={{ $alat->id }}&jumlah=${unit}&mulai=${inputMulai.value}&selesai=${inputSelesai.value}`;
                }
            }
        }

        // Event Listeners
        inputMulai.addEventListener('change', () => {
            // Update min tanggal selesai agar tidak kurang dari tanggal mulai + 1 hari
            const mulaiVal = new Date(inputMulai.value);
            if (!isNaN(mulaiVal.getTime())) {
                const minSelesai = new Date(mulaiVal);
                minSelesai.setDate(minSelesai.getDate() + 1);
                inputSelesai.min = minSelesai.toISOString().split('T')[0];
                if (new Date(inputSelesai.value) <= mulaiVal) {
                    inputSelesai.value = minSelesai.toISOString().split('T')[0];
                }
            }
            cekKetersediaan();
        });

        inputSelesai.addEventListener('change', cekKetersediaan);
        inputJumlah.addEventListener('input', kalkulasiEstimasi);

        // Jalankan saat load halaman
        document.addEventListener('DOMContentLoaded', cekKetersediaan);
    </script>
</x-layouts.publik>
