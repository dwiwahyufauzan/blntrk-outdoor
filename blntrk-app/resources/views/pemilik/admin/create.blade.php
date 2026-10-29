<x-pemilik-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 mb-1">
            <a href="{{ route('pemilik.admin.index') }}" class="text-xs font-bold text-slate-400 hover:text-[#0e1b15] transition">
                &larr; Kembali ke Kelola Admin
            </a>
        </div>
        <h2 class="font-extrabold text-2xl text-[#0e1b15] tracking-tight">Tambah Staf Admin Baru</h2>
    </x-slot>

    <div class="max-w-xl">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
            <form method="POST" action="{{ route('pemilik.admin.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap Staf</label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required
                           placeholder="Contoh: Budi Santoso"
                           class="w-full text-sm font-medium rounded-xl border-slate-200 focus:border-[#5edd5b] focus:ring-[#5edd5b]">
                    @error('nama') <span class="text-xs text-rose-500 font-semibold">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Email Login</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           placeholder="budi@blntrk.com"
                           class="w-full text-sm font-medium rounded-xl border-slate-200 focus:border-[#5edd5b] focus:ring-[#5edd5b]">
                    @error('email') <span class="text-xs text-rose-500 font-semibold">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Password</label>
                    <input type="password" name="password" required
                           placeholder="Minimal 8 karakter"
                           class="w-full text-sm font-medium rounded-xl border-slate-200 focus:border-[#5edd5b] focus:ring-[#5edd5b]">
                    @error('password') <span class="text-xs text-rose-500 font-semibold">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required
                           placeholder="Ulangi password di atas"
                           class="w-full text-sm font-medium rounded-xl border-slate-200 focus:border-[#5edd5b] focus:ring-[#5edd5b]">
                </div>

                <div class="pt-2">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_aktif" value="1" {{ old('is_aktif', '1') == '1' ? 'checked' : '' }}
                               class="rounded border-slate-300 text-[#5edd5b] focus:ring-[#5edd5b]">
                        <span class="text-xs font-bold text-slate-700">Akun Langsung Aktif</span>
                    </label>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <a href="{{ route('pemilik.admin.index') }}"
                       class="py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                        Batal
                    </a>
                    <button type="submit"
                            class="py-2.5 px-5 bg-[#0e1b15] hover:bg-[#1a2e24] text-white font-extrabold text-xs rounded-xl transition shadow-sm">
                        Simpan Akun Admin
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-pemilik-layout>
