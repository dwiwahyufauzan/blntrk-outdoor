<x-layouts.admin>
    <x-slot:title>Dashboard Operasional Toko</x-slot:title>

    @php
        $totalSewa7Hari = array_sum($chartData);
        $hariChart = max(count($chartData), 1);
        $rataSewaHarian = round($totalSewa7Hari / $hariChart, 1);
        $puncakSewa = count($chartData) ? max($chartData) : 0;
        $perluPerhatian = $statistik['pesanan_terlambat'] + $statistik['perlu_diambil_hari_ini'] + $statistik['pesanan_menunggu'];
        $namaAdmin = explode(' ', trim(auth()->user()?->nama ?? auth()->user()?->name ?? 'Admin'))[0];
    @endphp

    @if($statistik['pesanan_noshow'] > 0)
        <div class="mb-6">
            <a href="{{ route('admin.pesanan.index', ['status' => 'dibayar']) }}"
               class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-950 text-xs sm:text-sm flex items-start gap-3 hover:bg-amber-100/70 transition">
                <span class="w-6 h-6 rounded-full bg-amber-500 text-white flex items-center justify-center shrink-0 font-black">!</span>
                <span><strong>{{ $statistik['pesanan_noshow'] }} pesanan sudah dibayar tetapi tidak diambil</strong> hingga lewat batas pengambilan. Stoknya masih tertahan; sistem membatalkannya otomatis (no-show) sesuai pengaturan, atau batalkan manual.</span>
            </a>
        </div>
    @endif

    <!-- ========================= HERO ========================= -->
    <section class="relative overflow-hidden rounded-3xl bg-[#0e1b15] text-white mb-6 sm:mb-8 shadow-sm">
        <!-- Glow dekoratif (satu-satunya aksen visual di hero) -->
        <div
            class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_85%_0%,rgba(94,221,91,0.28),transparent_55%)]">
        </div>
        <div class="pointer-events-none absolute -bottom-24 -left-16 w-72 h-72 rounded-full bg-[#5edd5b]/10 blur-3xl">
        </div>

        <div class="relative p-5 sm:p-8 grid grid-cols-1 xl:grid-cols-12 gap-6 xl:gap-8 xl:items-center">
            <!-- Kiri: sapaan + aksi -->
            <div class="xl:col-span-7">
                <span
                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 ring-1 ring-white/15 text-[11px] font-bold text-[#5edd5b]">
                    <span class="relative flex w-2 h-2">
                        @if($perluPerhatian > 0)
                            <span
                                class="absolute inline-flex w-full h-full rounded-full bg-[#5edd5b] opacity-70 animate-ping"></span>
                        @endif
                        <span class="relative inline-flex w-2 h-2 rounded-full bg-[#5edd5b]"></span>
                    </span>
                    Pusat Kendali Operasional
                </span>

                <h1 class="mt-4 text-2xl sm:text-4xl font-extrabold tracking-tight leading-tight bl-d">
                    Halo, {{ $namaAdmin }}.
                    <span class="block text-white/70 font-bold">
                        @if($perluPerhatian > 0)
                            Ada {{ $perluPerhatian }} hal yang perlu ditangani hari ini.
                        @else
                            Semua operasional hari ini aman terkendali.
                        @endif
                    </span>
                </h1>
                <p class="mt-3 text-xs sm:text-sm text-white/55 max-w-xl">
                    Status transaksi rental harian, serah terima alat, jaminan fisik, dan pengembalian &bull;
                    {{ now()->translatedFormat('l, d F Y') }}
                </p>

                <div class="mt-6 flex flex-wrap items-center gap-2.5">
                    <a href="{{ route('admin.pesanan.create') }}"
                        class="px-4 py-2.5 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-extrabold shadow-sm transition inline-flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Sewa Walk-in Baru</span>
                    </a>
                    <a href="{{ route('admin.laporan.index') }}"
                        class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/15 ring-1 ring-white/15 text-white text-xs font-bold transition inline-flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Laporan Usaha</span>
                    </a>
                </div>
            </div>

            <!-- Kanan: ringkasan prioritas -->
            <div class="xl:col-span-5 grid grid-cols-3 gap-2.5 sm:gap-3">
                <a href="{{ route('admin.pesanan.index', ['status' => 'terlambat']) }}"
                    class="rounded-2xl p-3.5 sm:p-4 bg-white/[0.06] hover:bg-white/10 ring-1 {{ $statistik['pesanan_terlambat'] > 0 ? 'ring-rose-400/50' : 'ring-white/10' }} transition">
                    <span class="block text-[11px] text-white/55 font-semibold">Terlambat</span>
                    <span
                        class="block mt-1.5 text-2xl sm:text-3xl font-black font-urbanist tracking-tight {{ $statistik['pesanan_terlambat'] > 0 ? 'text-rose-300' : 'text-white' }}">{{ $statistik['pesanan_terlambat'] }}</span>
                </a>
                <a href="{{ route('admin.pesanan.index', ['status' => 'dibayar']) }}"
                    class="rounded-2xl p-3.5 sm:p-4 bg-white/[0.06] hover:bg-white/10 ring-1 ring-white/10 transition">
                    <span class="block text-[11px] text-white/55 font-semibold">Ambil hari ini</span>
                    <span
                        class="block mt-1.5 text-2xl sm:text-3xl font-black font-urbanist tracking-tight text-[#5edd5b]">{{ $statistik['perlu_diambil_hari_ini'] }}</span>
                </a>
                <a href="{{ route('admin.pesanan.index', ['status' => 'menunggu']) }}"
                    class="rounded-2xl p-3.5 sm:p-4 bg-white/[0.06] hover:bg-white/10 ring-1 ring-white/10 transition">
                    <span class="block text-[11px] text-white/55 font-semibold">Menunggu bayar</span>
                    <span
                        class="block mt-1.5 text-2xl sm:text-3xl font-black font-urbanist tracking-tight text-amber-300">{{ $statistik['pesanan_menunggu'] }}</span>
                </a>
            </div>
        </div>
    </section>

    <!-- ========================= KARTU METRIK ========================= -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4 mb-6 sm:mb-8">
        <!-- 1. Menunggu Bayar -->
        <a href="{{ route('admin.pesanan.index', ['status' => 'menunggu']) }}"
            class="group relative bg-white p-4 sm:p-5 rounded-2xl border border-stone-200/90 shadow-xs hover:border-amber-300 hover:shadow-md transition-all cursor-pointer">
            <div
                class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-600 ring-1 ring-amber-500/20 flex items-center justify-center group-hover:scale-105 transition">
                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <span
                class="block mt-4 text-3xl sm:text-4xl font-black text-[#14201a] font-urbanist tracking-tight leading-none">{{ $statistik['pesanan_menunggu'] }}</span>
            <span class="block mt-2 text-xs font-bold text-stone-800">Menunggu Bayar</span>
            <span class="block text-[11px] text-stone-500">reservasi aktif</span>
            <span class="absolute top-4 right-4 w-1.5 h-1.5 rounded-full bg-amber-500"></span>
        </a>

        <!-- 2. Ambil Hari Ini -->
        <a href="{{ route('admin.pesanan.index', ['status' => 'dibayar']) }}"
            class="group relative bg-white p-4 sm:p-5 rounded-2xl border border-stone-200/90 shadow-xs hover:border-emerald-300 hover:shadow-md transition-all cursor-pointer">
            <div
                class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-600 ring-1 ring-emerald-500/20 flex items-center justify-center group-hover:scale-105 transition">
                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <span
                class="block mt-4 text-3xl sm:text-4xl font-black text-[#14201a] font-urbanist tracking-tight leading-none">{{ $statistik['perlu_diambil_hari_ini'] }}</span>
            <span class="block mt-2 text-xs font-bold text-stone-800">Ambil Hari Ini</span>
            <span class="block text-[11px] text-stone-500">siap serah terima</span>
            <span class="absolute top-4 right-4 w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
        </a>

        <!-- 3. Sedang Disewa -->
        <a href="{{ route('admin.pesanan.index', ['status' => 'disewa']) }}"
            class="group relative bg-white p-4 sm:p-5 rounded-2xl border border-stone-200/90 shadow-xs hover:border-sky-300 hover:shadow-md transition-all cursor-pointer">
            <div
                class="w-9 h-9 rounded-xl bg-sky-500/10 text-sky-600 ring-1 ring-sky-500/20 flex items-center justify-center group-hover:scale-105 transition">
                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 3l4 8 5-5 5 15H2L8 3z" />
                </svg>
            </div>
            <span
                class="block mt-4 text-3xl sm:text-4xl font-black text-[#14201a] font-urbanist tracking-tight leading-none">{{ $statistik['pesanan_disewa'] }}</span>
            <span class="block mt-2 text-xs font-bold text-stone-800">Sedang Disewa</span>
            <span class="block text-[11px] text-stone-500">di tangan penyewa</span>
            <span class="absolute top-4 right-4 w-1.5 h-1.5 rounded-full bg-sky-500"></span>
        </a>

        <!-- 4. Terlambat -->
        <a href="{{ route('admin.pesanan.index', ['status' => 'terlambat']) }}"
            class="group relative p-4 sm:p-5 rounded-2xl border {{ $statistik['pesanan_terlambat'] > 0 ? 'border-rose-300 bg-rose-50/40' : 'border-stone-200/90 bg-white' }} shadow-xs hover:border-rose-400 hover:shadow-md transition-all cursor-pointer">
            <div
                class="w-9 h-9 rounded-xl bg-rose-500/10 text-rose-600 ring-1 ring-rose-500/20 flex items-center justify-center group-hover:scale-105 transition">
                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <span
                class="block mt-4 text-3xl sm:text-4xl font-black {{ $statistik['pesanan_terlambat'] > 0 ? 'text-rose-600' : 'text-[#14201a]' }} font-urbanist tracking-tight leading-none">{{ $statistik['pesanan_terlambat'] }}</span>
            <span class="block mt-2 text-xs font-bold text-stone-800">Terlambat</span>
            <span class="block text-[11px] text-stone-500">lewat jam kembali</span>
            <span class="absolute top-4 right-4 w-1.5 h-1.5 rounded-full bg-rose-500"></span>
        </a>

        <!-- 5. Denda Tertunggak -->
        <a href="{{ route('admin.denda.index') }}"
            class="group relative p-4 sm:p-5 rounded-2xl border {{ $statistik['denda_belum_lunas'] > 0 ? 'border-amber-300 bg-amber-50/30' : 'border-stone-200/90 bg-white' }} shadow-xs hover:border-stone-400 hover:shadow-md transition-all col-span-2 lg:col-span-1 cursor-pointer">
            <div
                class="w-9 h-9 rounded-xl bg-purple-500/10 text-purple-700 ring-1 ring-purple-500/20 flex items-center justify-center group-hover:scale-105 transition">
                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div class="mt-4 flex items-baseline gap-1">
                <span class="text-xs font-bold text-stone-400">Rp</span>
                <span
                    class="text-2xl sm:text-3xl font-black {{ $statistik['denda_belum_lunas'] > 0 ? 'text-rose-600' : 'text-[#14201a]' }} font-urbanist tracking-tight leading-none">{{ number_format($statistik['denda_belum_lunas'], 0, ',', '.') }}</span>
            </div>
            <span class="block mt-2 text-xs font-bold text-stone-800">Denda Tertunggak</span>
            @if(($statistik['estimasi_denda_berjalan'] ?? 0) > 0)
                <span class="block text-[11px] text-rose-700 font-semibold leading-snug" title="Rincian denda tertunggak">
                    Rp{{ number_format($statistik['denda_tercatat'], 0, ',', '.') }} tercatat +
                    Rp{{ number_format($statistik['estimasi_denda_berjalan'], 0, ',', '.') }} berjalan
                </span>
            @else
                <span class="block text-[11px] text-stone-500">terlambat/rusak</span>
            @endif
            <span
                class="absolute top-4 right-4 w-1.5 h-1.5 rounded-full {{ $statistik['denda_belum_lunas'] > 0 ? 'bg-rose-500' : 'bg-stone-300' }}"></span>
        </a>
    </div>

    <!-- ========================= TINDAKAN + GRAFIK ========================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6 mb-6 sm:mb-8">

        <!-- Kiri: Perlu Tindakan -->
        <div class="lg:col-span-7">
            <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs h-full">
                <div class="flex items-center justify-between gap-3 px-5 sm:px-6 py-4 border-b border-stone-100">
                    <div>
                        <h2 class="text-sm font-extrabold text-[#14201a]">Perlu Tindakan Operasional</h2>
                        <p class="text-[11px] text-stone-400 mt-0.5">Prioritas hari ini, urut dari yang paling mendesak
                        </p>
                    </div>
                    <span
                        class="shrink-0 px-2.5 py-1 rounded-full text-[11px] font-extrabold {{ $perluPerhatian > 0 ? 'bg-rose-50 text-rose-700 ring-1 ring-rose-200' : 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' }}">
                        {{ $perluPerhatian > 0 ? $perluPerhatian . ' tugas' : 'Beres' }}
                    </span>
                </div>

                <div class="p-5 sm:p-6 space-y-6">
                    <!-- Terlambat -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2 text-xs font-extrabold text-rose-800">
                                <span class="w-1.5 h-4 rounded-full bg-rose-500"></span>
                                Penyewa Terlambat Pengembalian
                                <span
                                    class="px-1.5 py-0.5 rounded-md bg-rose-100 text-rose-700 text-[10px] font-black">{{ $pesananTerlambat->count() }}</span>
                            </div>
                            @if($pesananTerlambat->isNotEmpty())
                                <span class="hidden sm:block text-[10px] text-rose-600">Hubungi penyewa segera</span>
                            @endif
                        </div>

                        <div class="space-y-2.5">
                            @forelse($pesananTerlambat as $pt)
                                <div
                                    class="p-3.5 rounded-xl bg-rose-50/60 border border-rose-200/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs hover:border-rose-300 transition">
                                    <div class="flex items-start gap-3 min-w-0">
                                        <div
                                            class="w-9 h-9 rounded-full bg-rose-200 text-rose-900 font-black flex items-center justify-center shrink-0">
                                            {{ strtoupper(mb_substr($pt->pelanggan?->nama_lengkap ?? '-', 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-bold text-stone-900 truncate">
                                                {{ $pt->pelanggan?->nama_lengkap ?? '-' }}</div>
                                            <span
                                                class="font-mono text-[10px] text-stone-500 block">{{ $pt->no_resi }}</span>
                                            <span class="text-[11px] text-rose-800 block mt-0.5">
                                                Jatuh tempo:
                                                {{ $pt->jatuh_tempo?->format('d/m/Y H:i') ?? $pt->tanggal_selesai->format('d/m/Y') }}
                                                WIB
                                            </span>
                                            @if(isset($pt->kalkulasi_telat))
                                                <div class="flex flex-wrap items-center gap-2 mt-1.5">
                                                    <span
                                                        class="px-1.5 py-0.5 rounded-md text-[9px] font-black uppercase bg-rose-200 text-rose-900">
                                                        Terlambat {{ $pt->kalkulasi_telat['hari'] }} Hari
                                                    </span>
                                                    <span class="font-mono font-black text-rose-700 text-xs">
                                                        Denda Berjalan:
                                                        Rp{{ number_format($pt->kalkulasi_telat['jumlah'], 0, ',', '.') }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1.5 shrink-0 sm:pl-3">
                                        <a href="{{ route('admin.pesanan.pengembalian.create', $pt->id) }}"
                                            class="px-3 py-2 rounded-lg bg-[#5edd5b] hover:bg-[#4ed04b] text-black font-extrabold text-[11px] transition shadow-2xs">
                                            Proses Kembali
                                        </a>
                                        <a href="{{ route('admin.pesanan.show', $pt->id) }}"
                                            class="px-3 py-2 rounded-lg bg-white hover:bg-stone-50 border border-rose-200 text-rose-800 font-bold text-[11px] transition">
                                            Periksa
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div
                                    class="py-6 text-center rounded-xl bg-stone-50 border border-dashed border-stone-200 text-stone-400 text-xs">
                                    Tidak ada pengembalian alat yang terlambat saat ini.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Pengambilan Hari Ini -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2 text-xs font-extrabold text-emerald-800">
                                <span class="w-1.5 h-4 rounded-full bg-emerald-500"></span>
                                Jadwal Pengambilan Hari Ini
                                <span
                                    class="px-1.5 py-0.5 rounded-md bg-emerald-100 text-emerald-700 text-[10px] font-black">{{ $pesananAmbilHariIni->count() }}</span>
                            </div>
                            <span class="hidden sm:block text-[10px] text-stone-400">Siapkan perlengkapan</span>
                        </div>

                        <div class="space-y-2.5">
                            @forelse($pesananAmbilHariIni as $pa)
                                <div
                                    class="p-3.5 rounded-xl bg-emerald-50/40 border border-emerald-200/80 flex items-center justify-between gap-3 text-xs hover:border-emerald-300 transition">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div
                                            class="w-9 h-9 rounded-full bg-emerald-200 text-emerald-900 font-black flex items-center justify-center shrink-0">
                                            {{ strtoupper(mb_substr($pa->pelanggan?->nama_lengkap ?? '-', 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-bold text-stone-900 truncate">
                                                {{ $pa->pelanggan?->nama_lengkap ?? '-' }}</div>
                                            <span
                                                class="font-mono text-[10px] text-stone-500 block">{{ $pa->no_resi }}</span>
                                            <span class="text-[11px] text-stone-500 block mt-0.5">
                                                {{ $pa->detail->sum('jumlah') }} unit alat &bull; Lunas, menunggu serah
                                                terima
                                            </span>
                                        </div>
                                    </div>
                                    <a href="{{ route('admin.pesanan.show', $pa->id) }}"
                                        class="shrink-0 px-3.5 py-2 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black font-extrabold text-[11px] transition shadow-2xs">
                                        Serahkan
                                    </a>
                                </div>
                            @empty
                                <div
                                    class="py-6 text-center rounded-xl bg-stone-50 border border-dashed border-stone-200 text-stone-400 text-xs">
                                    Tidak ada jadwal pengambilan baru hari ini.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kanan: Grafik -->
        <div class="lg:col-span-5">
            <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs h-full flex flex-col">
                <div class="flex items-start justify-between gap-3 px-5 sm:px-6 py-4 border-b border-stone-100">
                    <div>
                        <h2 class="text-sm font-extrabold text-[#14201a]">Tren Penyewaan 7 Hari</h2>
                        <p class="text-[11px] text-stone-400 mt-0.5">Volume transaksi sewa harian berjalan</p>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-[#5edd5b]/20 flex items-center justify-center shrink-0">
                        <svg class="w-[18px] h-[18px] text-emerald-800" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                </div>

                <div class="p-5 sm:p-6 flex-1 flex flex-col">
                    <!-- Mini statistik -->
                    <div class="grid grid-cols-3 gap-2.5 mb-4">
                        <div class="rounded-xl bg-stone-50 border border-stone-100 px-3 py-2.5">
                            <span class="block text-[10px] text-stone-500 font-semibold">Total</span>
                            <span
                                class="block text-lg font-black text-[#14201a] font-urbanist leading-tight">{{ $totalSewa7Hari }}</span>
                        </div>
                        <div class="rounded-xl bg-stone-50 border border-stone-100 px-3 py-2.5">
                            <span class="block text-[10px] text-stone-500 font-semibold">Rata-rata/hari</span>
                            <span
                                class="block text-lg font-black text-[#14201a] font-urbanist leading-tight">{{ $rataSewaHarian }}</span>
                        </div>
                        <div class="rounded-xl bg-[#5edd5b]/15 border border-[#5edd5b]/30 px-3 py-2.5">
                            <span class="block text-[10px] text-emerald-900/70 font-semibold">Puncak</span>
                            <span
                                class="block text-lg font-black text-emerald-900 font-urbanist leading-tight">{{ $puncakSewa }}</span>
                        </div>
                    </div>

                    <div class="relative w-full h-52 sm:h-60 lg:h-auto lg:flex-1 lg:min-h-[13rem]">
                        <canvas id="chartSewa7Hari"></canvas>
                    </div>
                </div>

                <div
                    class="px-5 sm:px-6 py-3.5 border-t border-stone-100 bg-stone-50/50 rounded-b-2xl flex items-center justify-between text-[11px] text-stone-500">
                    <span>Total sewa 7 hari: <strong class="text-stone-800">{{ $totalSewa7Hari }}
                            pesanan</strong></span>
                    <a href="{{ route('admin.laporan.index', ['jenis' => 'penyewaan']) }}"
                        class="text-[#0e1b15] font-bold hover:underline">
                        Lihat Laporan Lengkap &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================= PESANAN TERBARU ========================= -->
    <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs overflow-hidden">
        <div class="px-5 sm:px-6 py-4 border-b border-stone-200/80 flex items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-extrabold text-[#14201a]">Aktivitas Pesanan Terbaru</h2>
                <p class="text-xs text-stone-500 mt-0.5">6 transaksi penyewaan terakhir yang dicatat ke sistem</p>
            </div>
            <a href="{{ route('admin.pesanan.index') }}"
                class="shrink-0 px-3.5 py-2 rounded-xl bg-stone-100 hover:bg-[#5edd5b] text-[#0e1b15] text-xs font-bold flex items-center gap-1.5 transition">
                <span>Lihat Semua</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-left text-xs text-stone-700">
                <thead
                    class="bg-stone-50/80 text-stone-500 uppercase text-[10px] font-bold border-b border-stone-200 tracking-wider">
                    <tr>
                        <th class="py-3 px-5 sm:px-6">Pelanggan</th>
                        <th class="py-3 px-4">Jadwal Sewa</th>
                        <th class="py-3 px-4">Total Biaya</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-5 sm:px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($pesananTerbaru as $p)
                        <tr class="group hover:bg-stone-50/70 transition">
                            <td class="py-3.5 px-5 sm:px-6">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 rounded-full bg-[#0e1b15] text-[#5edd5b] font-black flex items-center justify-center shrink-0">
                                        {{ strtoupper(mb_substr($p->pelanggan?->nama_lengkap ?? '-', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-semibold text-[#14201a] truncate">
                                            {{ $p->pelanggan?->nama_lengkap ?? '-' }}
                                            @if($p->pelanggan?->is_walk_in)
                                                <span
                                                    class="ml-1 px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-[#5edd5b]/20 text-emerald-900 border border-[#5edd5b]/40 align-middle">Walk-in</span>
                                            @endif
                                        </div>
                                        <span class="block font-mono text-[10px] text-stone-400 mt-0.5">
                                            {{ $p->no_resi }}@if($p->pelanggan?->no_hp) &bull;
                                            {{ $p->pelanggan->no_hp }}@endif
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-medium text-stone-800">{{ $p->tanggal_mulai?->format('d/m/Y') }} &ndash;
                                    {{ $p->tanggal_selesai?->format('d/m/Y') }}</div>
                                <span class="text-[10px] text-stone-400 font-medium">{{ $p->durasi_hari }} hari sewa</span>
                            </td>
                            <td class="py-3.5 px-4 font-extrabold text-[#14201a] text-sm font-mono whitespace-nowrap">
                                Rp{{ number_format($p->total_sewa, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4">
                                <x-badge-status :status="$p->status" />
                            </td>
                            <td class="py-3.5 px-5 sm:px-6 text-right">
                                <a href="{{ route('admin.pesanan.show', $p->id) }}"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-stone-100 hover:bg-[#5edd5b] hover:text-black text-stone-700 font-bold text-[11px] transition">
                                    <span>Detail</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-stone-400">
                                Belum ada riwayat pesanan yang tercatat di database.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Chart.js: Tren Sewa 7 Hari -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const canvas = document.getElementById('chartSewa7Hari');
            if (!canvas) return;

            const c2d = canvas.getContext('2d');
            const gradient = c2d.createLinearGradient(0, 0, 0, canvas.parentElement.clientHeight || 220);
            gradient.addColorStop(0, 'rgba(94, 221, 91, 0.40)');
            gradient.addColorStop(1, 'rgba(94, 221, 91, 0)');

            new Chart(canvas, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        label: 'Jumlah Pesanan',
                        data: {!! json_encode($chartData) !!},
                        borderColor: '#0e1b15',
                        backgroundColor: gradient,
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#5edd5b',
                        pointBorderColor: '#0e1b15',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 7,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0e1b15',
                            titleFont: { size: 11, weight: 'bold' },
                            bodyFont: { size: 11 },
                            padding: 10,
                            cornerRadius: 10,
                            displayColors: false,
                            callbacks: {
                                label: function (context) {
                                    return context.parsed.y + ' Pesanan Sewa';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            border: { display: false },
                            ticks: { precision: 0, font: { size: 10 }, color: '#a8a29e' },
                            grid: { color: '#f1f5f9' }
                        },
                        x: {
                            border: { display: false },
                            ticks: { font: { size: 10 }, color: '#a8a29e' },
                            grid: { display: false }
                        }
                    }
                }
            });
        });
    </script>
</x-layouts.admin>