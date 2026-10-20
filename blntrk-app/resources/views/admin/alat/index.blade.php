<x-layouts.admin>
    <x-slot:title>Inventaris Alat Outdoor</x-slot:title>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-stone-900 tracking-tight">Data Alat Outdoor</h1>
            <p class="text-xs text-stone-500">Kelola inventaris, tarif rental harian, dan kondisi ketersediaan stok fisik toko</p>
        </div>

        <a href="{{ route('admin.alat.create') }}" class="px-4 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-sm transition inline-flex items-center gap-1.5 w-fit">
            <span>➕</span>
            <span>Tambah Alat Baru</span>
        </a>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white p-4 rounded-xl border border-stone-200 shadow-sm mb-6">
        <form method="GET" action="{{ route('admin.alat.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2">
                <label for="q" class="block text-[11px] font-bold text-stone-600 uppercase mb-1">Cari Nama atau Kode Alat</label>
                <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Ketik nama atau kode alat (misal: TND-001)..."
                    class="w-full text-xs rounded-lg border-stone-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm">
            </div>

            <div>
                <label for="kategori_id" class="block text-[11px] font-bold text-stone-600 uppercase mb-1">Kategori</label>
                <select id="kategori_id" name="kategori_id" class="w-full text-xs rounded-lg border-stone-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm">
                    <option value="">-- Semua Kategori --</option>
                    @foreach($kategoriList as $k)
                        <option value="{{ $k->id }}" {{ request('kategori_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 rounded-lg bg-stone-900 hover:bg-stone-800 text-white text-xs font-bold transition shadow-sm">
                    Filter 🔍
                </button>
                @if(request()->hasAny(['q', 'kategori_id', 'status']))
                    <a href="{{ route('admin.alat.index') }}" class="px-3 py-2 rounded-lg bg-stone-100 hover:bg-stone-200 text-stone-600 text-xs font-semibold transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Alat -->
    <div class="bg-white rounded-xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-stone-700">
                <thead class="bg-stone-50 text-stone-500 uppercase text-[10px] font-bold border-b border-stone-200 tracking-wider">
                    <tr>
                        <th class="py-3 px-4 w-16">Foto</th>
                        <th class="py-3 px-4">Kode & Nama Alat</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4">Tarif Sewa</th>
                        <th class="py-3 px-4 text-center">Stok (Total/Servis/Siap)</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($alatList as $alat)
                        <tr class="hover:bg-stone-50/60 transition">
                            <td class="py-3 px-4">
                                <div class="w-12 h-12 rounded-lg bg-stone-100 border border-stone-200 overflow-hidden flex items-center justify-center">
                                    @if($alat->foto)
                                        <img src="{{ asset('storage/' . $alat->foto) }}" alt="{{ $alat->nama }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-xl">⛺</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-mono text-[10px] font-bold text-emerald-800 block">
                                    {{ $alat->kode_alat }}
                                </span>
                                <a href="{{ route('admin.alat.show', $alat->id) }}" class="font-bold text-stone-900 hover:text-emerald-700 text-xs transition">
                                    {{ $alat->nama }}
                                </a>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-stone-100 text-stone-700 border border-stone-200">
                                    {{ $alat->kategori?->nama ?? 'Umum' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-bold text-stone-900">
                                Rp {{ number_format($alat->harga_per_hari, 0, ',', '.') }}<span class="text-[10px] font-normal text-stone-400">/hari</span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="inline-flex items-center gap-1 font-mono font-semibold text-xs">
                                    <span class="text-stone-800" title="Total Fisik">{{ $alat->stok_total }}</span>
                                    <span class="text-stone-300">/</span>
                                    <span class="{{ $alat->stok_perawatan > 0 ? 'text-amber-600 font-bold' : 'text-stone-400' }}" title="Dalam Perawatan">
                                        {{ $alat->stok_perawatan }}
                                    </span>
                                    <span class="text-stone-300">/</span>
                                    <span class="px-1.5 py-0.2 rounded font-bold {{ $alat->stok_tersedia > 0 ? 'text-emerald-700 bg-emerald-50' : 'text-rose-700 bg-rose-50' }}" title="Siap Disewa">
                                        {{ $alat->stok_tersedia }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <form method="POST" action="{{ route('admin.alat.toggle', $alat->id) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" title="Klik untuk mengubah status"
                                        class="px-2 py-0.5 rounded-full text-[10px] font-bold cursor-pointer transition {{ $alat->is_aktif ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-stone-200 text-stone-600 hover:bg-stone-300' }}">
                                        {{ $alat->is_aktif ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </form>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('admin.alat.show', $alat->id) }}" class="px-2 py-1 rounded bg-stone-100 hover:bg-stone-200 text-stone-700 font-semibold text-[11px] transition">
                                        Detail
                                    </a>
                                    <a href="{{ route('admin.alat.edit', $alat->id) }}" class="px-2 py-1 rounded bg-stone-100 hover:bg-stone-200 text-stone-700 font-semibold text-[11px] transition">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.alat.destroy', $alat->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus alat {{ $alat->nama }}? Riwayat pesanan lama tetap aman.');" class="inline">
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
                                Tidak ada data alat yang sesuai dengan pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($alatList->hasPages())
            <div class="p-4 border-t border-stone-200">
                {{ $alatList->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
