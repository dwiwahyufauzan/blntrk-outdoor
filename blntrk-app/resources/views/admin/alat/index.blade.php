<x-layouts.admin>
    <x-slot:title>Inventaris Alat Outdoor</x-slot:title>

    <div class="mb-7 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#0e1b15] block mb-1">Inventaris & Stok Fisik</span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#14201a] tracking-tight">Data Alat Outdoor</h1>
            <p class="text-xs text-stone-500 mt-0.5">Kelola perlengkapan rental, tarif harian 24 jam, dan kesiapan stok di toko fisik</p>
        </div>

        <a href="{{ route('admin.alat.create') }}" class="px-4 py-2.5 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-extrabold shadow-sm transition inline-flex items-center gap-2 w-fit cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Alat Baru</span>
        </a>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white p-5 rounded-2xl border border-stone-200/90 shadow-xs mb-6">
        <form method="GET" action="{{ route('admin.alat.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3.5">
            <div class="sm:col-span-2">
                <label for="q" class="block text-[11px] font-bold text-[#14201a] uppercase tracking-wider mb-1.5">Cari Nama atau Kode Alat</label>
                <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Ketik nama atau kode alat (misal: TND-001)..."
                    class="w-full text-xs rounded-xl border-stone-300 focus:border-[#5edd5b] focus:ring-[#5edd5b] shadow-xs text-[#14201a]">
            </div>

            <div>
                <label for="kategori_id" class="block text-[11px] font-bold text-[#14201a] uppercase tracking-wider mb-1.5">Kategori</label>
                <select id="kategori_id" name="kategori_id" class="w-full text-xs rounded-xl border-stone-300 focus:border-[#5edd5b] focus:ring-[#5edd5b] shadow-xs text-[#14201a]">
                    <option value="">-- Semua Kategori --</option>
                    @foreach($kategoriList as $k)
                        <option value="{{ $k->id }}" {{ request('kategori_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-[#0e1b15] hover:bg-[#14251e] text-white text-xs font-extrabold transition shadow-sm cursor-pointer inline-flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-[#5edd5b]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>Filter</span>
                </button>
                @if(request()->hasAny(['q', 'kategori_id', 'status']))
                    <a href="{{ route('admin.alat.index') }}" class="px-3.5 py-2.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-600 text-xs font-bold transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Alat -->
    <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-stone-700">
                <thead class="bg-stone-50/80 text-stone-500 uppercase text-[10px] font-bold border-b border-stone-200 tracking-wider">
                    <tr>
                        <th class="py-3 px-5 w-16">Foto</th>
                        <th class="py-3 px-4">Kode & Nama Alat</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4">Tarif Sewa</th>
                        <th class="py-3 px-4 text-center">Stok (Total / Servis / Siap)</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($alatList as $alat)
                        <tr class="hover:bg-stone-50/60 transition">
                            <td class="py-3.5 px-5">
                                <div class="w-12 h-12 rounded-xl bg-stone-100 border border-stone-200 overflow-hidden flex items-center justify-center">
                                    @if($alat->foto)
                                        <img src="{{ asset('storage/' . $alat->foto) }}" alt="{{ $alat->nama }}" class="w-full h-full object-cover">
                                    @else
                                        <svg class="w-5 h-5 text-stone-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3l4 8 5-5 5 15H2L8 3z"/></svg>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-mono text-[10px] font-bold text-emerald-800 block">
                                    {{ $alat->kode_alat }}
                                </span>
                                <a href="{{ route('admin.alat.show', $alat->id) }}" class="font-bold text-[#14201a] hover:text-emerald-700 text-xs transition">
                                    {{ $alat->nama }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-stone-100 text-stone-700 border border-stone-200">
                                    {{ $alat->kategori?->nama ?? 'Umum' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-extrabold text-[#14201a] text-sm">
                                Rp {{ number_format($alat->harga_per_hari, 0, ',', '.') }}<span class="text-[10px] font-normal text-stone-400">/hari</span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5 font-mono font-bold text-xs">
                                    <span class="text-stone-700" title="Total Fisik">{{ $alat->stok_total }}</span>
                                    <span class="text-stone-300">/</span>
                                    <span class="{{ $alat->stok_perawatan > 0 ? 'text-amber-600 font-extrabold' : 'text-stone-400' }}" title="Dalam Perawatan">
                                        {{ $alat->stok_perawatan }}
                                    </span>
                                    <span class="text-stone-300">/</span>
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-extrabold {{ $alat->stok_tersedia > 0 ? 'text-emerald-900 bg-emerald-100 border border-emerald-200' : 'text-rose-700 bg-rose-50 border border-rose-200' }}" title="Siap Disewa">
                                        {{ $alat->stok_tersedia }} unit
                                    </span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <form method="POST" action="{{ route('admin.alat.toggle', $alat->id) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" title="Klik untuk mengubah status katalog"
                                        class="px-2.5 py-0.5 rounded-full text-[10px] font-bold cursor-pointer transition {{ $alat->is_aktif ? 'bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100' : 'bg-stone-200 text-stone-600 hover:bg-stone-300' }}">
                                        {{ $alat->is_aktif ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </form>
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('admin.alat.show', $alat->id) }}" class="px-2.5 py-1.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold text-[11px] transition">
                                        Detail
                                    </a>
                                    <a href="{{ route('admin.alat.edit', $alat->id) }}" class="px-2.5 py-1.5 rounded-xl bg-stone-100 hover:bg-[#5edd5b] hover:text-black text-stone-700 font-bold text-[11px] transition">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.alat.destroy', $alat->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus alat {{ $alat->nama }}?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-[11px] border border-rose-200/80 transition cursor-pointer">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-stone-400">
                                <div class="w-10 h-10 rounded-2xl bg-stone-100 text-stone-400 mx-auto flex items-center justify-center mb-2">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3l4 8 5-5 5 15H2L8 3z"/></svg>
                                </div>
                                Tidak ada data alat outdoor yang cocok dengan kriteria filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($alatList->hasPages())
            <div class="p-4 border-t border-stone-200/80 bg-stone-50/40">
                {{ $alatList->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>

