<x-layouts.admin>
    <x-slot:title>Daftar Denda & Ganti Rugi - BLNTRK Outdoor</x-slot:title>

    <!-- Header -->
    <div class="mb-7 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="text-[10px] font-bold uppercase tracking-widest text-[#4e7a5a] block mb-1">Manajemen Denda</span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#14201a] tracking-tight bl-d">Denda & Ganti Rugi</h1>
            <p class="text-xs text-stone-500 mt-0.5">Kelola denda keterlambatan pengembalian dan biaya ganti rugi kerusakan alat</p>
        </div>
    </div>

    <!-- Alert Notifikasi Status -->
    @if(session('status'))
        <div class="mb-6 p-4 rounded-2xl bg-[#0e1b15] text-[#5edd5b] border border-[#1e3a2c] text-xs font-bold flex items-center gap-3">
            <svg class="w-5 h-5 text-[#5edd5b] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- Kartu Statistik Ringkasan Denda -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="bg-white p-5 rounded-2xl border border-stone-200/90 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-rose-600 block mb-1">Total Denda Belum Lunas</span>
                <div class="text-xl sm:text-2xl font-black font-mono text-[#0e1b15]">
                    Rp{{ number_format($totalBelumLunas, 0, ',', '.') }}
                </div>
                <span class="text-[11px] text-stone-500 font-medium mt-0.5 block">{{ $countBelumLunas }} Tagihan Denda Tertunggak</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-stone-200/90 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 block mb-1">Total Denda Telah Dilunasi</span>
                <div class="text-xl sm:text-2xl font-black font-mono text-[#0e1b15]">
                    Rp{{ number_format($totalLunas, 0, ',', '.') }}
                </div>
                <span class="text-[11px] text-stone-500 font-medium mt-0.5 block">{{ $countLunas }} Tagihan Berhasil Dilunasi</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>

    <!-- Tab Filter Status Bayar -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 mb-4 scrollbar-none">
        <a href="{{ route('admin.denda.index', array_filter(['q' => request('q'), 'jenis' => request('jenis')])) }}"
           class="px-3.5 py-2 rounded-xl text-xs font-bold shrink-0 transition flex items-center gap-2 {{ empty(request('status_bayar')) ? 'bg-[#0e1b15] text-[#5edd5b] shadow-xs' : 'bg-white text-stone-600 hover:bg-stone-50 border border-stone-200/90' }}">
            <span>Semua</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ empty(request('status_bayar')) ? 'bg-[#5edd5b]/20 text-[#5edd5b]' : 'bg-stone-100 text-stone-500' }}">{{ $countSemua }}</span>
        </a>

        <a href="{{ route('admin.denda.index', array_filter(['status_bayar' => 'belum_lunas', 'q' => request('q'), 'jenis' => request('jenis')])) }}"
           class="px-3.5 py-2 rounded-xl text-xs font-bold shrink-0 transition flex items-center gap-2 {{ request('status_bayar') === 'belum_lunas' ? 'bg-[#0e1b15] text-[#5edd5b] shadow-xs' : 'bg-white text-stone-600 hover:bg-stone-50 border border-stone-200/90' }}">
            <span>Belum Lunas</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ request('status_bayar') === 'belum_lunas' ? 'bg-[#5edd5b]/20 text-[#5edd5b]' : 'bg-stone-100 text-stone-500' }}">{{ $countBelumLunas }}</span>
        </a>

        <a href="{{ route('admin.denda.index', array_filter(['status_bayar' => 'lunas', 'q' => request('q'), 'jenis' => request('jenis')])) }}"
           class="px-3.5 py-2 rounded-xl text-xs font-bold shrink-0 transition flex items-center gap-2 {{ request('status_bayar') === 'lunas' ? 'bg-[#0e1b15] text-[#5edd5b] shadow-xs' : 'bg-white text-stone-600 hover:bg-stone-50 border border-stone-200/90' }}">
            <span>Sudah Lunas</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ request('status_bayar') === 'lunas' ? 'bg-[#5edd5b]/20 text-[#5edd5b]' : 'bg-stone-100 text-stone-500' }}">{{ $countLunas }}</span>
        </a>
    </div>

    <!-- Filter Pencarian -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-stone-200/90 shadow-xs mb-6">
        <form method="GET" action="{{ route('admin.denda.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            @if(request('status_bayar'))
                <input type="hidden" name="status_bayar" value="{{ request('status_bayar') }}">
            @endif

            <div class="sm:col-span-2">
                <label for="q" class="block text-[11px] font-bold text-[#14201a] uppercase tracking-wider mb-1.5">Pencarian Resi, Pelanggan, atau Keterangan</label>
                <div class="relative">
                    <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Cari nomor resi atau nama penyewa..."
                        class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs text-[#14201a] pl-9">
                    <svg class="w-4 h-4 text-stone-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <div>
                <label for="jenis" class="block text-[11px] font-bold text-[#14201a] uppercase tracking-wider mb-1.5">Jenis Denda</label>
                <select id="jenis" name="jenis" class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs text-[#14201a]">
                    <option value="">-- Semua Jenis --</option>
                    <option value="keterlambatan" {{ request('jenis') === 'keterlambatan' ? 'selected' : '' }}>Denda Keterlambatan</option>
                    <option value="kerusakan" {{ request('jenis') === 'kerusakan' ? 'selected' : '' }}>Biaya Kerusakan / Kehilangan</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-extrabold transition shadow-sm cursor-pointer inline-flex items-center justify-center gap-1.5">
                    <span>Filter</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
                @if(request()->hasAny(['q', 'status_bayar', 'jenis']))
                    <a href="{{ route('admin.denda.index') }}" class="px-3.5 py-2.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-600 text-xs font-bold transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Daftar Denda -->
    <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-stone-700">
                <thead class="bg-stone-50/80 text-stone-500 uppercase text-[10px] font-bold border-b border-stone-200 tracking-wider">
                    <tr>
                        <th class="py-3 px-5 w-12 text-center">No</th>
                        <th class="py-3 px-4">No. Resi</th>
                        <th class="py-3 px-4">Penyewa</th>
                        <th class="py-3 px-4">Jenis & Keterangan</th>
                        <th class="py-3 px-4 text-right">Nominal</th>
                        <th class="py-3 px-4 text-center">Status Bayar</th>
                        <th class="py-3 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($dendaList as $index => $d)
                        <tr class="hover:bg-stone-50/60 transition">
                            <td class="py-3.5 px-5 text-center font-bold text-stone-400">
                                {{ $dendaList->firstItem() + $index }}
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold text-[#14201a]">
                                <a href="{{ route('admin.pesanan.show', $d->pesanan_id) }}" class="hover:underline text-[#0e1b15]">
                                    {{ $d->pesanan->no_resi }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-[#14201a] bl-d">{{ $d->pesanan->pelanggan->nama_lengkap }}</div>
                                <div class="text-[10px] text-stone-400 font-mono">{{ $d->pesanan->pelanggan->no_hp }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold {{ $d->jenis->value === 'keterlambatan' ? 'bg-amber-100 text-amber-900' : 'bg-rose-100 text-rose-900' }}">
                                        {{ $d->jenis->label() }}
                                    </span>
                                </div>
                                <span class="text-xs text-stone-600 block">{{ $d->keterangan }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-bold text-base text-[#14201a] font-mono">
                                Rp{{ number_format($d->jumlah, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($d->status_bayar === 'lunas')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        Lunas
                                    </span>
                                    <div class="text-[9px] text-stone-400 font-mono mt-0.5">
                                        {{ $d->dilunasi_pada?->format('d/m/Y H:i') }}
                                    </div>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-rose-100 text-rose-900 border border-rose-300">
                                        Belum Lunas
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($d->status_bayar !== 'lunas')
                                        <form method="POST" action="{{ route('admin.denda.lunas', $d->id) }}" onsubmit="return confirm('Tandai lunas denda Rp{{ number_format($d->jumlah, 0, ',', '.') }} untuk {{ $d->pesanan->no_resi }}?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-extrabold transition shadow-2xs cursor-pointer inline-flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                <span>Tandai Lunas</span>
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('admin.pesanan.show', $d->pesanan_id) }}"
                                       class="px-2.5 py-1.5 rounded-xl bg-stone-100 hover:bg-[#0e1b15] hover:text-[#5edd5b] text-stone-700 font-bold text-[11px] transition inline-flex items-center gap-1 shadow-2xs">
                                        <span>Resi</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-stone-400">
                                <svg class="w-8 h-8 mx-auto mb-2 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Tidak ada data denda atau ganti rugi yang cocok.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($dendaList->hasPages())
            <div class="p-4 border-t border-stone-200/80 bg-stone-50/40">
                {{ $dendaList->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
