<x-layouts.admin>
    <x-slot:title>Kelola Paket Bundling - BLNTRK Outdoor</x-slot:title>

    <div class="mb-7 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#4e7a5a] block mb-1">Katalog & Promosi Sewa</span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#14201a] tracking-tight bl-d">Paket Bundling Hemat</h1>
            <p class="text-xs text-stone-500 mt-0.5">Kelola paket set perlengkapan outdoor siap pakai (Paket 1, Paket 2, Paket 3) dengan tarif hemat</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('paket-bundling.index') }}" target="_blank"
               class="px-3.5 py-2.5 rounded-xl bg-white border border-stone-200 text-stone-700 hover:bg-stone-50 text-xs font-bold shadow-2xs transition inline-flex items-center gap-1.5">
                <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>Lihat di Web</span>
            </a>
            <a href="{{ route('admin.paket-bundling.create') }}"
               class="px-4 py-2.5 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-extrabold shadow-sm transition inline-flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Paket Bundling</span>
            </a>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-stone-200/90 shadow-xs mb-6">
        <form method="GET" action="{{ route('admin.paket-bundling.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama paket, kode paket, atau kapasitas..."
                       class="w-full pl-10 pr-4 py-2 text-xs rounded-xl border-stone-200 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-2xs">
            </div>

            @if($q)
                <a href="{{ route('admin.paket-bundling.index') }}"
                   class="px-3.5 py-2 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold transition flex items-center justify-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Tabel Daftar Paket Bundling -->
    <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-stone-700">
                <thead class="bg-stone-50/80 text-stone-500 uppercase text-[10px] font-bold border-b border-stone-200 tracking-wider">
                    <tr>
                        <th class="py-3 px-5">Paket</th>
                        <th class="py-3 px-4">Nama & Kapasitas</th>
                        <th class="py-3 px-4">Komposisi Alat Terkandung</th>
                        <th class="py-3 px-4 text-right">Tarif Paket / Hari</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($paketList as $p)
                        <tr class="hover:bg-stone-50/60 transition">
                            <td class="py-3.5 px-5 font-mono">
                                <span class="px-2.5 py-1 rounded-lg bg-[#0e1b15] text-[#5edd5b] text-[11px] font-black tracking-wider">
                                    {{ $p->label_paket }}
                                </span>
                                <div class="text-[10px] text-stone-400 mt-1 font-mono font-medium">{{ $p->kode_paket }}</div>
                            </td>

                            <td class="py-3.5 px-4">
                                <div class="font-extrabold text-stone-900 text-sm font-urbanist">{{ $p->nama }}</div>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-stone-600 bg-stone-100 px-2 py-0.5 rounded-md">
                                        <svg class="w-3 h-3 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        {{ $p->kapasitas_orang }}
                                    </span>
                                    <span class="text-[10px] text-emerald-700 bg-emerald-50 border border-emerald-200/60 px-1.5 py-0.5 rounded font-bold">
                                        Hemat {{ $p->persen_hemat }}% (Rp {{ number_format($p->hemat_per_hari, 0, ',', '.') }})
                                    </span>
                                </div>
                                @if($p->deskripsi)
                                    <p class="text-[11px] text-stone-500 mt-1 line-clamp-1 max-w-md">{{ $p->deskripsi }}</p>
                                @endif
                            </td>

                            <td class="py-3.5 px-4">
                                <div class="space-y-1 max-w-xs">
                                    @foreach($p->items->take(4) as $it)
                                        <div class="text-[11px] text-stone-600 flex items-center justify-between">
                                            <span class="truncate">{{ $it->alat?->nama ?? 'Alat #'.$it->alat_id }}</span>
                                            <span class="font-bold text-stone-800 ml-2 shrink-0">{{ $it->jumlah }} unit</span>
                                        </div>
                                    @endforeach
                                    @if($p->items->count() > 4)
                                        <span class="text-[10px] text-stone-400 italic block">+ {{ $p->items->count() - 4 }} alat lainnya</span>
                                    @endif
                                </div>
                            </td>

                            <td class="py-3.5 px-4 text-right">
                                <div class="font-extrabold text-sm text-[#0e1b15]">
                                    Rp {{ number_format($p->harga_paket_per_hari, 0, ',', '.') }}
                                </div>
                                <div class="text-[10px] text-stone-400 line-through">
                                    Rp {{ number_format($p->harga_asli_per_hari, 0, ',', '.') }}
                                </div>
                                <span class="text-[10px] text-stone-500">per hari sewa</span>
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                <form method="POST" action="{{ route('admin.paket-bundling.toggle', $p) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            title="Klik untuk ubah status aktif/nonaktif"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider transition cursor-pointer {{ $p->is_aktif ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-stone-100 text-stone-500 hover:bg-stone-200' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $p->is_aktif ? 'bg-emerald-600' : 'bg-stone-400' }}"></span>
                                        <span>{{ $p->is_aktif ? 'Aktif' : 'Nonaktif' }}</span>
                                    </button>
                                </form>
                            </td>

                            <td class="py-3.5 px-5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.paket-bundling.edit', $p) }}"
                                       title="Edit Paket"
                                       class="p-1.5 rounded-lg text-stone-500 hover:text-stone-900 hover:bg-stone-100 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>

                                    <form method="POST" action="{{ route('admin.paket-bundling.destroy', $p) }}"
                                          data-konfirmasi="danger"
                                          data-judul="Hapus Paket Bundling"
                                          data-pesan="Apakah Anda yakin ingin menghapus paket {{ $p->nama }}?"
                                          data-tombol="Ya, Hapus Paket"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                title="Hapus Paket"
                                                class="p-1.5 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 transition cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-stone-400">
                                <svg class="w-12 h-12 mx-auto mb-2 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                <p class="font-bold text-stone-700">Tidak ada paket bundling ditemukan</p>
                                <p class="text-xs text-stone-400 mt-0.5">Mulai dengan menambahkan paket bundling baru seperti Paket 1, 2, atau 3.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($paketList->hasPages())
            <div class="p-4 border-t border-stone-200">
                {{ $paketList->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>

