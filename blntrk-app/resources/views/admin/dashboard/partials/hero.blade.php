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
