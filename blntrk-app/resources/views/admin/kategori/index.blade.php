<x-layouts.admin>
    <x-slot:title>Kelola Kategori Alat Outdoor</x-slot:title>

    <div class="mb-7 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#0e1b15] block mb-1">Katalog & Inventaris</span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#14201a] tracking-tight">Kategori Alat Outdoor</h1>
            <p class="text-xs text-stone-500 mt-0.5">Klasifikasi kelompok perlengkapan camping & hiking di toko fisik BLNTRK Outdoor</p>
        </div>

        <a href="{{ route('admin.kategori.create') }}" class="px-4 py-2.5 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-extrabold shadow-sm transition inline-flex items-center gap-2 w-fit cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Kategori</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-stone-700">
                <thead class="bg-stone-50/80 text-stone-500 uppercase text-[10px] font-bold border-b border-stone-200 tracking-wider">
                    <tr>
                        <th class="py-3 px-5 w-14 text-center">No</th>
                        <th class="py-3 px-4">Nama Kategori</th>
                        <th class="py-3 px-4">Slug URL</th>
                        <th class="py-3 px-4">Deskripsi</th>
                        <th class="py-3 px-4 text-center">Jumlah Alat</th>
                        <th class="py-3 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($kategoriList as $index => $kat)
                        <tr class="hover:bg-stone-50/60 transition">
                            <td class="py-3.5 px-5 text-center font-bold text-stone-400">
                                {{ $kategoriList->firstItem() + $index }}
                            </td>
                            <td class="py-3.5 px-4 font-bold text-[#14201a] text-sm">
                                {{ $kat->nama }}
                            </td>
                            <td class="py-3.5 px-4 font-mono text-stone-500 text-[11px]">
                                {{ $kat->slug }}
                            </td>
                            <td class="py-3.5 px-4 text-stone-600 max-w-xs truncate">
                                {{ $kat->deskripsi ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold {{ $kat->alat_count > 0 ? 'bg-[#5edd5b]/20 text-emerald-900 border border-[#5edd5b]/40' : 'bg-stone-100 text-stone-500' }}">
                                    {{ $kat->alat_count }} unit jenis
                                </span>
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.kategori.edit', $kat->id) }}" class="px-3 py-1.5 rounded-xl bg-stone-100 hover:bg-[#5edd5b] hover:text-black text-stone-700 font-bold text-[11px] transition">
                                        Edit
                                    </a>

                                    <form method="POST" action="{{ route('admin.kategori.destroy', $kat->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori {{ $kat->nama }}?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-[11px] border border-rose-200/80 transition cursor-pointer">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-stone-400">
                                <div class="w-10 h-10 rounded-2xl bg-stone-100 text-stone-400 mx-auto flex items-center justify-center mb-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                                </div>
                                Belum ada data kategori alat outdoor.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($kategoriList->hasPages())
            <div class="p-4 border-t border-stone-200/80 bg-stone-50/40">
                {{ $kategoriList->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>

