<x-layouts.admin>
    <x-slot:title>Kelola Kategori Alat Outdoor</x-slot:title>

    <div class="mb-7 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="text-[10px] font-bold uppercase tracking-widest text-[#4e7a5a] block mb-1">Katalog & Inventaris</span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#14201a] tracking-tight bl-d">Kategori Alat Outdoor</h1>
            <p class="text-xs text-stone-500 mt-0.5">Klasifikasi kelompok perlengkapan camping & hiking di toko fisik BLNTRK Outdoor</p>
        </div>

        <a href="{{ route('admin.kategori.create') }}" class="px-4 py-2.5 rounded-xl bg-[#0e1b15] hover:bg-[#1e3a2c] text-[#f6c453] text-xs font-bold shadow-sm transition inline-flex items-center gap-2 w-fit cursor-pointer">
            <span>➕</span>
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
                            <td class="py-3.5 px-4 font-bold text-[#14201a] text-sm bl-d">
                                {{ $kat->nama }}
                            </td>
                            <td class="py-3.5 px-4 font-mono text-stone-500 text-[11px]">
                                {{ $kat->slug }}
                            </td>
                            <td class="py-3.5 px-4 text-stone-600 max-w-xs truncate">
                                {{ $kat->deskripsi ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $kat->alat_count > 0 ? 'bg-[#1e3a2c]/10 text-[#1e3a2c] border border-[#1e3a2c]/20' : 'bg-stone-100 text-stone-500' }}">
                                    {{ $kat->alat_count }} unit jenis
                                </span>
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.kategori.edit', $kat->id) }}" class="px-3 py-1.5 rounded-xl bg-stone-100 hover:bg-[#0e1b15] hover:text-[#f6c453] text-stone-700 font-bold text-[11px] transition">
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
                                <span class="text-2xl block mb-1">📂</span>
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

