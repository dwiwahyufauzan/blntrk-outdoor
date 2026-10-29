<x-layouts.admin>
    <x-slot:title>Daftar Pesanan Rental - BLNTRK Outdoor</x-slot:title>

    <!-- Header & Action -->
    <div class="mb-7 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="text-[10px] font-bold uppercase tracking-widest text-[#4e7a5a] block mb-1">Transaksi Rental</span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#14201a] tracking-tight bl-d">Daftar Pesanan Rental</h1>
            <p class="text-xs text-stone-500 mt-0.5">Kelola reservasi daring, pembayaran di kasir, jaminan identitas, dan serah terima alat</p>
        </div>

        <a href="{{ route('admin.pesanan.create') }}"
           class="px-4 py-2.5 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-extrabold shadow-sm transition inline-flex items-center gap-2 w-fit cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            <span>Buat Pesanan Walk-in</span>
        </a>
    </div>

    <!-- Alert Status -->
    @if(session('status'))
        <div class="mb-6 p-4 rounded-2xl bg-[#0e1b15] text-[#5edd5b] border border-[#1e3a2c] text-xs font-bold flex items-center gap-3">
            <svg class="w-5 h-5 text-[#5edd5b] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- Tab Filter Status dengan Counter Badge -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 mb-4 scrollbar-none">
        <a href="{{ route('admin.pesanan.index', array_filter(['q' => request('q')])) }}"
           class="px-3.5 py-2 rounded-xl text-xs font-bold shrink-0 transition flex items-center gap-2 {{ empty(request('status')) ? 'bg-[#0e1b15] text-[#5edd5b] shadow-xs' : 'bg-white text-stone-600 hover:bg-stone-50 border border-stone-200/90' }}">
            <span>Semua</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ empty(request('status')) ? 'bg-[#5edd5b]/20 text-[#5edd5b]' : 'bg-stone-100 text-stone-500' }}">{{ $totalCount }}</span>
        </a>
        @foreach($statuses as $st)
            @php
                $isActive = request('status') === $st->value;
                $count = $statusCounts[$st->value] ?? 0;
            @endphp
            <a href="{{ route('admin.pesanan.index', array_filter(['status' => $st->value, 'q' => request('q')])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold shrink-0 transition flex items-center gap-2 {{ $isActive ? 'bg-[#0e1b15] text-[#5edd5b] shadow-xs' : 'bg-white text-stone-600 hover:bg-stone-50 border border-stone-200/90' }}">
                <span>{{ $st->label() }}</span>
                <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $isActive ? 'bg-[#5edd5b]/20 text-[#5edd5b]' : 'bg-stone-100 text-stone-500' }}">{{ $count }}</span>
            </a>
        @endforeach
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-stone-200/90 shadow-xs mb-6">
        <form method="GET" action="{{ route('admin.pesanan.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <div class="sm:col-span-5">
                <label for="q" class="block text-[11px] font-bold text-[#14201a] uppercase tracking-wider mb-1.5">Pencarian Resi / Nama</label>
                <div class="relative">
                    <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Nomor resi (BLN-...), nama penyewa, nomor HP..."
                        class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs text-[#14201a] pl-9">
                    <svg class="w-4 h-4 text-stone-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <div class="sm:col-span-2">
                <label for="dari" class="block text-[11px] font-bold text-[#14201a] uppercase tracking-wider mb-1.5">Dari Tgl</label>
                <input type="date" id="dari" name="dari" value="{{ request('dari') }}"
                    class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs text-[#14201a]">
            </div>

            <div class="sm:col-span-2">
                <label for="sampai" class="block text-[11px] font-bold text-[#14201a] uppercase tracking-wider mb-1.5">Sampai Tgl</label>
                <input type="date" id="sampai" name="sampai" value="{{ request('sampai') }}"
                    class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs text-[#14201a]">
            </div>

            <div class="sm:col-span-3 flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-extrabold transition shadow-sm cursor-pointer inline-flex items-center justify-center gap-1.5">
                    <span>Filter & Cari</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
                @if(request()->hasAny(['q', 'status', 'dari', 'sampai']))
                    <a href="{{ route('admin.pesanan.index') }}" class="px-3.5 py-2.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-600 text-xs font-bold transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Pesanan -->
    <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-stone-700">
                <thead class="bg-stone-50/80 text-stone-500 uppercase text-[10px] font-bold border-b border-stone-200 tracking-wider">
                    <tr>
                        <th class="py-3 px-5 w-12 text-center">No</th>
                        <th class="py-3 px-4">No. Resi</th>
                        <th class="py-3 px-4">Penyewa</th>
                        <th class="py-3 px-4">Jadwal Sewa</th>
                        <th class="py-3 px-4 text-center">Durasi</th>
                        <th class="py-3 px-4 text-right">Total Biaya</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($pesananList as $index => $p)
                        <tr class="hover:bg-stone-50/60 transition">
                            <td class="py-3.5 px-5 text-center font-bold text-stone-400">
                                {{ $pesananList->firstItem() + $index }}
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold text-[#14201a]">
                                <a href="{{ route('admin.pesanan.show', $p->id) }}" class="hover:underline text-[#0e1b15]">
                                    {{ $p->no_resi }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-[#14201a] bl-d">{{ $p->pelanggan->nama_lengkap }}</div>
                                <div class="text-[10px] text-stone-400 font-mono">{{ $p->pelanggan->no_hp }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-stone-600">
                                <div>{{ $p->tanggal_mulai->format('d/m/Y') }} &ndash; {{ $p->tanggal_selesai->format('d/m/Y') }}</div>
                                <span class="text-[10px] text-stone-400">Tempo: {{ $p->jatuh_tempo?->format('H:i') ?? '12:00' }} WIB</span>
                            </td>
                            <td class="py-3.5 px-4 text-center font-bold text-stone-700">
                                {{ $p->durasi_hari }} Hari
                            </td>
                            <td class="py-3.5 px-4 text-right font-bold text-[#14201a] font-mono">
                                Rp{{ number_format($p->total_sewa, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @php
                                    $badge = match($p->status->value) {
                                        'menunggu' => 'bg-amber-100 text-amber-900 border border-amber-300 font-extrabold',
                                        'dibayar' => 'bg-[#5edd5b]/20 text-stone-900 border border-[#5edd5b]/40 font-extrabold',
                                        'disewa' => 'bg-sky-100 text-sky-900 border border-sky-300 font-extrabold',
                                        'dikembalikan', 'selesai' => 'bg-emerald-100 text-emerald-900 border border-emerald-300 font-bold',
                                        'terlambat' => 'bg-rose-100 text-rose-900 border border-rose-300 font-extrabold animate-pulse',
                                        'dibatalkan' => 'bg-stone-100 text-stone-600 border border-stone-200 font-bold',
                                        default => 'bg-stone-100 text-stone-600 font-bold'
                                    };
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] uppercase tracking-wider {{ $badge }}">
                                    {{ $p->status->label() }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                <a href="{{ route('admin.pesanan.show', $p->id) }}"
                                   class="px-2.5 py-1.5 rounded-xl bg-stone-100 hover:bg-[#0e1b15] hover:text-[#5edd5b] text-stone-700 font-bold text-[11px] transition inline-flex items-center gap-1 shadow-2xs">
                                    <span>Detail</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-stone-400">
                                <svg class="w-8 h-8 mx-auto mb-2 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Tidak ada data pesanan yang cocok dengan kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pesananList->hasPages())
            <div class="p-4 border-t border-stone-200/80 bg-stone-50/40">
                {{ $pesananList->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
