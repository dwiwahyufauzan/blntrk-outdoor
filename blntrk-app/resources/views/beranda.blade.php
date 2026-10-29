@php
    $heroLocal = public_path('images/hero-blntrk.jpg');
    $heroImg = file_exists($heroLocal)
        ? asset('images/hero-blntrk.jpg')
        : 'https://images.unsplash.com/photo-1510312305653-8ed496efae75?auto=format&fit=crop&w=2000&q=80';

    $brands = [
        ['nama' => 'EIGER', 'desc' => 'Tropical Adventure'],
        ['nama' => 'CONSINA', 'desc' => 'The Outdoor Lifestyle'],
        ['nama' => 'TENDAKI ID', 'desc' => 'Tents & Camping Gear'],
        ['nama' => 'MERAPI MOUNTAIN', 'desc' => 'Expedition Tested'],
        ['nama' => 'AREI', 'desc' => 'Outdoorgear Pro'],
        ['nama' => 'QUECHUA', 'desc' => 'Camping & Hiking Gear'],
        ['nama' => 'DEUTER', 'desc' => 'German Backpacks'],
        ['nama' => 'MOKZHAWARE', 'desc' => 'Mountaineering Gear'],
        ['nama' => 'RUBTRACK', 'desc' => 'Adventure Footwear'],
        ['nama' => 'GREAT OUTDOOR', 'desc' => 'Durable Camping Tents'],
    ];

    $steps = [
        ['Pilih alat dan tanggal sewa', 'Tentukan tanggal ambil dan durasi pemakaian. Sistem langsung menampilkan unit yang ready stok.'],
        ['Buat pesanan online', 'Masuk atau daftar akun, pilih alat yang diinginkan, dan konfirmasi pesanan untuk mendapatkan nomor resi sewa.'],
        ['Datang ke toko Subang', 'Tunjukkan nomor resi dan serahkan kartu identitas asli (KTP/SIM) sebagai jaminan, lalu bayar tunai atau QRIS.'],
        ['Ambil alat & kembalikan tepat waktu', 'Periksa kelengkapan unit bersama staf toko. Kembalikan perlengkapan sebelum batas jam 12:00 siang.'],
    ];

    $testimonials = [
        [
            'nama' => 'Reza Pratama',
            'role' => 'Pendaki Gunung Ciremai & Tangkuban Parahu',
            'judul' => 'Kondisi Tenda Bersih & Siap Tempur',
            'ulasan' => 'Sewa tenda kapasitas 4 orang buat naik Tangkuban Parahu. Tendanya wangi, frame lengkap, pasak tidak ada yang bengkok. Buka 24 jam jadi sangat menolong!',
            'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=120&h=120&q=80',
        ],
        [
            'nama' => 'Siti Maulida',
            'role' => 'Camper Ciater & Sari Ater',
            'judul' => 'Proses Cepat & Admin Sangat Ramah',
            'ulasan' => 'Proses booking online sangat gampang. Sampai di toko Kumpay tinggal tunjukin KTP dan bayar via QRIS. Alatnya dicek bareng-bareng di depan kita.',
            'avatar' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=120&h=120&q=80',
        ],
        [
            'nama' => 'Dimas Aditya',
            'role' => 'Komunitas Trail Run Subang',
            'judul' => 'Bisa Ambil Tengah Malam Tanpa Ribet',
            'ulasan' => 'Berangkat jam 2 pagi tetap dilayani dengan sangat baik. Sangat rekomended untuk pendaki yang butuh perlengkapan mendesak di jalur Subang!',
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=120&h=120&q=80',
        ],
    ];

    $faq = [
        ['Apakah harus bayar transfer saat memesan online?', 'Tidak. Pesanan dibuat online secara gratis, pembayaran dilakukan langsung di toko saat alat diambil (Tunai atau QRIS tanpa biaya admin).'],
        ['Jaminan apa yang wajib dibawa saat pengambilan?', 'Wajib membawa kartu identitas asli yang masih berlaku (KTP atau SIM). Jaminan akan disimpan aman dan dikembalikan utuh saat alat dikembalikan lengkap.'],
        ['Kapan batas jam maksimal pengembalian alat?', 'Batas waktu pengembalian adalah pukul 12:00 siang pada tanggal kembali yang telah disepakati.'],
        ['Apakah toko benar-benar buka 24 jam?', 'Ya, toko BLNTRK Outdoor di Desa Kumpay, Jalancagak, Subang buka 24 jam setiap hari untuk melayani pendaki yang berangkat atau tiba tengah malam/dini hari.'],
    ];
