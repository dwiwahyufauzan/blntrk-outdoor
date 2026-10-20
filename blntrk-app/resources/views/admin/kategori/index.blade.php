<x-layouts.admin>
    <x-slot:title>Kelola Kategori Alat Outdoor</x-slot:title>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-stone-900 tracking-tight">Kategori Alat Outdoor</h1>
            <p class="text-xs text-stone-500">Klasifikasi kelompok perlengkapan hiking & camping BLNTRK Outdoor</p>
        </div>

        <a href="{{ route('admin.kategori.create') }}" class="px-4 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-sm transition inline-flex items-center gap-1.5 w-fit">
            <span>➕</span>
            <span>Tambah Kategori</span>
        </a>
    </div>

    <div class="bg-white rounded-xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-stone-700">
                <thead class="bg-stone-50 text-stone-500 uppercase text-[10px] font-bold border-b border-stone-200 tracking-wider">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Kategori</th>
                        <th class="py-3 px-4">Slug URL</th>
                        <th class="py-3 px-4">Deskripsi</th>
                        <th class="py-3 px-4 text-center">Jumlah Alat</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($kategoriList as $index => $kat)
                        <tr class="hover:bg-stone-50/60 transition">
                            <td class="py-3 px-4 text-center font-medium text-stone-500">
                                {{ $kategoriList->firstItem() + $index }}
                            </td>
                            <td class="py-3 px-4 font-bold text-stone-900">
                                {{ $kat->nama }}
                            </td>
                            <td class="py-3 px-4 font-mono text-stone-500">
                                {{ $kat->slug }}
                            </td>
                            <td class="py-3 px-4 text-stone-600 max-w-xs truncate">
                                {{ $kat->deskripsi ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $kat->alat_count > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-stone-100 text-stone-500' }}">
                                    {{ $kat->alat_count }} unit jenis
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.kategori.edit', $kat->id) }}" class="px-2.5 py-1 rounded bg-stone-100 hover:bg-stone-200 text-stone-800 font-semibold text-[11px] transition">
                                        Edit
                                    </a>

                                    <form method="POST" action="{{ route('admin.kategori.destroy', $kat->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori {{ $kat->nama }}?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 rounded bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold text-[11px] border border-rose-200 transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-stone-400">
                                Belum ada data kategori alat outdoor.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($kategoriList->hasPages())
            <div class="p-4 border-t border-stone-200">
                {{ $kategoriList->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
