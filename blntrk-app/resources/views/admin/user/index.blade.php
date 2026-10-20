<x-layouts.admin>
    <x-slot:title>Kelola Akun Pengguna</x-slot:title>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-stone-900 tracking-tight">Kelola Akun Pengguna</h1>
            <p class="text-xs text-stone-500">Manajemen akun staf Admin Toko dan Pelanggan terdaftar BLNTRK Outdoor</p>
        </div>

        <a href="{{ route('admin.user.create') }}" class="px-4 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-sm transition inline-flex items-center gap-1.5 w-fit">
            <span>➕</span>
            <span>Tambah Pengguna Baru</span>
        </a>
    </div>

    <!-- Alert Khusus Saat Reset Password Berhasil -->
    @if(session('password_baru'))
        <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-300 shadow-sm flex items-start justify-between gap-4">
            <div class="flex items-start gap-3">
                <span class="text-2xl">🔑</span>
                <div>
                    <h3 class="font-bold text-amber-900 text-sm">Password Berhasil Direset!</h3>
                    <p class="text-xs text-amber-800 mt-0.5">
                        Password sementara untuk <strong>{{ session('password_baru')['nama'] }}</strong> ({{ session('password_baru')['email'] }}):
                    </p>
                    <div class="mt-2 inline-block px-3 py-1 bg-white font-mono font-bold text-sm text-stone-900 rounded border border-amber-300 select-all">
                        {{ session('password_baru')['password'] }}
                    </div>
                    <p class="text-[11px] text-amber-700 mt-1">
                        *Salin password ini dan serahkan kepada pengguna. Pesan ini hanya ditampilkan sekali.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Filter & Pencarian -->
    <div class="bg-white p-4 rounded-xl border border-stone-200 shadow-sm mb-6">
        <form method="GET" action="{{ route('admin.user.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2">
                <label for="q" class="block text-[11px] font-bold text-stone-600 uppercase mb-1">Cari Nama atau Email</label>
                <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Ketik nama atau alamat email..."
                    class="w-full text-xs rounded-lg border-stone-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm">
            </div>

            <div>
                <label for="role" class="block text-[11px] font-bold text-stone-600 uppercase mb-1">Peran (Role)</label>
                <select id="role" name="role" class="w-full text-xs rounded-lg border-stone-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-sm">
                    <option value="">-- Semua Peran --</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->nama }}" {{ request('role') === $r->nama ? 'selected' : '' }}>
                            {{ $r->label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 rounded-lg bg-stone-900 hover:bg-stone-800 text-white text-xs font-bold transition shadow-sm">
                    Filter 🔍
                </button>
                @if(request()->hasAny(['q', 'role']))
                    <a href="{{ route('admin.user.index') }}" class="px-3 py-2 rounded-lg bg-stone-100 hover:bg-stone-200 text-stone-600 text-xs font-semibold transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Pengguna -->
    <div class="bg-white rounded-xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-stone-700">
                <thead class="bg-stone-50 text-stone-500 uppercase text-[10px] font-bold border-b border-stone-200 tracking-wider">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Pengguna</th>
                        <th class="py-3 px-4">Alamat Email</th>
                        <th class="py-3 px-4">Peran</th>
                        <th class="py-3 px-4 text-center">Status Keaktifan</th>
                        <th class="py-3 px-4">Terdaftar</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($userList as $index => $u)
                        <tr class="hover:bg-stone-50/60 transition">
                            <td class="py-3 px-4 text-center font-medium text-stone-500">
                                {{ $userList->firstItem() + $index }}
                            </td>
                            <td class="py-3 px-4 font-bold text-stone-900">
                                {{ $u->nama }}
                                @if($u->id === auth()->id())
                                    <span class="ml-1 text-[10px] font-bold text-emerald-700">(Anda)</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-mono text-stone-600">
                                {{ $u->email }}
                            </td>
                            <td class="py-3 px-4">
                                @if($u->role?->nama === 'pemilik')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800 border border-purple-200">
                                        Pemilik Toko
                                    </span>
                                @elseif($u->role?->nama === 'admin')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        Admin Toko
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                        Pelanggan
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($u->punyaRole('pemilik') || $u->id === auth()->id())
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        Aktif Tetap
                                    </span>
                                @else
                                    <form method="POST" action="{{ route('admin.user.toggle', $u->id) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" title="Klik untuk mengubah status aktif"
                                            class="px-2 py-0.5 rounded-full text-[10px] font-bold cursor-pointer transition {{ $u->is_aktif ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-rose-100 text-rose-800 hover:bg-rose-200' }}">
                                            {{ $u->is_aktif ? 'Aktif' : 'Nonaktif' }}
                                        </button>
                                    </form>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-stone-500">
                                {{ $u->created_at?->format('d/m/Y') }}
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    @if(! $u->punyaRole('pemilik'))
                                        <a href="{{ route('admin.user.edit', $u->id) }}" class="px-2 py-1 rounded bg-stone-100 hover:bg-stone-200 text-stone-700 font-semibold text-[11px] transition">
                                            Edit
                                        </a>

                                        <form method="POST" action="{{ route('admin.user.reset-password', $u->id) }}" onsubmit="return confirm('Reset password akun {{ $u->nama }}?');" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2 py-1 rounded bg-amber-50 hover:bg-amber-100 text-amber-700 font-semibold text-[11px] border border-amber-200 transition">
                                                Reset
                                            </button>
                                        </form>

                                        @if($u->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.user.destroy', $u->id) }}" onsubmit="return confirm('Hapus akun {{ $u->nama }}?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-2 py-1 rounded bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold text-[11px] border border-rose-200 transition">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        <span class="text-[10px] text-stone-400 italic">Terkunci</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-stone-400">
                                Tidak ada akun yang cocok dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($userList->hasPages())
            <div class="p-4 border-t border-stone-200">
                {{ $userList->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
