<x-layouts.admin>
    <x-slot:title>Data Pelanggan Rental</x-slot:title>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-stone-900 tracking-tight">Data Pelanggan Rental</h1>
            <p class="text-xs text-stone-500">Kelola identitas pelanggan yang terdaftar secara daring maupun peminjam langsung di toko fisik (Walk-In)</p>
        </div>

        <a href="{{ route('admin.pelanggan.create') }}" class="px-4 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-sm transition inline-flex items-center gap-1.5 w-fit">
            <span>➕</span>
            <span>Tambah Pelanggan Walk-In</span>
        </a>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white p-4 rounded-xl border border-stone-200 shadow-sm mb-6">
        <form method="GET" action="{{ route('admin.pelanggan.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2">
                <label for="q" class="block text-[11px] font-bold text-stone-600 uppercase mb-1">Cari Nama atau Nomor HP</label>
                <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Ketik nama atau nomor HP/WhatsApp..."
                    class="w-full text-xs rounded-lg border-stone-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm">
            </div>

            <div>
                <label for="tipe" class="block text-[11px] font-bold text-stone-600 uppercase mb-1">Jenis Pelanggan</label>
                <select id="tipe" name="tipe" class="w-full text-xs rounded-lg border-stone-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm">
                    <option value="">-- Semua Jenis --</option>
                    <option value="walk_in" {{ request('tipe') === 'walk_in' ? 'selected' : '' }}>Pelanggan Walk-In (Di Toko)</option>
                    <option value="akun" {{ request('tipe') === 'akun' ? 'selected' : '' }}>Pelanggan Akun Terdaftar</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 rounded-lg bg-stone-900 hover:bg-stone-800 text-white text-xs font-bold transition shadow-sm">
                    Cari 🔍
                </button>
                @if(request()->hasAny(['q', 'tipe']))
                    <a href="{{ route('admin.pelanggan.index') }}" class="px-3 py-2 rounded-lg bg-stone-100 hover:bg-stone-200 text-stone-600 text-xs font-semibold transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Pelanggan -->
    <div class="bg-white rounded-xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-stone-700">
                <thead class="bg-stone-50 text-stone-500 uppercase text-[10px] font-bold border-b border-stone-200 tracking-wider">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Pelanggan</th>
                        <th class="py-3 px-4">Tipe Pemesan</th>
                        <th class="py-3 px-4">No. HP / WhatsApp</th>
                        <th class="py-3 px-4">Alamat Domisili</th>
                        <th class="py-3 px-4">Akun Login</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($pelangganList as $index => $pelanggan)
                        <tr class="hover:bg-stone-50/60 transition">
                            <td class="py-3 px-4 text-center font-medium text-stone-500">
                                {{ $pelangganList->firstItem() + $index }}
                            </td>
                            <td class="py-3 px-4 font-bold text-stone-900">
                                <a href="{{ route('admin.pelanggan.show', $pelanggan->id) }}" class="hover:text-emerald-700 transition">
                                    {{ $pelanggan->nama_lengkap }}
                                </a>
                            </td>
                            <td class="py-3 px-4">
                                @if($pelanggan->is_walk_in)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        Walk-In (Toko)
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                        Akun Daring
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-mono">
                                {{ $pelanggan->no_hp }}
                            </td>
                            <td class="py-3 px-4 text-stone-600 max-w-xs truncate">
                                {{ $pelanggan->alamat }}
                            </td>
                            <td class="py-3 px-4 text-stone-500">
                                @if($pelanggan->user)
                                    <span class="font-medium text-stone-800">{{ $pelanggan->user->email }}</span>
                                @else
                                    <span class="text-stone-400 italic">Tanpa akun login</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('admin.pelanggan.show', $pelanggan->id) }}" class="px-2 py-1 rounded bg-stone-100 hover:bg-stone-200 text-stone-700 font-semibold text-[11px] transition">
                                        Detail
                                    </a>
                                    <a href="{{ route('admin.pelanggan.edit', $pelanggan->id) }}" class="px-2 py-1 rounded bg-stone-100 hover:bg-stone-200 text-stone-700 font-semibold text-[11px] transition">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.pelanggan.destroy', $pelanggan->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pelanggan {{ $pelanggan->nama_lengkap }}?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2 py-1 rounded bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold text-[11px] border border-rose-200 transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-stone-400">
                                Belum ada data pelanggan yang sesuai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pelangganList->hasPages())
            <div class="p-4 border-t border-stone-200">
                {{ $pelangganList->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
