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
