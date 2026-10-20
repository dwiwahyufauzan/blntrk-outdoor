<x-layouts.publik title="Katalog Perlengkapan Outdoor - BLNTRK Outdoor Subang">
    <!-- Header Hero Katalog -->
    <section class="bg-gradient-to-b from-[#0e1b15] to-[#14201a] text-white pt-12 pb-16 border-b border-[#1e3a2c]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#1e3a2c] text-[#f6c453] text-[11px] font-bold uppercase tracking-wider mb-4 border border-[#f6c453]/20">
                <span class="w-2 h-2 rounded-full bg-[#f6c453] animate-pulse"></span>
                Katalog Peralatan Siap Sewa
            </span>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black tracking-tight bl-d text-white">
                Pilih Perlengkapan <span class="text-[#f6c453]">Petualanganmu</span>
            </h1>
            <p class="text-sm sm:text-base text-stone-300 max-w-2xl mx-auto mt-3 font-normal">
                Koleksi lengkap tenda dome, carrier, nesting, sleeping bag, dan matras terawat. Cek ketersediaan tanggal sebelum mendaki gunung!
            </p>

            <!-- Search Bar Floating -->
            <div class="mt-8 max-w-2xl mx-auto">
                <form method="GET" action="{{ route('katalog.index') }}" class="flex flex-col sm:flex-row gap-2 bg-white/10 p-2 rounded-2xl backdrop-blur-md border border-white/15 shadow-xl">
                    @if(request('kategori'))
                        <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                    @endif
                    @if($tanggalMulai && $tanggalSelesai)
                        <input type="hidden" name="mulai" value="{{ $tanggalMulai->format('Y-m-d') }}">
                        <input type="hidden" name="selesai" value="{{ $tanggalSelesai->format('Y-m-d') }}">
                    @endif
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" name="q" value="{{ $keyword }}" placeholder="Cari tenda 4P, carrier 60L, kompor..."
                            class="w-full pl-11 pr-4 py-3 bg-white text-[#14201a] rounded-xl text-xs sm:text-sm placeholder-stone-400 focus:outline-none focus:ring-2 focus:ring-[#f6c453] font-medium shadow-inner">
                    </div>
                    <button type="submit" class="px-6 py-3 bg-[#f6c453] hover:bg-[#ffd36e] text-[#0e1b15] font-black text-xs sm:text-sm rounded-xl transition shadow-md flex items-center justify-center gap-2 cursor-pointer bl-d">
                        <span>Cari Alat</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Konten Utama Katalog -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <!-- Banner Filter Tanggal Aktif -->
        @if($tanggalMulai && $tanggalSelesai)
            <div class="mb-6 p-4 rounded-2xl bg-gradient-to-r from-[#0e1b15] to-[#1e3a2c] text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-xs border border-[#1e3a2c]">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-[#f6c453] text-[#0e1b15] flex items-center justify-center font-bold text-lg shrink-0">
                        📅
                    </span>
                    <div>
                        <span class="text-[10px] font-bold text-[#f6c453] uppercase tracking-wider block">Jadwal Sewa Terpilih</span>
                        <span class="text-xs sm:text-sm font-bold text-white">
                            {{ $tanggalMulai->format('d M Y') }} &rarr; {{ $tanggalSelesai->format('d M Y') }} ({{ $durasiHari }} Hari)
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-[11px] text-stone-300">Menampilkan ketersediaan riil tanggal ini</span>
                    <a href="{{ route('katalog.index', array_filter(['kategori' => $kategoriAktif, 'q' => $keyword, 'sort' => $sortAktif])) }}"
                        class="px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-xs font-bold text-stone-200 transition">
                        Reset Tanggal &times;
                    </a>
                </div>
            </div>
        @endif

        <!-- Kategori Pills Filter -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black uppercase tracking-wider text-[#14201a] bl-d">Kategori Perlengkapan</span>
                @if($kategoriAktif || $keyword || $tanggalMulai)
                    <a href="{{ route('katalog.index') }}" class="text-xs font-bold text-rose-600 hover:text-rose-800 transition inline-flex items-center gap-1">
                        <span>&times; Reset Seluruh Filter</span>
                    </a>
                @endif
            </div>

            <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
                <a href="{{ route('katalog.index', array_filter(['q' => $keyword, 'sort' => $sortAktif, 'mulai' => $tanggalMulai?->format('Y-m-d'), 'selesai' => $tanggalSelesai?->format('Y-m-d')])) }}"
                    class="px-4 py-2 rounded-xl text-xs font-bold shrink-0 transition {{ empty($kategoriAktif) ? 'bg-[#0e1b15] text-[#f6c453] shadow-xs' : 'bg-white text-stone-700 border border-stone-200/90 hover:bg-stone-50' }}">
                    Semua Perlengkapan
                </a>
                @foreach($kategoriList as $kat)
                    <a href="{{ route('katalog.index', array_filter(['kategori' => $kat->slug, 'q' => $keyword, 'sort' => $sortAktif, 'mulai' => $tanggalMulai?->format('Y-m-d'), 'selesai' => $tanggalSelesai?->format('Y-m-d')])) }}"
                        class="px-4 py-2 rounded-xl text-xs font-bold shrink-0 transition flex items-center gap-2 {{ $kategoriAktif === $kat->slug || $kategoriAktif == $kat->id ? 'bg-[#0e1b15] text-[#f6c453] shadow-xs' : 'bg-white text-stone-700 border border-stone-200/90 hover:bg-stone-50' }}">
                        <span>{{ $kat->nama }}</span>
                        <span class="text-[10px] px-1.5 py-0.2 rounded-full {{ $kategoriAktif === $kat->slug || $kategoriAktif == $kat->id ? 'bg-[#1e3a2c] text-[#f6c453]' : 'bg-stone-100 text-stone-500' }}">
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
                    Menampilkan <span class="font-bold text-[#14201a]">{{ $alatList->firstItem() ?? 0 }}-{{ $alatList->lastItem() ?? 0 }}</span> dari total <span class="font-bold text-[#14201a]">{{ $alatList->total() }}</span> alat outdoor
                    @if($keyword)
                        untuk kata kunci "<span class="font-bold text-[#14201a]">{{ $keyword }}</span>"
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
                    class="text-xs font-semibold rounded-xl border-stone-200 bg-white py-1.5 pl-3 pr-8 focus:border-[#1e3a2c] focus:ring-[#1e3a2c] shadow-2xs">
                    <option value="terbaru" {{ $sortAktif === 'terbaru' ? 'selected' : '' }}>Terbaru Ditambahkan</option>
                    <option value="termurah" {{ $sortAktif === 'termurah' ? 'selected' : '' }}>Harga: Termurah</option>
                    <option value="termahal" {{ $sortAktif === 'termahal' ? 'selected' : '' }}>Harga: Termahal</option>
                    <option value="nama_asc" {{ $sortAktif === 'nama_asc' ? 'selected' : '' }}>Nama Alat (A-Z)</option>
                </select>
            </form>
        </div>

        <!-- Grid Produk Katalog -->
        @if($alatList->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($alatList as $alat)
                    @php
                        $stokSiap = max(0, $alat->stok_total - $alat->stok_perawatan);
                        $targetUrl = route('katalog.show', array_filter([
                            'alat' => $alat->id,
                            'mulai' => $tanggalMulai?->format('Y-m-d'),
                            'selesai' => $tanggalSelesai?->format('Y-m-d')
                        ]));
                    @endphp
                    <div class="group bg-white rounded-2xl border border-stone-200/90 shadow-xs hover:shadow-md transition-all duration-300 flex flex-col overflow-hidden hover:-translate-y-1">
                        <!-- Gambar Alat -->
                        <div class="relative aspect-4/3 bg-stone-100 overflow-hidden">
                            @if($alat->foto)
                                <img src="{{ asset('storage/' . $alat->foto) }}" alt="{{ $alat->nama }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center text-stone-300 bg-gradient-to-br from-stone-50 to-stone-100">
                                    <span class="text-4xl">⛺</span>
                                    <span class="text-[10px] font-semibold text-stone-400 mt-1 uppercase tracking-wider">BLNTRK Outdoor</span>
                                </div>
                            @endif

                            <!-- Badge Kategori -->
                            <div class="absolute top-3 left-3">
                                <span class="px-2.5 py-1 rounded-full bg-[#0e1b15]/80 backdrop-blur-xs text-[#f6c453] text-[10px] font-bold uppercase tracking-wider">
                                    {{ $alat->kategori?->nama ?? 'Outdoor' }}
                                </span>
                            </div>

                            <!-- Badge Stok Indikator (Riil Tanggal vs Total) -->
                            <div class="absolute top-3 right-3">
                                @if(isset($alat->stok_tersedia_rentang))
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold shadow-xs backdrop-blur-xs {{ $alat->stok_tersedia_rentang > 0 ? 'bg-emerald-600/95 text-white' : 'bg-rose-600/95 text-white' }}">
                                        {{ $alat->stok_tersedia_rentang > 0 ? "Tersedia: {$alat->stok_tersedia_rentang} unit" : 'Habis di Tanggal Ini' }}
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold shadow-xs backdrop-blur-xs {{ $stokSiap > 0 ? 'bg-emerald-600/90 text-white' : 'bg-rose-600/90 text-white' }}">
                                        {{ $stokSiap > 0 ? "Stok Toko: {$stokSiap}" : 'Sedang Habis' }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Info Alat -->
                        <div class="p-5 flex-1 flex flex-col justify-between">
                            <div>
                                <div class="text-[11px] font-mono font-bold text-stone-400 uppercase tracking-wider mb-1">
                                    {{ $alat->kode_alat }}
                                </div>
                                <h2 class="text-base font-black text-[#14201a] group-hover:text-[#1e3a2c] transition line-clamp-1 bl-d">
                                    <a href="{{ $targetUrl }}">
                                        {{ $alat->nama }}
                                    </a>
                                </h2>
                                @if($alat->varian)
                                    <p class="text-xs text-[#4e7a5a] font-semibold mt-0.5">{{ $alat->varian }}</p>
                                @endif
                                <p class="text-xs text-stone-500 mt-2 line-clamp-2 leading-relaxed">
                                    {{ $alat->deskripsi ?? 'Perlengkapan outdoor berkualitas prima, bersih, kering, dan siap pakai untuk petualangan Anda.' }}
                                </p>
                            </div>

                            <div class="pt-4 mt-4 border-t border-stone-100 flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] font-semibold text-stone-400 block uppercase tracking-wider">Tarif Sewa</span>
                                    <div class="text-base font-black text-[#0e1b15] bl-d">
                                        Rp {{ number_format($alat->harga_per_hari, 0, ',', '.') }}
                                        <span class="text-[10px] font-normal text-stone-500">/ hari</span>
                                    </div>
                                </div>

                                <a href="{{ $targetUrl }}"
                                    class="px-3.5 py-2 rounded-xl bg-[#0e1b15] hover:bg-[#1e3a2c] text-[#f6c453] text-xs font-bold transition shadow-xs flex items-center gap-1.5 cursor-pointer">
                                    <span>Cek Jadwal</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Paginasi -->
            <div class="mt-10">
                {{ $alatList->links() }}
            </div>
        @else
            <!-- State Kosong -->
            <div class="bg-white rounded-2xl border border-stone-200/90 p-12 text-center max-w-md mx-auto shadow-xs">
                <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-3xl mx-auto mb-4 font-black">
                    🔍
                </div>
                <h3 class="text-lg font-black text-[#14201a] bl-d">Tidak Ada Alat Ditemukan</h3>
                <p class="text-xs text-stone-500 mt-1 leading-relaxed">
                    Kami tidak menemukan perlengkapan outdoor dengan kata kunci atau filter yang Anda pilih. Coba gunakan istilah pencarian lain.
                </p>
                <div class="mt-6">
                    <a href="{{ route('katalog.index') }}" class="px-5 py-2.5 rounded-xl bg-[#0e1b15] text-[#f6c453] text-xs font-bold shadow-xs hover:bg-[#1e3a2c] transition inline-block">
                        Lihat Seluruh Katalog
                    </a>
                </div>
            </div>
        @endif
    </section>
</x-layouts.publik>
