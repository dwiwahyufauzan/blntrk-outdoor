<x-layouts.admin>
    <x-slot:title>Perawatan & Servis Alat</x-slot:title>

    <div class="mb-7 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#0e1b15] block mb-1">Manajemen Inventaris</span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#14201a] tracking-tight">Perawatan &amp; Servis Alat</h1>
            <p class="text-xs text-stone-500 mt-0.5">Kelola unit perlengkapan toko yang sedang kotor, rusak, atau dalam masa perbaikan teknis</p>
        </div>

        <div class="flex items-center gap-2">
            <span class="text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200 px-3.5 py-2 rounded-xl flex items-center gap-2 shadow-2xs">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                <span>Total Dalam Perawatan: <strong class="font-black text-amber-950">{{ $totalUnitPerawatan }} Unit</strong></span>
            </span>
        </div>
    </div>

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
                                            <form method="POST" action="{{ route('admin.perawatan.selesai', $alat) }}" class="inline-flex items-center gap-1.5 justify-end"
                                                  data-konfirmasi="Selesai Perawatan Alat"
                                                  data-pesan="Apakah Anda yakin ingin menandai unit {{ $alat->nama }} ini telah selesai diperbaiki dan siap disewakan kembali?"
                                                  data-tombol="Ya, Selesai Perbaikan"
                                                  data-tipe="primary">
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

                    <form id="formPindahPerawatan" method="POST" action="" class="space-y-3"
                          data-konfirmasi="Pindahkan ke Perawatan"
                          data-pesan="Apakah Anda yakin ingin memindahkan unit alat ini ke masa perawatan?"
                          data-tombol="Ya, Pindahkan Unit"
                          data-tipe="warning"
                          onsubmit="return validateFormPindah(this, event)">
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


    <div class="mt-6 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100">
            <h3 class="font-extrabold text-sm text-[#0e1b15]">Riwayat Perawatan</h3>
            <p class="text-[11px] text-slate-400">Setiap unit masuk dan selesai diperbaiki tercatat beserta pelaku dan alasannya.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-[10px] uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Waktu</th>
                        <th class="px-4 py-3">Alat</th>
                        <th class="px-4 py-3">Kejadian</th>
                        <th class="px-4 py-3 text-center">Unit</th>
                        <th class="px-4 py-3">Keterangan</th>
                        <th class="px-4 py-3">Petugas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($riwayat as $r)
                        <tr>
                            <td class="px-4 py-3 font-mono text-[11px]">{{ $r->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 font-bold">{{ $r->alat?->nama }}</td>
                            <td class="px-4 py-3">
                                @if($r->jenis === \App\Models\RiwayatPerawatan::MASUK)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800">MASUK PERAWATAN</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">SELESAI DIPERBAIKI</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center font-black">{{ $r->jumlah }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $r->keterangan }}</td>
                            <td class="px-4 py-3">{{ $r->user?->nama ?? 'Sistem' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">Belum ada riwayat perawatan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($riwayat->hasPages())
            <div class="p-4 border-t border-slate-100">{{ $riwayat->links() }}</div>
        @endif
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
            } else {
                form.action = '';
            }

            if (max) {
                inputJumlah.max = max;
                if (parseInt(inputJumlah.value, 10) > parseInt(max, 10)) {
                    inputJumlah.value = max;
                }
            }
        }

        function validateFormPindah(form, event) {
            const select = document.getElementById('pilihAlatSelect');
            if (!select.value || !form.action) {
                if (event) event.preventDefault();
                alert('Silakan pilih alat toko yang akan dipindahkan ke perawatan terlebih dahulu.');
                select.focus();
                return false;
            }
            return true;
        }
    </script>
</x-layouts.admin>
