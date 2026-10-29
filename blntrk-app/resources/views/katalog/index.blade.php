<x-layouts.publik title="Katalog Perlengkapan Outdoor - BLNTRK Outdoor Subang">
    <!-- Header Hero Katalog (Foresta Adventure Style) -->
    <section class="bg-gradient-to-b from-[#0e1b15] to-[#14251e] text-white pt-12 pb-20 border-b border-[#1e3a2c]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-black/40 text-[#5edd5b] text-xs font-bold uppercase tracking-wider mb-4 border border-[#5edd5b]/30 backdrop-blur-md">
                <span class="w-2 h-2 rounded-full bg-[#5edd5b] animate-pulse"></span>
                Katalog Siap Sewa Real-Time
            </span>
            <h1 class="text-3xl sm:text-5xl md:text-6xl font-black tracking-tight font-urbanist text-white">
                Pilih Perlengkapan <span class="text-[#5edd5b]">Petualanganmu</span>
            </h1>
            <p class="text-sm sm:text-base text-stone-300 max-w-2xl mx-auto mt-3 font-normal leading-relaxed">
                Tenda dome, carrier, sleeping bag, kompor portable, dan peralatan camping lengkap di Subang. Cek ketersediaan unit dan pilih tanggal sewa langsung dari HP.
            </p>
        </div>
    </section>

    <!-- Floating Schedule & Search Filter Card (Foresta Style) -->
    <div class="max-w-6xl mx-auto -mt-10 sm:-mt-12 px-4 relative z-30 mb-10">
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-stone-200/90 shadow-xl p-4 sm:p-6">
            <form method="GET" action="{{ route('katalog.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                @if(request('kategori'))
                    <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                @endif
                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif

                <!-- Kolom 1: Tgl Ambil -->
                <div class="md:col-span-3 border-b md:border-b-0 md:border-r border-stone-200 pb-3 md:pb-0 md:pr-4">
                    <label for="kat-mulai" class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-[#1e3a2c]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Tgl Ambil
                    </label>
                    <input id="kat-mulai" type="date" name="mulai" min="{{ now()->format('Y-m-d') }}"
                           value="{{ $tanggalMulai ? $tanggalMulai->format('Y-m-d') : request('mulai', now()->format('Y-m-d')) }}"
                           class="w-full text-sm font-bold text-stone-900 border-0 p-0 focus:ring-0 focus:outline-none cursor-pointer bg-transparent">
                </div>

                <!-- Kolom 2: Durasi Dropdown -->
                <div class="md:col-span-2 border-b md:border-b-0 md:border-r border-stone-200 pb-3 md:pb-0 md:pr-4">
                    <label for="kat-durasi" class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-[#1e3a2c]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Durasi
                    </label>
                    <select id="kat-durasi" name="durasi" class="w-full text-sm font-bold text-stone-900 border-0 p-0 focus:ring-0 focus:outline-none cursor-pointer bg-transparent">
                        <option value="1" {{ ($durasiHari ?? request('durasi', 1)) == 1 ? 'selected' : '' }}>1 Hari (24 Jam)</option>
                        <option value="2" {{ ($durasiHari ?? request('durasi')) == 2 ? 'selected' : '' }}>2 Hari</option>
                        <option value="3" {{ ($durasiHari ?? request('durasi')) == 3 ? 'selected' : '' }}>3 Hari</option>
                        <option value="4" {{ ($durasiHari ?? request('durasi')) == 4 ? 'selected' : '' }}>4 Hari</option>
                        <option value="5" {{ ($durasiHari ?? request('durasi')) == 5 ? 'selected' : '' }}>5 Hari</option>
                        <option value="7" {{ ($durasiHari ?? request('durasi')) == 7 ? 'selected' : '' }}>7 Hari (1 Minggu)</option>
                        <option value="10" {{ ($durasiHari ?? request('durasi')) == 10 ? 'selected' : '' }}>10 Hari</option>
                    </select>
                </div>

                <!-- Kolom 3: Maks. Pengembalian -->
                <div class="md:col-span-3 border-b md:border-b-0 md:border-r border-stone-200 pb-3 md:pb-0 md:pr-4">
                    <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-[#1e3a2c]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        Maks. Pengembalian
                    </label>
                    <input type="hidden" id="kat-selesai" name="selesai" value="{{ $tanggalSelesai ? $tanggalSelesai->format('Y-m-d') : request('selesai', now()->addDay()->format('Y-m-d')) }}">
                    <div id="kat-kembali-text" class="text-sm font-bold text-stone-800 truncate">
                        {{ $tanggalSelesai ? $tanggalSelesai->translatedFormat('D, d M Y') : now()->addDay()->translatedFormat('D, d M Y') }}, 12:00
                    </div>
                </div>

                <!-- Kolom 4: Cari Kata Kunci -->
                <div class="md:col-span-2 border-b md:border-b-0 pb-3 md:pb-0">
                    <label for="kat-cari" class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-[#1e3a2c]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Cari
                    </label>
                    <input id="kat-cari" type="text" name="q" value="{{ $keyword }}" placeholder="Tenda, carrier..."
                           class="w-full text-sm font-bold text-stone-900 placeholder:text-stone-400 placeholder:font-normal border-0 p-0 focus:ring-0 focus:outline-none bg-transparent">
                </div>

                <!-- CTA Button Cari (Foresta Neon Lime) -->
                <div class="md:col-span-2 flex justify-end">
                    <button type="submit" class="w-full md:w-auto bg-[#5edd5b] hover:bg-[#4ed04b] text-black font-urbanist font-black text-sm px-6 py-3.5 rounded-xl shadow-md transition flex items-center justify-center gap-2 hover:scale-[1.02]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span>Cari</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Konten Utama Katalog -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <!-- Banner Filter Tanggal Aktif -->
        @if($tanggalMulai && $tanggalSelesai)
            <div class="mb-6 p-4 rounded-2xl bg-white border border-stone-200/90 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-xs">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#0e1b15] text-[#5edd5b] flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">Jadwal Sewa Aktif</span>
                        <div class="text-xs sm:text-sm font-bold text-stone-900 inline-flex items-center gap-1.5">
                            <span>{{ $tanggalMulai->translatedFormat('d M Y') }}</span>
                            <svg class="w-3.5 h-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            <span>{{ $tanggalSelesai->translatedFormat('d M Y') }}</span>
                            <span class="text-stone-500 font-medium">({{ $durasiHari }} Hari)</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-[11px] text-stone-500">Ketersediaan disaring real-time</span>
                    <a href="{{ route('katalog.index', array_filter(['kategori' => $kategoriAktif, 'q' => $keyword, 'sort' => $sortAktif])) }}"
                       class="px-3 py-1.5 rounded-lg bg-stone-100 hover:bg-stone-200 text-xs font-bold text-stone-700 transition inline-flex items-center gap-1">
                        <span>Reset Jadwal</span>
                        <svg class="w-3.5 h-3.5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                </div>
            </div>
        @endif

        <!-- Kategori Pills Filter (Foresta Adventure Style) -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black uppercase tracking-wider text-stone-800 font-urbanist">Kategori Perlengkapan</span>
                @if($kategoriAktif || $keyword || $tanggalMulai)
                    <a href="{{ route('katalog.index') }}" class="text-xs font-bold text-rose-600 hover:text-rose-800 transition inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>Reset Seluruh Filter</span>
                    </a>
                @endif
            </div>

            <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
                <a href="{{ route('katalog.index', array_filter(['q' => $keyword, 'sort' => $sortAktif, 'mulai' => $tanggalMulai?->format('Y-m-d'), 'selesai' => $tanggalSelesai?->format('Y-m-d')])) }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold shrink-0 transition {{ empty($kategoriAktif) ? 'bg-[#5edd5b] text-black shadow-xs font-extrabold' : 'bg-white text-stone-700 border border-stone-200/90 hover:bg-stone-50' }}">
                    Semua Perlengkapan
                </a>
                @foreach($kategoriList as $kat)
                    <a href="{{ route('katalog.index', array_filter(['kategori' => $kat->slug, 'q' => $keyword, 'sort' => $sortAktif, 'mulai' => $tanggalMulai?->format('Y-m-d'), 'selesai' => $tanggalSelesai?->format('Y-m-d')])) }}"
                       class="px-4 py-2 rounded-xl text-xs font-bold shrink-0 transition flex items-center gap-2 {{ $kategoriAktif === $kat->slug || $kategoriAktif == $kat->id ? 'bg-[#5edd5b] text-black shadow-xs font-extrabold' : 'bg-white text-stone-700 border border-stone-200/90 hover:bg-stone-50' }}">
                        <span>{{ $kat->nama }}</span>
                        <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $kategoriAktif === $kat->slug || $kategoriAktif == $kat->id ? 'bg-black text-[#5edd5b]' : 'bg-stone-100 text-stone-500' }}">
                            {{ $kat->alat_count }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Filter Bar & Sorting -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 pb-4 border-b border-stone-200/80">
            <div>
                <p class="text-xs text-stone-500">
                    Menampilkan <span class="font-bold text-stone-900">{{ $alatList->firstItem() ?? 0 }}-{{ $alatList->lastItem() ?? 0 }}</span> dari total <span class="font-bold text-stone-900">{{ $alatList->total() }}</span> alat outdoor
                    @if($keyword)
                        untuk kata kunci "<span class="font-bold text-stone-900">{{ $keyword }}</span>"
                    @endif
                </p>
            </div>

            <!-- Dropdown Sorting -->
            <form method="GET" action="{{ route('katalog.index') }}" class="flex items-center gap-2">
                @if($kategoriAktif)
                    <input type="hidden" name="kategori" value="{{ $kategoriAktif }}">
                @endif
                @if($keyword)
                    <input type="hidden" name="q" value="{{ $keyword }}">
                @endif
                @if($tanggalMulai && $tanggalSelesai)
                    <input type="hidden" name="mulai" value="{{ $tanggalMulai->format('Y-m-d') }}">
                    <input type="hidden" name="selesai" value="{{ $tanggalSelesai->format('Y-m-d') }}">
                @endif
                <label for="sort" class="text-xs font-bold text-stone-500 uppercase tracking-wider shrink-0">Urutkan:</label>
                <select id="sort" name="sort" onchange="this.form.submit()"
                        class="text-xs font-semibold rounded-xl border-stone-200 bg-white py-1.5 pl-3 pr-8 focus:border-[#5edd5b] focus:ring-[#5edd5b] shadow-2xs">
                    <option value="terbaru" {{ $sortAktif === 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                    <option value="termurah" {{ $sortAktif === 'termurah' ? 'selected' : '' }}>Harga: Termurah</option>
                    <option value="termahal" {{ $sortAktif === 'termahal' ? 'selected' : '' }}>Harga: Termahal</option>
                    <option value="nama_asc" {{ $sortAktif === 'nama_asc' ? 'selected' : '' }}>Nama Alat (A-Z)</option>
                </select>
            </form>
        </div>

        <!-- Grid Produk Katalog (Rasio 1:1 Khas Foresta) -->
        @php
            $catPhotos = [
                'Tenda' => 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?auto=format&fit=crop&w=600&q=80',
                'Carrier & Tas' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=600&q=80',
                'Sleeping Gear' => 'https://images.unsplash.com/photo-1520095972714-909e91b038e5?auto=format&fit=crop&w=600&q=80',
                'Alas Tidur' => 'https://images.unsplash.com/photo-1478131143081-80f7f84ca84d?auto=format&fit=crop&w=600&q=80',
                'Masak & Makan' => 'https://images.unsplash.com/photo-1526772662000-3f88f10405ff?auto=format&fit=crop&w=600&q=80',
                'Penerangan' => 'https://images.unsplash.com/photo-1517411032315-54ef2cb783bb?auto=format&fit=crop&w=600&q=80',
                'Sepatu & Trekking' => 'https://images.unsplash.com/photo-1551632811-561732d1e306?auto=format&fit=crop&w=600&q=80',
            ];
        @endphp

        @if($alatList->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                @foreach($alatList as $alat)
                    @php
                        $stokSiap = max(0, $alat->stok_total - $alat->stok_perawatan);
                        $targetUrl = route('katalog.show', array_filter([
                            'alat' => $alat->id,
                            'mulai' => $tanggalMulai?->format('Y-m-d'),
                            'selesai' => $tanggalSelesai?->format('Y-m-d')
                        ]));
                        $imgSrc = $alat->foto 
                            ? asset('storage/' . $alat->foto) 
                            : ($catPhotos[$alat->kategori?->nama ?? ''] ?? 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?auto=format&fit=crop&w=600&q=80');
                    @endphp
                    <div class="bg-white rounded-2xl border border-stone-200/80 p-4 sm:p-5 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                        <!-- Gambar Alat Rasio 1:1 -->
                        <a href="{{ $targetUrl }}" class="block relative aspect-square bg-stone-50 rounded-xl overflow-hidden p-2 flex items-center justify-center mb-4">
                            <img src="{{ $imgSrc }}" alt="{{ $alat->nama }}" loading="lazy"
                                 class="object-cover w-full h-full rounded-lg group-hover:scale-105 transition-transform duration-300">

                            <!-- Badge Kategori -->
                            <div class="absolute top-2.5 left-2.5">
                                <span class="px-2 py-0.5 rounded-full bg-[#0e1b15]/85 backdrop-blur-xs text-white text-[9px] font-bold uppercase tracking-wider">
                                    {{ $alat->kategori?->nama ?? 'Outdoor' }}
                                </span>
                            </div>

                            <!-- Badge Stok Indikator (Foresta Style) -->
                            <div class="absolute top-2.5 right-2.5">
                                @if(isset($alat->stok_tersedia_rentang))
                                    @if($alat->stok_tersedia_rentang > 0)
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-[#5edd5b] text-black shadow-xs">
                                            Tersedia {{ $alat->stok_tersedia_rentang }}
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-600 text-white shadow-xs">
                                            Habis
                                        </span>
                                    @endif
                                @else
                                    @if($stokSiap > 0)
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-[#5edd5b] text-black shadow-xs">
                                            Tersedia {{ $stokSiap }}
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-600 text-white shadow-xs">
                                            Habis
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </a>

                        <!-- Info Alat (Foresta Center Style) -->
                        <div class="text-center flex-grow flex flex-col justify-between">
                            <div>
                                <h2 class="text-xs sm:text-sm font-bold text-stone-900 group-hover:text-[#1e3a2c] transition line-clamp-2 min-h-[36px] flex items-center justify-center">
                                    <a href="{{ $targetUrl }}" title="{{ $alat->nama }}">
                                        {{ $alat->nama }}{{ $alat->varian ? ' - ' . $alat->varian : '' }}
                                    </a>
                                </h2>
                            </div>

                            <div class="mt-2.5">
                                <span class="font-urbanist font-black text-sm sm:text-base text-stone-900 block">
                                    Rp {{ number_format($alat->harga_per_hari, 0, ',', '.') }}
                                    <span class="text-[11px] text-stone-500 font-normal">/hari</span>
                                </span>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <a href="{{ $targetUrl }}" class="mt-3.5 block w-full py-2.5 rounded-xl bg-stone-100 hover:bg-[#5edd5b] text-stone-800 hover:text-black text-xs font-bold text-center transition">
                            Pilih &amp; Cek Jadwal
                        </a>
                    </div>
                @endforeach
            </div>

            <!-- Paginasi -->
            <div class="mt-10">
                {{ $alatList->links() }}
            </div>
        @else
            <!-- State Kosong (SVG, No Emoji) -->
            <div class="bg-white rounded-2xl border border-stone-200/90 p-12 text-center max-w-md mx-auto shadow-xs">
                <div class="w-16 h-16 rounded-2xl bg-[#5edd5b]/20 text-[#1e3a2c] flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <h3 class="text-lg font-black text-stone-900 font-urbanist">Tidak Ada Alat Ditemukan</h3>
                <p class="text-xs text-stone-500 mt-1 leading-relaxed">
                    Kami tidak menemukan perlengkapan outdoor dengan kata kunci atau filter yang Anda pilih. Coba gunakan istilah pencarian lain.
                </p>
                <div class="mt-6">
                    <a href="{{ route('katalog.index') }}" class="px-5 py-2.5 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-black shadow-xs transition inline-block">
                        Lihat Seluruh Katalog
                    </a>
                </div>
            </div>
        @endif
    </section>

    <!-- Script Sync Return Date -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mulaiInput = document.getElementById('kat-mulai');
            const durasiSelect = document.getElementById('kat-durasi');
            const selesaiHidden = document.getElementById('kat-selesai');
            const kembaliText = document.getElementById('kat-kembali-text');

            function updateReturnDate() {
                if (!mulaiInput.value) return;

                const days = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

                const startDate = new Date(mulaiInput.value + 'T00:00:00');
                const duration = parseInt(durasiSelect.value, 10) || 1;

                const endDate = new Date(startDate);
                endDate.setDate(startDate.getDate() + duration);

                const year = endDate.getFullYear();
                const month = String(endDate.getMonth() + 1).padStart(2, '0');
                const day = String(endDate.getDate()).padStart(2, '0');

                selesaiHidden.value = `${year}-${month}-${day}`;

                const dayName = days[endDate.getDay()];
                const monthName = months[endDate.getMonth()];
                kembaliText.textContent = `${dayName}, ${day} ${monthName} ${year}, 12:00`;
            }

            if (mulaiInput && durasiSelect) {
                mulaiInput.addEventListener('change', updateReturnDate);
                durasiSelect.addEventListener('change', updateReturnDate);
                updateReturnDate();
            }
        });
    </script>
</x-layouts.publik>
