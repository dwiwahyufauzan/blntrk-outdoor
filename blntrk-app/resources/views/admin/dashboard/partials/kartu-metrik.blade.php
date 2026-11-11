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