@endphp

<x-layouts.publik>
    <x-slot:title>Sewa Perlengkapan Outdoor Subang - Buka 24 Jam</x-slot:title>

    <!-- HERO SECTION (FORESTA ADVENTURE STYLE) -->
    <section class="relative isolate overflow-hidden rounded-[28px] sm:rounded-[36px] bg-[#0e1b15] text-white min-h-[580px] sm:min-h-[640px] flex items-center shadow-xl">
        <!-- Hero Background Image -->
        <img src="{{ $heroImg }}" alt="Tenda Camping Outdoor BLNTRK" class="absolute inset-0 -z-20 h-full w-full object-cover object-center opacity-40 brightness-95">
        
        <!-- Deep Forest Gradient Overlay -->
        <div class="absolute inset-0 -z-10 bg-gradient-to-r from-[#0a140f] via-[#0a140f]/80 to-[#0a140f]/30"></div>
        <div class="absolute inset-0 -z-10 bg-gradient-to-t from-[#0a140f] via-transparent to-transparent"></div>

        <div class="relative w-full max-w-5xl px-6 sm:px-12 lg:px-16 py-16 sm:py-20">
            <!-- Lime Subtitle -->
            <p class="text-[#5edd5b] font-urbanist font-extrabold text-sm sm:text-base tracking-wide uppercase mb-3 flex items-center gap-2">
                <span class="inline-block w-2 h-2 rounded-full bg-[#5edd5b] animate-ping"></span>
                Siap Menjelajah, Tanpa Ribet.
            </p>

            <!-- Main Heading -->
            <h1 class="font-urbanist text-white font-black text-4xl sm:text-6xl lg:text-7xl leading-[1.08] tracking-tight mb-6">
                Sewa Gear Outdoor <br class="hidden sm:inline">Kini Lebih Mudah!
            </h1>

            <p class="text-stone-300 text-sm sm:text-base max-w-xl leading-relaxed mb-8">
                Pusat penyewaan tenda dome, carrier, sleeping bag, kompor portable, dan gear pendakian lengkap di Subang. Cek ketersediaan real-time, bayar di toko saat serah terima.
            </p>

            <!-- Feature Checkmark Ticks (Foresta Style) -->
            <div class="flex flex-wrap gap-2.5 sm:gap-3 text-xs sm:text-sm font-semibold text-white">
                <span class="inline-flex items-center gap-2 bg-black/40 backdrop-blur-md px-4 py-2 rounded-full border border-white/10 shadow-xs">
                    <span class="w-5 h-5 rounded-full bg-[#5edd5b] text-black flex items-center justify-center font-black text-xs">
                        <svg class="w-3 h-3 text-black" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    Stok Real-Time & Lengkap
                </span>
                <span class="inline-flex items-center gap-2 bg-black/40 backdrop-blur-md px-4 py-2 rounded-full border border-white/10 shadow-xs">
                    <span class="w-5 h-5 rounded-full bg-[#5edd5b] text-black flex items-center justify-center font-black text-xs">
                        <svg class="w-3 h-3 text-black" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    Praktis & Cepat
                </span>
                <span class="inline-flex items-center gap-2 bg-black/40 backdrop-blur-md px-4 py-2 rounded-full border border-white/10 shadow-xs">
                    <span class="w-5 h-5 rounded-full bg-[#5edd5b] text-black flex items-center justify-center font-black text-xs">
                        <svg class="w-3 h-3 text-black" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    Buka 24 Jam di Jalancagak Subang
                </span>
            </div>
        </div>
    </section>

    <!-- FLOATING SCHEDULE & SEARCH PICKER CARD (FORESTA ADVENTURE SIGNATURE COMPONENT) -->
    <div class="relative z-30 max-w-6xl mx-auto -mt-10 sm:-mt-14 px-2 sm:px-4 mb-16">
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-stone-200/90 shadow-2xl p-4 sm:p-6 transition hover:shadow-2xl">
            <form action="{{ route('katalog.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                <!-- Kolom 1: Tgl Ambil -->
                <div class="md:col-span-3 border-b md:border-b-0 md:border-r border-stone-200 pb-3 md:pb-0 md:pr-4">
                    <label for="bl-mulai" class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-[#1e3a2c]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Tgl Ambil
                    </label>
                    <input id="bl-mulai" type="date" name="mulai" min="{{ now()->format('Y-m-d') }}" value="{{ request('mulai', now()->format('Y-m-d')) }}"
                           class="w-full text-sm font-bold text-stone-900 border-0 p-0 focus:ring-0 focus:outline-none cursor-pointer bg-transparent">
                </div>

                <!-- Kolom 2: Durasi Dropdown -->
                <div class="md:col-span-2 border-b md:border-b-0 md:border-r border-stone-200 pb-3 md:pb-0 md:pr-4">
                    <label for="bl-durasi" class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-[#1e3a2c]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Durasi
                    </label>
                    <select id="bl-durasi" name="durasi" class="w-full text-sm font-bold text-stone-900 border-0 p-0 focus:ring-0 focus:outline-none cursor-pointer bg-transparent">
                        <option value="1" {{ request('durasi', 1) == 1 ? 'selected' : '' }}>1 Hari (24 Jam)</option>
                        <option value="2" {{ request('durasi') == 2 ? 'selected' : '' }}>2 Hari</option>
                        <option value="3" {{ request('durasi') == 3 ? 'selected' : '' }}>3 Hari</option>
                        <option value="4" {{ request('durasi') == 4 ? 'selected' : '' }}>4 Hari</option>
                        <option value="5" {{ request('durasi') == 5 ? 'selected' : '' }}>5 Hari</option>
                        <option value="7" {{ request('durasi') == 7 ? 'selected' : '' }}>7 Hari (1 Minggu)</option>
                        <option value="10" {{ request('durasi') == 10 ? 'selected' : '' }}>10 Hari</option>
                    </select>
                </div>

                <!-- Kolom 3: Maks. Pengembalian (Otomatis Terhitung) -->
                <div class="md:col-span-3 border-b md:border-b-0 md:border-r border-stone-200 pb-3 md:pb-0 md:pr-4">
                    <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-[#1e3a2c]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        Maks. Pengembalian
                    </label>
                    <input type="hidden" id="bl-selesai" name="selesai" value="{{ request('selesai', now()->addDay()->format('Y-m-d')) }}">
                    <div id="bl-kembali-text" class="text-sm font-bold text-stone-800 truncate">
                        {{ now()->addDay()->translatedFormat('D, d M Y') }}, 12:00
                    </div>
                </div>

                <!-- Kolom 4: Cari Kata Kunci -->
                <div class="md:col-span-2 border-b md:border-b-0 pb-3 md:pb-0">
                    <label for="bl-cari" class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-[#1e3a2c]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Cari
                    </label>
                    <input id="bl-cari" type="text" name="q" value="{{ request('q') }}" placeholder="Tenda, carrier..."
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

    <!-- SECTION: PILIHAN BRAND (FORESTA ADVENTURE STYLE) -->
    <section class="mb-16">
        <div class="mb-6">
            <h2 class="font-urbanist text-2xl sm:text-3xl font-black text-stone-900 tracking-tight">Pilihan Brand</h2>
            <p class="text-xs sm:text-sm text-stone-500 mt-1">Dipilih dari merek terbaik untuk pengalaman petualangan terbaik.</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3 sm:gap-4">
            @foreach($brands as $b)
                <a href="{{ route('katalog.index', ['q' => $b['nama']]) }}" class="bg-white rounded-2xl border border-stone-200/80 p-4 h-22 flex flex-col items-center justify-center text-center shadow-xs hover:shadow-md hover:border-[#5edd5b] transition group">
                    <span class="font-urbanist font-black text-sm sm:text-base tracking-wider text-stone-900 group-hover:text-[#1e3a2c] transition">{{ $b['nama'] }}</span>
                    <span class="text-[10px] text-stone-400 uppercase tracking-wider font-semibold mt-0.5">{{ $b['desc'] }}</span>
                </a>
            @endforeach
        </div>
    </section>

    <!-- SECTION: PILIHAN TERBAIK MINGGU INI / PRODUK UNGGULAN (FORESTA ADVENTURE STYLE) -->
    <section class="mb-16">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
            <div>
                <h2 class="font-urbanist text-2xl sm:text-4xl font-black text-stone-900 tracking-tight">Pilihan Terbaik Minggu Ini!</h2>
                <p class="text-xs sm:text-sm text-stone-500 mt-1">
                    Gear Pilihan Pendaki, Siap Temani Petualanganmu. <strong>Paling sering disewa</strong> oleh pendaki &amp; campers Subang.
                </p>
            </div>
            <a href="{{ route('katalog.index') }}" class="inline-flex items-center gap-2 bg-[#5edd5b] hover:bg-[#4ed04b] text-black font-urbanist font-bold text-xs sm:text-sm px-5 py-2.5 rounded-xl shadow-xs transition hover:scale-[1.02] w-fit">
                <span>Lihat Lainnya</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>

        <!-- Product Grid with 1:1 Aspect Ratio Cards -->
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
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
            @forelse($alatUnggulan as $alat)
                @php
                    $imgSrc = $alat->foto 
                        ? asset('storage/' . $alat->foto) 
                        : ($catPhotos[$alat->kategori?->nama ?? ''] ?? 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?auto=format&fit=crop&w=600&q=80');
                    $stokTersedia = $alat->kapasitas_fisik;
                @endphp
                <div class="bg-white rounded-2xl border border-stone-200/80 p-4 sm:p-5 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                    <!-- Image Wrapper (1:1 Ratio Square) -->
                    <a href="{{ route('katalog.show', $alat->id) }}" class="block relative aspect-square bg-stone-50 rounded-xl overflow-hidden p-2 flex items-center justify-center mb-4">
                        <img src="{{ $imgSrc }}" alt="{{ $alat->nama }}" loading="lazy"
                             class="object-cover w-full h-full rounded-lg group-hover:scale-105 transition-transform duration-300">

                        <!-- Availability Pill -->
                        <div class="absolute top-3 left-3">
                            @if($stokTersedia > 0)
                                <span class="bg-[#5edd5b] text-black text-[10px] font-black px-2.5 py-0.5 rounded-full shadow-md">
                                    Tersedia {{ $stokTersedia }}
                                </span>
                            @else
                                <span class="bg-rose-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-md">
                                    Habis
                                </span>
                            @endif
                        </div>
                    </a>

                    <!-- Title & Price (Foresta Centered Clean Style) -->
                    <div class="text-center flex-grow flex flex-col justify-between">
                        <h3 class="line-clamp-2 text-xs sm:text-sm font-bold text-stone-900 group-hover:text-[#1e3a2c] transition min-h-[36px] flex items-center justify-center" title="{{ $alat->nama }}">
                            <a href="{{ route('katalog.show', $alat->id) }}">
                                {{ $alat->nama }}{{ $alat->varian ? ' - ' . $alat->varian : '' }}
                            </a>
                        </h3>
                        
                        <div class="mt-2.5">
                            <span class="font-urbanist font-black text-sm sm:text-base text-stone-900 block">
                                Rp {{ number_format($alat->harga_per_hari, 0, ',', '.') }}
                                <span class="text-[11px] text-stone-500 font-normal">/hari</span>
                            </span>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <a href="{{ route('katalog.show', $alat->id) }}" class="mt-3.5 block w-full py-2.5 rounded-xl bg-stone-100 hover:bg-[#5edd5b] text-stone-800 hover:text-black text-xs font-bold text-center transition">
                        Pilih &amp; Sewa
                    </a>
                </div>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-stone-300 bg-white p-12 text-center text-sm text-stone-500">
                    Belum ada alat yang ditampilkan di etalase unggulan.
                </div>
            @endforelse
        </div>
    </section>

    <!-- SECTION: KATEGORI PERLENGKAPAN -->
    <section class="mb-16">
        <div class="flex items-end justify-between gap-4 mb-6">
            <div>
                <h2 class="font-urbanist text-2xl sm:text-3xl font-black text-stone-900 tracking-tight">Kategori Perlengkapan</h2>
                <p class="text-xs sm:text-sm text-stone-500 mt-1">Pilih perlengkapan sesuai jenis petualangan dan kebutuhan Anda.</p>
            </div>
            <a href="{{ route('katalog.index') }}" class="text-xs sm:text-sm font-bold text-[#0e1b15] hover:text-[#5edd5b] transition inline-flex items-center gap-1.5 group">
                <span>Semua Kategori</span>
                <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
            @forelse($kategoriList as $kat)
                <a href="{{ route('katalog.index', ['kategori' => $kat->slug]) }}"
                   class="bg-white rounded-2xl border border-stone-200/80 p-5 flex items-center gap-3.5 shadow-xs hover:border-[#5edd5b] hover:shadow-md transition group">
                    <div class="w-11 h-11 rounded-xl bg-[#0e1b15] text-[#5edd5b] flex items-center justify-center shrink-0 group-hover:scale-105 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 20 12 4l9 16zM12 20v-6l-3 6M12 14l3 6"/></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-stone-900 group-hover:text-[#1e3a2c] transition">{{ $kat->nama }}</h4>
                        <span class="text-xs text-stone-400 font-semibold">{{ $kat->alat_count }} unit ready</span>
                    </div>
                </a>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-stone-300 bg-white p-8 text-center text-sm text-stone-500">
                    Belum ada kategori yang terdaftar.
                </div>
            @endforelse
        </div>
    </section>

    <!-- SECTION: CARA SEWA (4 LANGKAH MUDAH) -->
    <section id="alur-sewa" class="relative mb-16 overflow-hidden rounded-[28px] sm:rounded-[36px] bg-[#0e1b15] text-white p-6 sm:p-12 shadow-xl">
        <div class="relative max-w-xl mb-10">
            <span class="text-[#5edd5b] font-urbanist font-bold text-xs uppercase tracking-wider block mb-1">Panduan Praktis</span>
            <h2 class="font-urbanist text-2xl sm:text-4xl font-black text-white tracking-tight">Empat Langkah Sampai Alat di Tangan</h2>
            <p class="text-stone-400 text-xs sm:text-sm mt-2">Pesan unit dari rumah melalui HP, serah terima alat di toko tanpa antre lama.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            @foreach($steps as $i => $s)
                <div class="bg-white/5 border border-white/10 rounded-2xl p-5 backdrop-blur-sm hover:bg-white/10 transition">
                    <div class="w-10 h-10 rounded-full bg-[#5edd5b] text-black font-urbanist font-black text-base flex items-center justify-center mb-4 shadow-sm">
                        {{ $i + 1 }}
                    </div>
                    <h3 class="text-sm font-bold text-white mb-1.5">{{ $s[0] }}</h3>
                    <p class="text-xs text-stone-400 leading-relaxed">{{ $s[1] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <!-- SECTION: TESTIMONI PELANGGAN (FORESTA STYLE - APA KATA MEREKA?) -->
    <section class="mb-16">
        <div class="text-center max-w-xl mx-auto mb-10">
            <span class="bg-white border border-stone-200 text-stone-600 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider inline-block mb-2 shadow-xs">
                Testimoni Pelanggan
            </span>
            <h2 class="font-urbanist text-2xl sm:text-4xl font-black text-stone-900 tracking-tight">Apa Kata Mereka?</h2>
            <p class="text-xs sm:text-sm text-stone-500 mt-1">Pengalaman nyata para pendaki dan wisatawan yang menyewa di BLNTRK Outdoor.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($testimonials as $t)
                <div class="bg-white rounded-2xl border border-stone-200/80 p-6 shadow-xs flex flex-col justify-between hover:shadow-lg transition">
                    <div>
                        <!-- 5 Stars with Foresta Green Accents -->
                        <div class="flex items-center gap-1 mb-3">
                            @for($s = 0; $s < 5; $s++)
                                <span class="w-6 h-6 rounded-md bg-[#5edd5b] text-black flex items-center justify-center shadow-xs">
                                    <svg class="w-3.5 h-3.5 text-black" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                </span>
                            @endfor
                        </div>
                        <h3 class="text-base font-bold text-stone-900 mb-2">{{ $t['judul'] }}</h3>
                        <p class="text-xs sm:text-sm text-stone-600 leading-relaxed">"{{ $t['ulasan'] }}"</p>
                    </div>

                    <div class="flex items-center gap-3 mt-6 pt-4 border-t border-stone-100">
                        <img src="{{ $t['avatar'] }}" alt="{{ $t['nama'] }}" class="w-10 h-10 rounded-full object-cover border border-stone-200">
                        <div>
                            <div class="text-xs font-bold text-stone-900">{{ $t['nama'] }}</div>
                            <div class="text-[11px] text-stone-400">{{ $t['role'] }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- SECTION: PERTANYAAN UMUM (FAQ) -->
    <section id="faq" class="mb-16 grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <div class="lg:col-span-5">
            <span class="text-[#1e3a2c] font-urbanist font-extrabold text-xs uppercase tracking-wider block mb-1">Bantuan &amp; Info</span>
            <h2 class="font-urbanist text-2xl sm:text-4xl font-black text-stone-900 tracking-tight mb-3">Pertanyaan yang Sering Muncul</h2>
            <p class="text-xs sm:text-sm text-stone-500 leading-relaxed mb-6">
                Punya pertanyaan seputar tarif harian, ketentuan jaminan, atau lokasi penjemputan alat? Simak penjelasan di samping.
            </p>
            <a href="https://wa.me/{{ $toko['wa'] }}" target="_blank" class="inline-flex items-center gap-2 bg-white border border-stone-300 text-stone-800 hover:border-black font-bold text-xs sm:text-sm px-5 py-2.5 rounded-xl shadow-xs transition">
                <span>Tanya via WhatsApp</span>
                <svg class="w-4 h-4 text-[#5edd5b]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            </a>
        </div>

        <div class="lg:col-span-7 space-y-3">
            @foreach($faq as $f)
                <details class="group bg-white rounded-2xl border border-stone-200/80 p-5 shadow-xs [&_summary::-webkit-details-marker]:hidden">
                    <summary class="flex items-center justify-between cursor-pointer font-bold text-stone-900 text-sm">
                        <span>{{ $f[0] }}</span>
                        <svg class="w-4 h-4 text-stone-400 transition-transform duration-300 group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </summary>
                    <p class="text-xs sm:text-sm text-stone-600 leading-relaxed mt-3 pt-3 border-t border-stone-100">
                        {{ $f[1] }}
                    </p>
                </details>
            @endforeach
        </div>
    </section>

    <!-- SECTION: LOKASI & QUICK WHATSAPP CALLOUT -->
    <section class="relative overflow-hidden rounded-[28px] sm:rounded-[36px] bg-[#0e1b15] text-white p-6 sm:p-10 shadow-xl mb-12">
        <div class="relative flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
                <span class="text-[#5edd5b] font-urbanist font-bold text-xs uppercase tracking-wider block mb-1">Lokasi Toko Buka 24 Jam</span>
                <h3 class="font-urbanist text-xl sm:text-3xl font-black text-white">{{ $toko['nama'] }}</h3>
                <p class="text-xs sm:text-sm text-stone-300 mt-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#5edd5b] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>{{ $toko['alamat'] }}</span>
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                <a href="https://wa.me/{{ $toko['wa'] }}" target="_blank" class="bg-[#5edd5b] hover:bg-[#4ed04b] text-black font-urbanist font-black text-xs sm:text-sm px-6 py-3.5 rounded-xl text-center shadow-md transition hover:scale-[1.02]">
                    Chat WhatsApp Toko
                </a>
                <a href="{{ route('katalog.index') }}" class="bg-white/10 hover:bg-white/20 text-white font-bold text-xs sm:text-sm px-6 py-3.5 rounded-xl text-center border border-white/20 transition">
                    Buka Katalog Alat
                </a>
            </div>
        </div>
    </section>

    <!-- FLOATING WHATSAPP BUTTON (FORESTA ADVENTURE) -->
    <a href="https://wa.me/{{ $toko['wa'] }}" target="_blank" rel="noopener" aria-label="Chat WhatsApp BLNTRK Outdoor"
       class="fixed bottom-6 right-6 z-40 flex h-14 w-14 items-center justify-center rounded-full bg-[#5edd5b] text-black shadow-2xl transition hover:scale-110">
        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#5edd5b] opacity-40"></span>
        <svg class="relative h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.861.174.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.086s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
        </svg>
    </a>

    <!-- SCRIPT FOR SCHEDULE PICKER DATE SYNC -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mulaiInput = document.getElementById('bl-mulai');
            const durasiSelect = document.getElementById('bl-durasi');
            const selesaiHidden = document.getElementById('bl-selesai');
            const kembaliText = document.getElementById('bl-kembali-text');

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