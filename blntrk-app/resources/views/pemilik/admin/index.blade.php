<x-pemilik-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-[#5edd5b]/20 text-[#0e1b15]">
                        <svg class="w-4 h-4 text-[#0e1b15]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </span>
                    <span class="text-xs font-bold uppercase tracking-wider text-[#5edd5b]">Manajemen Pengguna</span>
                </div>
                <h2 class="font-extrabold text-2xl text-[#0e1b15] tracking-tight">Kelola Akun Admin / Kasir</h2>
            </div>
            <div>
                <a href="{{ route('pemilik.admin.create') }}"
                   class="inline-flex items-center gap-2 py-2.5 px-4 bg-[#0e1b15] hover:bg-[#1a2e24] text-white text-xs font-bold rounded-xl transition shadow-sm">
                    <svg class="w-4 h-4 text-[#5edd5b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Tambah Staf Admin</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl flex items-center gap-3 text-sm font-semibold shadow-sm">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl flex items-center gap-3 text-sm font-semibold shadow-sm">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- PENCARIAN -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm">
            <form method="GET" action="{{ route('pemilik.admin.index') }}" class="flex items-center gap-3">
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </span>
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="Cari nama atau email staf admin..."
                           class="w-full pl-10 pr-4 py-2 text-xs font-medium rounded-xl border-slate-200 focus:border-[#5edd5b] focus:ring-[#5edd5b]">
                </div>
                <button type="submit"
                        class="py-2 px-4 bg-[#0e1b15] hover:bg-[#1a2e24] text-white text-xs font-bold rounded-xl transition shadow-sm">
                    Cari
                </button>
            </form>
        </div>

        <!-- TABEL ADMIN -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Nama Staf</th>
                            <th class="py-3 px-4">Email Login</th>
                            <th class="py-3 px-4">Status Akses</th>
                            <th class="py-3 px-4">Terdaftar Pada</th>
                            <th class="py-3 px-4 text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($admins as $admin)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <div class="font-bold text-[#0e1b15] text-sm">{{ $admin->nama }}</div>
                                    <div class="text-[10px] text-slate-400">ID: #{{ $admin->id }}</div>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap font-mono text-slate-600">
                                    {{ $admin->email }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    @if($admin->is_aktif)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span>Aktif</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            <span>Nonaktif</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap text-slate-500">
                                    {{ $admin->created_at?->format('d/m/Y H:i') ?? '-' }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap text-right">
                                    <div class="inline-flex items-center gap-1.5 justify-end">
                                        <!-- TOGGLE AKTIF/NONAKTIF -->
                                        <form method="POST" action="{{ route('pemilik.admin.toggle', $admin) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 py-1 px-2.5 rounded-lg text-xs font-bold transition {{ $admin->is_aktif ? 'bg-amber-50 hover:bg-amber-100 text-amber-700' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700' }}"
                                                    title="{{ $admin->is_aktif ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                </svg>
                                                <span>{{ $admin->is_aktif ? 'Nonaktifkan' : 'Aktifkan' }}</span>
                                            </button>
                                        </form>

                                        <!-- RESET PASSWORD -->
                                        <form method="POST" action="{{ route('pemilik.admin.reset-password', $admin) }}"
                                              data-konfirmasi="warning"
                                              data-judul="Reset Password Akun Admin"
                                              data-pesan="Reset password akun {{ $admin->nama }} menjadi default (password123)?"
                                              data-tombol="Ya, Reset Password">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 py-1 px-2.5 rounded-lg text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition"
                                                    title="Reset Password Default">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                                </svg>
                                                <span>Reset Pass</span>
                                            </button>
                                        </form>

                                        <!-- EDIT -->
                                        <a href="{{ route('pemilik.admin.edit', $admin) }}"
                                           class="inline-flex items-center gap-1 py-1 px-2.5 rounded-lg text-xs font-bold bg-[#0e1b15]/10 hover:bg-[#0e1b15]/20 text-[#0e1b15] transition"
                                           title="Edit Profil Admin">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            <span>Edit</span>
                                        </a>

                                        <!-- HAPUS -->
                                        <form method="POST" action="{{ route('pemilik.admin.destroy', $admin) }}"
                                              data-konfirmasi="danger"
                                              data-judul="Hapus Akun Admin"
                                              data-pesan="Apakah Anda yakin ingin menghapus akun admin {{ $admin->nama }}?"
                                              data-tombol="Ya, Hapus Admin">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 transition"
                                                    title="Hapus Akun">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-10 text-center text-slate-400">
                                    Belum ada staf admin yang terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($admins->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $admins->links() }}
                </div>
            @endif
        </div>
    </div>
</x-pemilik-layout>
