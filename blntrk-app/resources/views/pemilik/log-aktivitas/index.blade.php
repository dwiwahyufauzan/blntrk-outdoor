<x-pemilik-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-[#5edd5b]/20 text-[#0e1b15]">
                        <svg class="w-4 h-4 text-[#0e1b15]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </span>
                    <span class="text-xs font-bold uppercase tracking-wider text-[#5edd5b]">Audit Trail & Pengawasan</span>
                </div>
                <h2 class="font-extrabold text-2xl text-[#0e1b15] tracking-tight">Log Aktivitas Sistem</h2>
            </div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 bg-white px-3 py-1.5 rounded-lg border border-slate-200 shadow-sm">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <span>Read-Only &bull; Tidak Dapat Dimanipulasi</span>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- FILTER FORM -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
            <form method="GET" action="{{ route('pemilik.log-aktivitas.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Dari Tanggal</label>
                    <input type="date" name="dari" value="{{ $dariInput }}"
                           class="w-full text-xs font-medium rounded-xl border-slate-200 focus:border-[#5edd5b] focus:ring-[#5edd5b]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Sampai Tanggal</label>
                    <input type="date" name="sampai" value="{{ $sampaiInput }}"
                           class="w-full text-xs font-medium rounded-xl border-slate-200 focus:border-[#5edd5b] focus:ring-[#5edd5b]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Pengguna</label>
                    <select name="user_id" class="w-full text-xs font-medium rounded-xl border-slate-200 focus:border-[#5edd5b] focus:ring-[#5edd5b]">
                        <option value="">Semua Pengguna</option>
                        @foreach($daftarUser as $u)
                            <option value="{{ $u->id }}" {{ $userId == $u->id ? 'selected' : '' }}>
                                {{ $u->nama }} ({{ $u->role?->nama ?? 'user' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Modul</label>
                    <select name="modul" class="w-full text-xs font-medium rounded-xl border-slate-200 focus:border-[#5edd5b] focus:ring-[#5edd5b]">
                        <option value="">Semua Modul</option>
                        @foreach($daftarModul as $m)
                            <option value="{{ $m }}" {{ $modul == $m ? 'selected' : '' }}>
                                {{ strtoupper($m) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit"
                            class="flex-1 inline-flex items-center justify-center gap-1.5 py-2 px-3 bg-[#0e1b15] hover:bg-[#1a2e24] text-white text-xs font-bold rounded-xl transition shadow-sm">
                        <svg class="w-3.5 h-3.5 text-[#5edd5b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span>Filter</span>
                    </button>
                    <a href="{{ route('pemilik.log-aktivitas.index') }}"
                       class="inline-flex items-center justify-center py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition"
                       title="Reset Filter">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </a>
                </div>
            </form>
        </div>

        <!-- TABEL LOG AKTIVITAS -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Waktu</th>
                            <th class="py-3 px-4">Pelaku / Pengguna</th>
                            <th class="py-3 px-4">Modul</th>
                            <th class="py-3 px-4">Aksi</th>
                            <th class="py-3 px-4">Deskripsi Rincian</th>
                            <th class="py-3 px-4">Alamat IP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($logs as $log)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-4 whitespace-nowrap text-slate-500 font-mono text-[11px]">
                                    {{ $log->created_at->format('d/m/Y H:i:s') }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    @if($log->user)
                                        <div class="font-bold text-[#0e1b15]">{{ $log->user->nama }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono">{{ $log->user->email }}</div>
                                    @else
                                        <span class="text-slate-400 italic">Sistem / Tamu</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $log->modul }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold font-mono bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        {{ $log->aksi }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-slate-700 leading-relaxed max-w-md">
                                    {{ $log->deskripsi ?? '-' }}
                                    @if($log->ref_id)
                                        <span class="text-[10px] text-slate-400 font-mono">(Ref: #{{ $log->ref_id }})</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap text-slate-400 font-mono text-[11px]">
                                    {{ $log->ip_address ?? '127.0.0.1' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <p class="font-bold text-slate-700 text-sm">Tidak ada catatan log aktivitas</p>
                                        <p class="text-xs text-slate-500 mt-1">Belum ada riwayat aktivitas yang sesuai dengan kriteria filter.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            @if($logs->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</x-pemilik-layout>
