<x-layouts.publik>
    <x-slot:title>Sewa Perlengkapan Outdoor Subang - Buka 24 Jam</x-slot:title>

    <!-- Hero Section -->
    <div class="relative bg-gradient-to-br from-emerald-900 via-emerald-800 to-stone-900 text-white rounded-2xl overflow-hidden shadow-xl mb-10">
        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>
        
        <div class="relative max-w-4xl mx-auto px-6 py-12 sm:py-16 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-700/60 border border-emerald-500/30 text-emerald-200 text-xs font-semibold mb-4 backdrop-blur-sm">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                Toko Buka 24 Jam Non-Stop di Jalancagak, Subang
            </div>

            <h1 class="text-3xl sm:text-5xl font-black tracking-tight leading-tight mb-4">
                Petualangan Dimulai dari Perlengkapan yang Tepat
            </h1>

            <p class="text-base sm:text-lg text-emerald-100 max-w-2xl mx-auto mb-8 font-light leading-relaxed">
                Penyewaan alat outdoor lengkap, bersih, dan terawat untuk pendakian, camping, dan kegiatan alam bebas. Tanpa ribet transfer bank—pesan online, bayar tunai/QRIS langsung di toko.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ url('/alat') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-white text-emerald-900 font-bold text-sm shadow hover:bg-emerald-50 hover:shadow-lg transition">
                    Lihat Semua Alat 🏕️
                </a>
                <a href="#alur-sewa" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-emerald-700/70 hover:bg-emerald-700 text-white border border-emerald-500/40 font-semibold text-sm transition">
                    Cara & Ketentuan Sewa 📋
                </a>
            </div>

            <!-- Mini badges -->
            <div class="mt-10 pt-8 border-t border-emerald-800/60 grid grid-cols-2 sm:grid-cols-4 gap-4 text-left">
                <div class="flex items-center gap-2.5">
                    <span class="text-2xl">⏰</span>
                    <div>
                        <div class="font-bold text-xs">24 Jam Standby</div>
                        <div class="text-[11px] text-emerald-200">Bisa ambil kapan saja</div>
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <span class="text-2xl">💵</span>
                    <div>
                        <div class="font-bold text-xs">Bayar di Toko</div>
                        <div class="text-[11px] text-emerald-200">Manual saat serah terima</div>
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <span class="text-2xl">🪪</span>
                    <div>
                        <div class="font-bold text-xs">Jaminan Fleksibel</div>
                        <div class="text-[11px] text-emerald-200">KTP/SIM asli</div>
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <span class="text-2xl">🧼</span>
                    <div>
                        <div class="font-bold text-xs">Alat Terawat</div>
                        <div class="text-[11px] text-emerald-200">Selalu bersih & wangi</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kategori Section -->
    <div class="mb-12">
        <div class="flex items-end justify-between mb-6">
            <div>
                <h2 class="text-xl sm:text-2xl font-black text-stone-900">Kategori Perlengkapan</h2>
                <p class="text-xs sm:text-sm text-stone-500">Pilih peralatan camping dan pendakian sesuai kebutuhanmu</p>
            </div>
            <a href="{{ url('/alat') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 hover:underline">
                Lihat Semua &rarr;
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            @forelse($kategoriList as $kat)
                <a href="{{ url('/alat?kategori=' . $kat->slug) }}" class="p-4 rounded-xl bg-white border border-stone-200 hover:border-emerald-500 hover:shadow-md transition group">
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-lg mb-3 group-hover:bg-emerald-700 group-hover:text-white transition">
                        ⛺
                    </div>
                    <div class="font-bold text-stone-900 text-sm group-hover:text-emerald-700 transition">
                        {{ $kat->nama }}
                    </div>
                    <div class="text-xs text-stone-400 mt-0.5">
                        {{ $kat->alat_count }} jenis alat
                    </div>
                </a>
            @empty
                <div class="col-span-full p-6 text-center text-stone-400 bg-white rounded-xl border border-stone-200">
                    Belum ada kategori yang ditambahkan.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Alat Unggulan Section -->
    <div class="mb-12">
        <div class="flex items-end justify-between mb-6">
            <div>
                <h2 class="text-xl sm:text-2xl font-black text-stone-900">Alat Populer & Siap Sewa</h2>
                <p class="text-xs sm:text-sm text-stone-500">Unit terlaris yang siap menemani perjalanan mendakimu</p>
            </div>
            <a href="{{ url('/alat') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 hover:underline">
                Katalog Lengkap &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-5">
            @forelse($alatUnggulan as $alat)
                <div class="bg-white rounded-xl border border-stone-200 overflow-hidden hover:shadow-lg transition flex flex-col justify-between">
                    <div>
                        <!-- Foto Preview -->
                        <div class="h-44 bg-stone-100 relative overflow-hidden flex items-center justify-center">
                            @if($alat->foto)
                                <img src="{{ asset('storage/' . $alat->foto) }}" alt="{{ $alat->nama }}" class="w-full h-full object-cover">
                            @else
                                <div class="text-center p-4">
                                    <span class="text-4xl text-stone-300 block mb-1">🌲</span>
                                    <span class="text-[11px] text-stone-400 font-medium">{{ $alat->kategori?->nama ?? 'Alat Outdoor' }}</span>
                                </div>
                            @endif
                            
                            <span class="absolute top-2 left-2 px-2 py-0.5 rounded text-[10px] font-bold bg-white/90 backdrop-blur-sm text-stone-700 shadow-sm border border-stone-200">
                                {{ $alat->kategori?->nama ?? 'Umum' }}
                            </span>

                            @if($alat->stok_tersedia > 0)
                                <span class="absolute top-2 right-2 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-600 text-white shadow-sm">
                                    Tersedia {{ $alat->stok_tersedia }}
                                </span>
                            @else
                                <span class="absolute top-2 right-2 px-2 py-0.5 rounded text-[10px] font-bold bg-rose-600 text-white shadow-sm">
                                    Stok Habis
                                </span>
                            @endif
                        </div>

                        <!-- Info Alat -->
                        <div class="p-4">
                            <h3 class="font-bold text-stone-900 text-sm line-clamp-1 mb-1" title="{{ $alat->nama }}">
                                {{ $alat->nama }}
                            </h3>
                            <p class="text-xs text-stone-500 line-clamp-2 mb-3">
                                {{ $alat->deskripsi ?? 'Perlengkapan berkualitas tinggi, bersih dan siap pakai untuk aktivitas outdoor.' }}
                            </p>
                        </div>
                    </div>

                    <!-- Footer Harga & Aksi -->
                    <div class="p-4 pt-0">
                        <div class="flex items-center justify-between mb-3 border-t border-stone-100 pt-3">
                            <div>
                                <span class="text-[10px] text-stone-400 block">Tarif Sewa</span>
                                <span class="text-base font-black text-emerald-800">
                                    Rp {{ number_format($alat->harga_per_hari, 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] text-stone-500">/hari</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-stone-400 block">Total Unit</span>
                                <span class="text-xs font-semibold text-stone-700">{{ $alat->stok_total }} unit</span>
                            </div>
                        </div>

                        <a href="{{ url('/alat/' . $alat->id) }}" class="block w-full text-center py-2 px-3 rounded-lg bg-stone-900 hover:bg-emerald-800 text-white text-xs font-bold transition shadow-sm">
                            Pilih & Cek Jadwal 🛒
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full p-8 text-center bg-white rounded-xl border border-stone-200 text-stone-400">
                    Belum ada data alat yang ditampilkan.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Alur Sewa Section -->
    <div id="alur-sewa" class="bg-stone-900 text-white rounded-2xl p-6 sm:p-10 mb-12 shadow-md">
        <div class="max-w-2xl mb-8">
            <span class="text-[11px] font-bold text-emerald-400 uppercase tracking-wider block mb-1">Panduan Pelanggan</span>
            <h2 class="text-2xl font-black tracking-tight text-white mb-2">Alur Penyewaan di BLNTRK Outdoor</h2>
            <p class="text-xs sm:text-sm text-stone-400">
                Proses pemesanan cepat dan jelas, tanpa perlu transfer bank online.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            <div class="p-4 rounded-xl bg-stone-800/60 border border-stone-700">
                <div class="w-8 h-8 rounded-full bg-emerald-600 text-white font-black text-sm flex items-center justify-center mb-3">1</div>
                <h4 class="font-bold text-sm text-white mb-1">Pilih Alat & Tanggal</h4>
                <p class="text-xs text-stone-400 leading-relaxed">
                    Tentukan tanggal ambil dan kembali. Sistem otomatis mengecek ketersediaan unit.
                </p>
            </div>

            <div class="p-4 rounded-xl bg-stone-800/60 border border-stone-700">
                <div class="w-8 h-8 rounded-full bg-emerald-600 text-white font-black text-sm flex items-center justify-center mb-3">2</div>
                <h4 class="font-bold text-sm text-white mb-1">Buat Pesanan Online</h4>
                <p class="text-xs text-stone-400 leading-relaxed">
                    Daftar/masuk akun untuk menyimpan pesanan dengan nomor kode unik sewa.
                </p>
            </div>

            <div class="p-4 rounded-xl bg-stone-800/60 border border-stone-700">
                <div class="w-8 h-8 rounded-full bg-emerald-600 text-white font-black text-sm flex items-center justify-center mb-3">3</div>
                <h4 class="font-bold text-sm text-white mb-1">Datang ke Toko</h4>
                <p class="text-xs text-stone-400 leading-relaxed">
                    Kunjungi toko di Kumpay. Tunjukkan KTP/SIM asli sebagai jaminan dan bayar sewa (Tunai/QRIS).
                </p>
            </div>

            <div class="p-4 rounded-xl bg-stone-800/60 border border-stone-700">
                <div class="w-8 h-8 rounded-full bg-emerald-600 text-white font-black text-sm flex items-center justify-center mb-3">4</div>
                <h4 class="font-bold text-sm text-white mb-1">Ambil & Kembalikan</h4>
                <p class="text-xs text-stone-400 leading-relaxed">
                    Periksa kelengkapan alat bersama admin. Kembalikan tepat waktu sebelum cut-off 12:00 siang.
                </p>
            </div>
        </div>
    </div>

    <!-- Informasi Toko Fisik Section -->
    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-6 sm:p-8 flex flex-col md:flex-row items-center justify-between gap-6">
        <div>
            <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider block mb-1">Lokasi Kami</span>
            <h3 class="text-lg sm:text-xl font-black text-emerald-950 mb-1">
                {{ $toko['nama'] }}
            </h3>
            <p class="text-xs sm:text-sm text-emerald-800 mb-2">
                📍 {{ $toko['alamat'] }}
            </p>
            <p class="text-xs text-emerald-700">
                Buka 24 jam untuk melayani pendaki yang berangkat tengah malam atau dini hari.
            </p>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <a href="https://wa.me/{{ $toko['wa'] }}" target="_blank" class="w-full md:w-auto px-5 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs text-center shadow-sm transition">
                Hubungi via WhatsApp 💬
            </a>
            <a href="{{ url('/alat') }}" class="w-full md:w-auto px-5 py-2.5 rounded-xl bg-white border border-emerald-300 hover:bg-emerald-100 text-emerald-900 font-bold text-xs text-center transition">
                Pesan Sekarang ⛺
            </a>
        </div>
    </div>
</x-layouts.publik>
