<x-layouts.admin>
    <x-slot:title>Detail Pelanggan - {{ $pelanggan->nama_lengkap }}</x-slot:title>

    <div class="mb-7 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('admin.pelanggan.index') }}" class="text-xs font-bold text-stone-600 hover:text-black transition flex items-center gap-1.5 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Daftar Pelanggan</span>
            </a>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#14201a] tracking-tight">{{ $pelanggan->nama_lengkap }}</h1>
            <p class="text-xs text-stone-500 mt-0.5">ID Pelanggan: #{{ $pelanggan->id }} • Terdaftar sejak {{ $pelanggan->created_at?->format('d M Y') }}</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.pelanggan.edit', $pelanggan->id) }}" class="px-4 py-2.5 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-extrabold transition shadow-sm cursor-pointer inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                <span>Edit Data</span>
            </a>
        </div>
    </div>

    <!-- Informasi Profil Pelanggan -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 sm:p-7 rounded-2xl border border-stone-200/90 shadow-xs flex flex-col justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400 block mb-1">Tipe Pelanggan</span>
                <div class="mb-4">
                    @if($pelanggan->is_walk_in)
                        <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-[#5edd5b]/20 text-emerald-900 border border-[#5edd5b]/40">
                            Penyewa Walk-In (Langsung di Toko)
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-stone-100 text-stone-700 border border-stone-200">
                            Pelanggan Terdaftar (Akun Daring)
                        </span>
                    @endif
                </div>

                <div class="space-y-3.5 text-xs">
                    <div>
                        <span class="text-stone-400 block text-[11px] font-semibold">Nomor WhatsApp:</span>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $pelanggan->no_hp) }}" target="_blank" class="font-mono font-bold text-emerald-700 hover:underline flex items-center gap-1.5 mt-0.5">
                            <span>{{ $pelanggan->no_hp }}</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    </div>

                    <div>
                        <span class="text-stone-400 block text-[11px] font-semibold">Akun Email Login:</span>
                        <span class="font-medium text-[#14201a] mt-0.5 block">{{ $pelanggan->user?->email ?? '(Tidak memiliki akun login)' }}</span>
                    </div>

                    <div>
                        <span class="text-stone-400 block text-[11px] font-semibold">Alamat Domisili:</span>
                        <p class="text-stone-700 leading-relaxed mt-0.5 font-medium">{{ $pelanggan->alamat }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metrik Singkat Pelanggan -->
        <div class="md:col-span-2 bg-white p-6 sm:p-7 rounded-2xl border border-stone-200/90 shadow-xs flex flex-col justify-between">
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-stone-500 mb-4 pb-2 border-b border-stone-100">
                    Statistik Riwayat Rental
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-4 rounded-xl bg-emerald-50/70 border border-emerald-200/60">
                        <span class="text-[10px] font-bold text-emerald-800 uppercase block">Total Transaksi</span>
                        <span class="text-3xl font-extrabold text-[#14201a] block mt-1">{{ $pelanggan->pesanan->count() }}</span>
                        <span class="text-[11px] text-emerald-700 block mt-0.5 font-medium">pesanan sewa</span>
                    </div>

                    <div class="p-4 rounded-xl bg-blue-50/70 border border-blue-200/60">
                        <span class="text-[10px] font-bold text-blue-800 uppercase block">Total Belanja Sewa</span>
                        <span class="text-xl font-extrabold text-[#14201a] block mt-1">
                            Rp {{ number_format($pelanggan->pesanan->sum('total_sewa'), 0, ',', '.') }}
                        </span>
                        <span class="text-[11px] text-blue-700 block mt-0.5 font-medium">akumulasi rental</span>
                    </div>

                    <div class="p-4 rounded-xl bg-stone-50 border border-stone-200">
                        <span class="text-[10px] font-bold text-stone-600 uppercase block">Status Akun</span>
                        <span class="text-sm font-extrabold text-[#14201a] block mt-2">
                            {{ $pelanggan->user?->is_aktif ? 'Aktif' : ($pelanggan->user ? 'Nonaktif' : 'Walk-In') }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-stone-100 text-right mt-4">
                <a href="{{ url('/admin/pesanan?pelanggan_id=' . $pelanggan->id) }}" class="text-xs font-bold text-[#0e1b15] hover:text-[#5edd5b] inline-flex items-center justify-end gap-1 transition">
                    <span>Buka Riwayat Lengkap</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Tabel Riwayat Pesanan -->
    <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-stone-200/80 bg-stone-50/50">
            <h2 class="text-sm font-extrabold text-[#14201a]">Riwayat Pesanan Rental Terakhir</h2>
            <p class="text-xs text-stone-500">Maksimal 10 transaksi penyewaan terakhir oleh pelanggan ini</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-stone-700">
                <thead class="bg-stone-50/80 text-stone-500 uppercase text-[10px] font-bold border-b border-stone-200 tracking-wider">
                    <tr>
                        <th class="py-3 px-5">Kode Resi</th>
                        <th class="py-3 px-4">Jadwal Sewa</th>
                        <th class="py-3 px-4">Total Biaya</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($pelanggan->pesanan as $pesanan)
                        <tr class="hover:bg-stone-50/60 transition">
                            <td class="py-3.5 px-5 font-mono font-bold text-[#14201a]">
                                {{ $pesanan->no_resi }}
                            </td>
                            <td class="py-3.5 px-4 font-medium text-stone-800">
                                {{ $pesanan->tanggal_mulai?->format('d/m/Y') }} &ndash; {{ $pesanan->tanggal_selesai?->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-4 font-extrabold text-[#14201a]">
                                Rp {{ number_format($pesanan->total_sewa, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4">
                                <x-badge-status :status="$pesanan->status" />
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                <a href="{{ url('/admin/pesanan/' . $pesanan->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-stone-100 hover:bg-[#5edd5b] hover:text-black text-stone-700 font-bold text-[11px] transition">
                                    <span>Detail</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-stone-400">
                                <div class="w-10 h-10 rounded-2xl bg-stone-100 text-stone-400 mx-auto flex items-center justify-center mb-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                Pelanggan ini belum memiliki riwayat pesanan sewa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>

