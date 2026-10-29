<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-[#5edd5b]/20 text-[#0e1b15]">
                        <svg class="w-4 h-4 text-[#0e1b15]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </span>
                    <span class="text-xs font-bold uppercase tracking-wider text-[#5edd5b]">Manajemen Inventaris</span>
                </div>
                <h2 class="font-extrabold text-2xl text-[#0e1b15] tracking-tight">Perawatan & Servis Alat</h2>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200 px-3 py-1.5 rounded-xl flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    <span>Total Dalam Perawatan: <strong>{{ $totalUnitPerawatan }} Unit</strong></span>
                </span>
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

        @if ($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl text-xs font-semibold shadow-sm">
                <ul class="list-disc pl-4 space-y-1">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- DAFTAR ALAT SEDANG DALAM PERAWATAN (2 COLS) -->
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="font-extrabold text-sm text-[#0e1b15] flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <span>Daftar Unit Sedang Dirawat / Rusak</span>
                        </h3>
                        <span class="text-xs font-bold text-slate-400">{{ $alatPerawatan->count() }} Jenis Alat</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-700">
                            <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[10px] border-b border-slate-200">
                                <tr>
                                    <th class="py-3 px-4">Alat Outdoor</th>
                                    <th class="py-3 px-4 text-center">Unit Dirawat</th>
                                    <th class="py-3 px-4 text-center">Kapasitas Toko</th>
                                    <th class="py-3 px-4 text-right">Aksi Selesai</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                @forelse($alatPerawatan as $alat)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-3.5 px-4">
                                            <div class="font-bold text-[#0e1b15] text-sm">{{ $alat->nama }}</div>
                                            <div class="text-[10px] text-slate-400 font-mono">Kode: {{ $alat->kode_alat }} &bull; {{ $alat->kategori?->nama }}</div>
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-extrabold bg-amber-50 text-amber-700 border border-amber-200">
                                                {{ $alat->stok_perawatan }} Unit
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-center text-slate-500">
                                            {{ $alat->kapasitas_fisik }} / {{ $alat->stok_total }} Unit
                                        </td>
                                        <td class="py-3.5 px-4 text-right">
                                            <form method="POST" action="{{ route('admin.perawatan.selesai', $alat) }}" class="inline-flex items-center gap-1.5 justify-end">
                                                @csrf
                                                @method('PATCH')
                                                <input type="number" name="jumlah" value="1" min="1" max="{{ $alat->stok_perawatan }}"
                                                       class="w-16 py-1 px-2 text-xs font-bold text-center rounded-lg border-slate-200 focus:border-[#5edd5b] focus:ring-[#5edd5b]">
                                                <button type="submit"
                                                        class="inline-flex items-center gap-1 py-1 px-3 bg-[#0e1b15] hover:bg-[#1a2e24] text-[#5edd5b] text-xs font-extrabold rounded-lg transition shadow-sm"
                                                        title="Tandai unit selesai perbaikan dan kembali siap sewa">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    <span>Selesai</span>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-12 text-center text-slate-400">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                </div>
                                                <p class="font-bold text-slate-700 text-sm">Tidak ada alat dalam perawatan</p>
                                                <p class="text-xs text-slate-500 mt-1">Seluruh unit peralatan toko dalam kondisi prima dan siap disewa.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- FORM PINDAHKAN UNIT KE PERAWATAN SECARA MANUAL (1 COL) -->
            <div>
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-4">
                    <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                        <svg class="w-4 h-4 text-[#0e1b15]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="font-extrabold text-sm text-[#0e1b15]">Pindahkan Unit ke Perawatan</h3>
                    </div>

                    <p class="text-xs text-slate-500 leading-relaxed">
                        Gunakan form ini jika menemukan alat toko yang kotor, sobek, atau butuh servis sebelum disewakan kembali.
                    </p>

                    <form id="formPindahPerawatan" method="POST" action="" class="space-y-3">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Alat</label>
                            <select id="pilihAlatSelect" required
                                    class="w-full text-xs font-medium rounded-xl border-slate-200 focus:border-[#5edd5b] focus:ring-[#5edd5b]"
                                    onchange="updateActionUrl(this)">
                                <option value="">-- Pilih Alat Toko --</option>
                                @foreach($semuaAlat as $item)
                                    @php $tersedia = max(0, $item->stok_total - $item->stok_perawatan); @endphp
                                    <option value="{{ $item->id }}" data-url="{{ route('admin.perawatan.tambah', $item) }}" data-max="{{ $tersedia }}">
                                        {{ $item->nama }} (Tersedia: {{ $tersedia }} Unit)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Jumlah Unit</label>
                            <input type="number" id="inputJumlahPindah" name="jumlah" value="1" min="1" required
                                   class="w-full text-xs font-bold rounded-xl border-slate-200 focus:border-[#5edd5b] focus:ring-[#5edd5b]">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Alasan / Catatan Kerusakan</label>
                            <input type="text" name="keterangan" placeholder="Contoh: Perlu cuci tenda atau resleting macet"
                                   class="w-full text-xs font-medium rounded-xl border-slate-200 focus:border-[#5edd5b] focus:ring-[#5edd5b]">
                        </div>

                        <div class="pt-2">
                            <button type="submit"
                                    class="w-full py-2.5 px-4 bg-[#0e1b15] hover:bg-[#1a2e24] text-white text-xs font-bold rounded-xl transition shadow-sm">
                                Pindahkan ke Perawatan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateActionUrl(selectElem) {
            const selectedOption = selectElem.options[selectElem.selectedIndex];
            const url = selectedOption.getAttribute('data-url');
            const max = selectedOption.getAttribute('data-max');
            const form = document.getElementById('formPindahPerawatan');
            const inputJumlah = document.getElementById('inputJumlahPindah');

            if (url) {
                form.action = url;
            }
            if (max) {
                inputJumlah.max = max;
            }
        }
    </script>
</x-admin-layout>
