<x-layouts.admin>
    <x-slot:title>Kelola Akun Pengguna</x-slot:title>

    <div class="mb-7 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="text-[10px] font-bold uppercase tracking-widest text-[#4e7a5a] block mb-1">Akses & Pengguna</span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#14201a] tracking-tight bl-d">Kelola Akun Pengguna</h1>
            <p class="text-xs text-stone-500 mt-0.5">Manajemen akun staf Admin Toko dan akun daring Pelanggan BLNTRK Outdoor</p>
        </div>

        <a href="{{ route('admin.user.create') }}" class="px-4 py-2.5 rounded-xl bg-[#0e1b15] hover:bg-[#1e3a2c] text-[#f6c453] text-xs font-bold shadow-sm transition inline-flex items-center gap-2 w-fit cursor-pointer">
            <span>➕</span>
            <span>Tambah Pengguna Baru</span>
        </a>
    </div>

    <!-- Alert Khusus Saat Reset Password Berhasil -->
    @if(session('password_baru'))
        <div class="mb-6 p-5 rounded-2xl bg-amber-50/90 border border-amber-300/80 shadow-xs flex items-start justify-between gap-4">
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-lg shrink-0">
                    🔑
                </div>
                <div>
                    <h3 class="font-extrabold text-amber-950 text-sm bl-d">Password Berhasil Direset!</h3>
                    <p class="text-xs text-amber-800 mt-0.5">
                        Password sementara untuk <strong>{{ session('password_baru')['nama'] }}</strong> ({{ session('password_baru')['email'] }}):
                    </p>
                    <div class="mt-2.5 inline-block px-3.5 py-1.5 bg-white font-mono font-bold text-sm text-[#14201a] rounded-xl border border-amber-300 select-all shadow-2xs">
                        {{ session('password_baru')['password'] }}
                    </div>
                    <p class="text-[11px] text-amber-700 mt-1.5 font-medium">
                        *Salin password ini dan serahkan kepada pengguna. Pesan ini hanya ditampilkan sekali.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Filter & Pencarian -->
    <div class="bg-white p-5 rounded-2xl border border-stone-200/90 shadow-xs mb-6">
        <form method="GET" action="{{ route('admin.user.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3.5">
            <div class="sm:col-span-2">
                <label for="q" class="block text-[11px] font-bold text-[#14201a] uppercase tracking-wider mb-1.5">Cari Nama atau Email</label>
                <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Ketik nama atau alamat email..."
                    class="w-full text-xs rounded-xl border-stone-300 focus:border-[#1e3a2c] focus:ring-[#1e3a2c] shadow-xs text-[#14201a]">
            </div>

            <div>
                <label for="role" class="block text-[11px] font-bold text-[#14201a] uppercase tracking-wider mb-1.5">Peran (Role)</label>
                <select id="role" name="role" class="w-full text-xs rounded-xl border-stone-300 focus:border-[#1e3a2c] focus:ring-[#1e3a2c] shadow-xs text-[#14201a]">
                    <option value="">-- Semua Peran --</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->nama }}" {{ request('role') === $r->nama ? 'selected' : '' }}>
                            {{ $r->label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-[#0e1b15] hover:bg-[#1e3a2c] text-[#f6c453] text-xs font-bold transition shadow-sm cursor-pointer">
                    Filter 🔍
                </button>
                @if(request()->hasAny(['q', 'role']))
                    <a href="{{ route('admin.user.index') }}" class="px-3.5 py-2.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-600 text-xs font-bold transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Pengguna -->
    <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-stone-700">
                <thead class="bg-stone-50/80 text-stone-500 uppercase text-[10px] font-bold border-b border-stone-200 tracking-wider">
                    <tr>
                        <th class="py-3 px-5 w-14 text-center">No</th>
                        <th class="py-3 px-4">Nama Pengguna</th>
                        <th class="py-3 px-4">Alamat Email</th>
                        <th class="py-3 px-4">Peran</th>
                        <th class="py-3 px-4 text-center">Status Keaktifan</th>
                        <th class="py-3 px-4">Terdaftar</th>
                        <th class="py-3 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($userList as $index => $u)
                        <tr class="hover:bg-stone-50/60 transition">
                            <td class="py-3.5 px-5 text-center font-bold text-stone-400">
                                {{ $userList->firstItem() + $index }}
                            </td>
                            <td class="py-3.5 px-4 font-bold text-[#14201a] text-sm bl-d">
                                {{ $u->nama }}
                                @if($u->id === auth()->id())
                                    <span class="ml-1 text-[10px] font-bold text-[#4e7a5a] bg-stone-100 px-1.5 py-0.5 rounded-md">(Anda)</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-mono text-stone-600">
                                {{ $u->email }}
                            </td>
                            <td class="py-3.5 px-4">
                                @if($u->role?->nama === 'pemilik')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-[#0e1b15] text-[#f6c453] border border-[#1e3a2c]">
                                        Pemilik Toko
                                    </span>
                                @elseif($u->role?->nama === 'admin')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-[#f6c453]/25 text-[#14201a] border border-[#f6c453]/40">
                                        Admin Toko
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-[#1e3a2c]/10 text-[#1e3a2c] border border-[#1e3a2c]/20">
                                        Pelanggan
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($u->punyaRole('pemilik') || $u->id === auth()->id())
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-stone-100 text-stone-600 border border-stone-200">
                                        Aktif Tetap
                                    </span>
                                @else
                                    <form method="POST" action="{{ route('admin.user.toggle', $u->id) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" title="Klik untuk mengubah status aktif"
                                            class="px-2.5 py-0.5 rounded-full text-[10px] font-bold cursor-pointer transition {{ $u->is_aktif ? 'bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100' : 'bg-rose-50 text-rose-800 border border-rose-200 hover:bg-rose-100' }}">
                                            {{ $u->is_aktif ? 'Aktif' : 'Nonaktif' }}
                                        </button>
                                    </form>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-stone-500 font-medium">
                                {{ $u->created_at?->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    @if(! $u->punyaRole('pemilik'))
                                        <a href="{{ route('admin.user.edit', $u->id) }}" class="px-2.5 py-1.5 rounded-xl bg-stone-100 hover:bg-[#0e1b15] hover:text-[#f6c453] text-stone-700 font-bold text-[11px] transition">
                                            Edit
                                        </a>

                                        <form method="POST" action="{{ route('admin.user.reset-password', $u->id) }}" onsubmit="return confirm('Reset password akun {{ $u->nama }}?');" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold text-[11px] border border-amber-200 transition cursor-pointer">
                                                Reset
                                            </button>
                                        </form>

                                        @if($u->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.user.destroy', $u->id) }}" onsubmit="return confirm('Hapus akun {{ $u->nama }}?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-[11px] border border-rose-200/80 transition cursor-pointer">
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
                            <td colspan="7" class="py-10 text-center text-stone-400">
                                <span class="text-2xl block mb-1">🔑</span>
                                Tidak ada akun yang cocok dengan kriteria filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($userList->hasPages())
            <div class="p-4 border-t border-stone-200/80 bg-stone-50/40">
                {{ $userList->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>

