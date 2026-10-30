<x-layouts.admin>
    <x-slot:title>Data Pelanggan Rental</x-slot:title>

    <div class="mb-7 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#0e1b15] block mb-1">Pelanggan & Pengguna</span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#14201a] tracking-tight">Data Pelanggan Rental</h1>
            <p class="text-xs text-stone-500 mt-0.5">Kelola identitas pelanggan terdaftar daring dan peminjam langsung di toko fisik (Walk-In)</p>
        </div>

        <a href="{{ route('admin.pelanggan.create') }}" class="px-4 py-2.5 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-extrabold shadow-sm transition inline-flex items-center gap-2 w-fit cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Pelanggan Walk-In</span>
        </a>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white p-5 rounded-2xl border border-stone-200/90 shadow-xs mb-6">
        <form method="GET" action="{{ route('admin.pelanggan.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3.5">
            <div class="sm:col-span-2">
                <label for="q" class="block text-[11px] font-bold text-[#14201a] uppercase tracking-wider mb-1.5">Cari Nama atau Nomor HP</label>
                <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Ketik nama atau nomor HP/WhatsApp..."
                    class="w-full text-xs rounded-xl border-stone-300 focus:border-[#5edd5b] focus:ring-[#5edd5b] shadow-xs text-[#14201a]">
            </div>

            <div>
                <label for="tipe" class="block text-[11px] font-bold text-[#14201a] uppercase tracking-wider mb-1.5">Jenis Pelanggan</label>
                <select id="tipe" name="tipe" class="w-full text-xs rounded-xl border-stone-300 focus:border-[#5edd5b] focus:ring-[#5edd5b] shadow-xs text-[#14201a]">
                    <option value="">-- Semua Jenis --</option>
                    <option value="walk_in" {{ request('tipe') === 'walk_in' ? 'selected' : '' }}>Pelanggan Walk-In (Di Toko)</option>
                    <option value="akun" {{ request('tipe') === 'akun' ? 'selected' : '' }}>Pelanggan Akun Terdaftar</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-[#0e1b15] hover:bg-[#14251e] text-white text-xs font-extrabold transition shadow-sm cursor-pointer inline-flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-[#5edd5b]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>Cari</span>
                </button>
                @if(request()->hasAny(['q', 'tipe']))
                    <a href="{{ route('admin.pelanggan.index') }}" class="px-3.5 py-2.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-600 text-xs font-bold transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Pelanggan -->
    <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-stone-700">
                <thead class="bg-stone-50/80 text-stone-500 uppercase text-[10px] font-bold border-b border-stone-200 tracking-wider">
                    <tr>
                        <th class="py-3 px-5 w-14 text-center">No</th>
                        <th class="py-3 px-4">Nama Pelanggan</th>
                        <th class="py-3 px-4">Tipe Pemesan</th>
                        <th class="py-3 px-4">No. HP / WhatsApp</th>
                        <th class="py-3 px-4">Alamat Domisili</th>
                        <th class="py-3 px-4">Akun Login</th>
                        <th class="py-3 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($pelangganList as $index => $pelanggan)
                        <tr class="hover:bg-stone-50/60 transition">
                            <td class="py-3.5 px-5 text-center font-bold text-stone-400">
                                {{ $pelangganList->firstItem() + $index }}
                            </td>
                            <td class="py-3.5 px-4 font-bold text-[#14201a] text-sm">
                                <a href="{{ route('admin.pelanggan.show', $pelanggan->id) }}" class="hover:text-emerald-700 transition">
                                    {{ $pelanggan->nama_lengkap }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($pelanggan->is_walk_in)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-[#5edd5b]/20 text-emerald-900 border border-[#5edd5b]/40">
                                        Walk-In (Toko)
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-stone-100 text-stone-700 border border-stone-200">
                                        Akun Daring
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-mono font-medium text-stone-700">
                                {{ $pelanggan->no_hp }}
                            </td>
                            <td class="py-3.5 px-4 text-stone-600 max-w-xs truncate">
                                {{ $pelanggan->alamat }}
                            </td>
                            <td class="py-3.5 px-4 text-stone-500">
                                @if($pelanggan->user)
                                    <span class="font-medium text-[#14201a]">{{ $pelanggan->user->email }}</span>
                                @else
                                    <span class="text-stone-400 italic text-[11px]">Tanpa akun login</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('admin.pelanggan.show', $pelanggan->id) }}" class="px-2.5 py-1.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold text-[11px] transition">
                                        Detail
                                    </a>
                                    <a href="{{ route('admin.pelanggan.edit', $pelanggan->id) }}" class="px-2.5 py-1.5 rounded-xl bg-stone-100 hover:bg-[#5edd5b] hover:text-black text-stone-700 font-bold text-[11px] transition">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.pelanggan.destroy', $pelanggan->id) }}"
                                          data-konfirmasi="danger"
                                          data-judul="Hapus Data Pelanggan"
                                          data-pesan="Apakah Anda yakin ingin menghapus data pelanggan {{ $pelanggan->nama_lengkap }}?"
                                          data-tombol="Ya, Hapus Pelanggan"
                                          class="inline">
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
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                </div>
                                Belum ada data pelanggan yang sesuai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pelangganList->hasPages())
            <div class="p-4 border-t border-stone-200/80 bg-stone-50/40">
                {{ $pelangganList->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>

