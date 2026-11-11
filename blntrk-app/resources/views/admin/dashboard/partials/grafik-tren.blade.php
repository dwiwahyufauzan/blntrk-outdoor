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
