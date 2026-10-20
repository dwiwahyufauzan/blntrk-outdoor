<x-layouts.admin>
    <x-slot:title>Data Pelanggan Rental</x-slot:title>

    <div class="mb-7 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="text-[10px] font-bold uppercase tracking-widest text-[#4e7a5a] block mb-1">Pelanggan & Pengguna</span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#14201a] tracking-tight bl-d">Data Pelanggan Rental</h1>
            <p class="text-xs text-stone-500 mt-0.5">Kelola identitas pelanggan terdaftar daring dan peminjam langsung di toko fisik (Walk-In)</p>
        </div>

        <a href="{{ route('admin.pelanggan.create') }}" class="px-4 py-2.5 rounded-xl bg-[#0e1b15] hover:bg-[#1e3a2c] text-[#f6c453] text-xs font-bold shadow-sm transition inline-flex items-center gap-2 w-fit cursor-pointer">
            <span>➕</span>
            <span>Tambah Pelanggan Walk-In</span>
        </a>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white p-5 rounded-2xl border border-stone-200/90 shadow-xs mb-6">
        <form method="GET" action="{{ route('admin.pelanggan.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3.5">
            <div class="sm:col-span-2">
                <label for="q" class="block text-[11px] font-bold text-[#14201a] uppercase tracking-wider mb-1.5">Cari Nama atau Nomor HP</label>
                <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Ketik nama atau nomor HP/WhatsApp..."
                    class="w-full text-xs rounded-xl border-stone-300 focus:border-[#1e3a2c] focus:ring-[#1e3a2c] shadow-xs text-[#14201a]">
            </div>

            <div>
                <label for="tipe" class="block text-[11px] font-bold text-[#14201a] uppercase tracking-wider mb-1.5">Jenis Pelanggan</label>
                <select id="tipe" name="tipe" class="w-full text-xs rounded-xl border-stone-300 focus:border-[#1e3a2c] focus:ring-[#1e3a2c] shadow-xs text-[#14201a]">
                    <option value="">-- Semua Jenis --</option>
                    <option value="walk_in" {{ request('tipe') === 'walk_in' ? 'selected' : '' }}>Pelanggan Walk-In (Di Toko)</option>
                    <option value="akun" {{ request('tipe') === 'akun' ? 'selected' : '' }}>Pelanggan Akun Terdaftar</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-[#0e1b15] hover:bg-[#1e3a2c] text-[#f6c453] text-xs font-bold transition shadow-sm cursor-pointer">
                    Cari 🔍
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
                            <td class="py-3.5 px-4 font-bold text-[#14201a] text-sm bl-d">
                                <a href="{{ route('admin.pelanggan.show', $pelanggan->id) }}" class="hover:text-[#1e3a2c] transition">
                                    {{ $pelanggan->nama_lengkap }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($pelanggan->is_walk_in)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-[#f6c453]/25 text-[#14201a] border border-[#f6c453]/40">
                                        Walk-In (Toko)
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-[#1e3a2c]/10 text-[#1e3a2c] border border-[#1e3a2c]/20">
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
                                    <a href="{{ route('admin.pelanggan.edit', $pelanggan->id) }}" class="px-2.5 py-1.5 rounded-xl bg-stone-100 hover:bg-[#0e1b15] hover:text-[#f6c453] text-stone-700 font-bold text-[11px] transition">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.pelanggan.destroy', $pelanggan->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pelanggan {{ $pelanggan->nama_lengkap }}?');" class="inline">
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
                                <span class="text-2xl block mb-1">👥</span>
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

