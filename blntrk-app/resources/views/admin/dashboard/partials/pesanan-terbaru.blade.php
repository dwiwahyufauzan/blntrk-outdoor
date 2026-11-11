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
