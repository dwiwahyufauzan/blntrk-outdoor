<x-layouts.admin>
    <x-slot:title>Kelola Kategori Denda - BLNTRK Outdoor</x-slot:title>

    <div class="mb-7 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#4e7a5a] block mb-1">Master Kebijakan Toko</span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#14201a] tracking-tight bl-d">Kategori Denda & Ganti Rugi</h1>
            <p class="text-xs text-stone-500 mt-0.5">Atur kategori denda keterlambatan, kehilangan kelengkapan, dan kerusakan alat</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.kategori-denda.create') }}"
               class="px-4 py-2.5 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-extrabold shadow-sm transition inline-flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Kategori Denda</span>
            </a>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-stone-200/90 shadow-xs mb-6">
        <form method="GET" action="{{ route('admin.kategori-denda.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama, kode, atau keterangan kategori..."
                       class="w-full pl-10 pr-4 py-2 text-xs rounded-xl border-stone-200 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-2xs">
            </div>

            <div class="sm:w-56">
                <select name="jenis" onchange="this.form.submit()"
                        class="w-full py-2 text-xs rounded-xl border-stone-200 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-2xs">
                    <option value="">-- Semua Jenis Denda --</option>
                    <option value="keterlambatan" {{ $jenis === 'keterlambatan' ? 'selected' : '' }}>Keterlambatan</option>
                    <option value="kehilangan" {{ $jenis === 'kehilangan' ? 'selected' : '' }}>Kehilangan Barang</option>
                    <option value="kerusakan" {{ $jenis === 'kerusakan' ? 'selected' : '' }}>Kerusakan Fisik</option>
                    <option value="lainnya" {{ $jenis === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>

            @if($q || $jenis)
                <a href="{{ route('admin.kategori-denda.index') }}"
                   class="px-3.5 py-2 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold transition flex items-center justify-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Tabel Daftar Kategori Denda -->
    <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-stone-700">
                <thead class="bg-stone-50/80 text-stone-500 uppercase text-[10px] font-bold border-b border-stone-200 tracking-wider">
                    <tr>
                        <th class="py-3 px-5">Kode</th>
                        <th class="py-3 px-4">Nama Kategori & Deskripsi</th>
                        <th class="py-3 px-4">Jenis</th>
                        <th class="py-3 px-4 text-right">Tarif Acuan / Satuan</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Total Kasus</th>
                        <th class="py-3 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($kategoriList as $k)
                        <tr class="hover:bg-stone-50/60 transition">
                            <td class="py-3.5 px-5 font-mono font-bold text-stone-900">
                                <span class="px-2 py-0.5 rounded-lg bg-stone-100 text-[#0e1b15] border border-stone-200">
                                    {{ $k->kode }}
                                </span>
                            </td>

                            <td class="py-3.5 px-4">
                                <div class="font-extrabold text-stone-900 text-sm font-urbanist">{{ $k->nama }}</div>
                                <p class="text-[11px] text-stone-500 mt-0.5 line-clamp-2 max-w-md">
                                    {{ $k->deskripsi ?: 'Tidak ada deskripsi tambahan.' }}
                                </p>
                            </td>

                            <td class="py-3.5 px-4">
                                @php
                                    $badgeJenis = match($k->jenis) {
                                        'keterlambatan' => 'bg-amber-100 text-amber-900 border-amber-300',
                                        'kehilangan' => 'bg-rose-100 text-rose-900 border-rose-300 font-extrabold',
                                        'kerusakan' => 'bg-orange-100 text-orange-900 border-orange-300',
                                        default => 'bg-purple-100 text-purple-900 border-purple-300',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $badgeJenis }}">
                                    {{ $k->label_jenis }}
                                </span>
                            </td>

                            <td class="py-3.5 px-4 text-right">
                                @if($k->tarif_standar > 0)
                                    <div class="font-mono font-black text-stone-900 text-xs">
                                        Rp{{ number_format($k->tarif_standar, 0, ',', '.') }}
                                    </div>
                                    <span class="text-[10px] text-stone-400">{{ $k->satuan }}</span>
                                @else
                                    <span class="text-[11px] font-bold text-stone-500 italic">Proporsional sewa</span>
                                    <span class="block text-[10px] text-stone-400">{{ $k->satuan }}</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                <form method="POST" action="{{ route('admin.kategori-denda.toggle', $k->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            title="Klik untuk mengubah status aktif"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider transition cursor-pointer {{ $k->is_aktif ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-stone-200 text-stone-600 hover:bg-stone-300' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $k->is_aktif ? 'bg-emerald-600' : 'bg-stone-400' }}"></span>
                                        <span>{{ $k->is_aktif ? 'Aktif' : 'Non-Aktif' }}</span>
                                    </button>
                                </form>
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-xl bg-stone-50 border border-stone-200/80 font-mono font-bold text-stone-700 text-xs">
                                    {{ $k->denda_count }}
                                </span>
                            </td>

                            <td class="py-3.5 px-5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.kategori-denda.edit', $k->id) }}"
                                       class="p-1.5 rounded-lg text-stone-600 hover:text-black hover:bg-stone-100 transition"
                                       title="Edit Kategori">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </a>

                                    <form method="POST" action="{{ route('admin.kategori-denda.destroy', $k->id) }}"
                                          data-konfirmasi="danger"
                                          data-judul="Hapus Kategori Denda"
                                          data-pesan="Apakah Anda yakin ingin menghapus kategori denda [{{ $k->kode }}] {{ $k->nama }}?"
                                          data-tombol="Ya, Hapus Kategori">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-1.5 rounded-lg text-stone-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer"
                                                title="Hapus Kategori">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-stone-400">
                                Tidak ada data kategori denda ditemukan.
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
