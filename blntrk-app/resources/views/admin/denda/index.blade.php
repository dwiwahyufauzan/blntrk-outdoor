<x-layouts.admin>
    <x-slot:title>Daftar Denda & Ganti Rugi - BLNTRK Outdoor</x-slot:title>

    <!-- Header -->
    <div class="mb-7 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="text-[10px] font-bold uppercase tracking-widest text-[#4e7a5a] block mb-1">Manajemen Denda & Ganti Rugi</span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#14201a] tracking-tight bl-d">Denda & Ganti Rugi</h1>
            <p class="text-xs text-stone-500 mt-0.5">Pantau penyewa terlambat, estimasi denda berjalan otomatis, serta kelola ganti rugi kerusakan dan kehilangan alat</p>
        </div>
    </div>

    <!-- Alert Notifikasi Status -->
    @if(session('status'))
        <div class="mb-6 p-4 rounded-2xl bg-[#0e1b15] text-[#5edd5b] border border-[#1e3a2c] text-xs font-bold flex items-center gap-3">
            <svg class="w-5 h-5 text-[#5edd5b] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- Kartu Statistik Ringkasan Denda -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-7">
        <!-- Kartu 1: Total Denda Tertunggak -->
        <div class="bg-white p-5 rounded-2xl border border-stone-200/90 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-rose-600 block mb-1">Total Denda Tertunggak</span>
                <div class="text-xl sm:text-2xl font-black font-mono text-rose-600">
                    Rp{{ number_format($grandTotalTertunggak, 0, ',', '.') }}
                </div>
                <div class="text-[11px] text-stone-500 font-medium mt-0.5">
                    <span>Rp{{ number_format($totalBelumLunas, 0, ',', '.') }} tercatat</span>
                    <span class="text-stone-300 mx-1">•</span>
                    <span>+Rp{{ number_format($totalEstimasiDendaBerjalan, 0, ',', '.') }} denda berjalan</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <!-- Kartu 2: Penyewa Sedang Terlambat -->
        <div class="bg-white p-5 rounded-2xl border border-stone-200/90 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700 block mb-1">Penyewa Sedang Terlambat</span>
                <div class="text-xl sm:text-2xl font-black font-mono text-[#0e1b15]">
                    {{ $pesananTerlambatList->count() }} <span class="text-sm font-semibold text-stone-500 font-sans">Penyewa</span>
                </div>
                <div class="text-[11px] text-stone-500 font-medium mt-0.5">
                    @if($pesananTerlambatList->isNotEmpty())
                        <span class="text-amber-800 font-bold">Unit belum diserahkan ke toko</span>
                    @else
                        <span class="text-emerald-700 font-bold">Semua unit tepat waktu</span>
                    @endif
                </div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-200 text-amber-700 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <!-- Kartu 3: Total Denda Telah Dilunasi -->
        <div class="bg-white p-5 rounded-2xl border border-stone-200/90 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 block mb-1">Total Denda Telah Dilunasi</span>
                <div class="text-xl sm:text-2xl font-black font-mono text-[#0e1b15]">
                    Rp{{ number_format($totalLunas, 0, ',', '.') }}
                </div>
                <span class="text-[11px] text-stone-500 font-medium mt-0.5 block">{{ $countLunas }} Tagihan Berhasil Dilunasi</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>

    <!-- SEKSI 1: PENYEWA SEDANG TERLAMBAT (DENDA KETERLAMBATAN BERJALAN AKTIF) -->
    <div class="mb-8">
        <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-stone-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-stone-50/60">
                <div class="flex items-center gap-2.5">
                    @if($pesananTerlambatList->isNotEmpty())
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-600"></span>
                        </span>
                    @else
                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                    @endif
                    <div>
                        <h2 class="text-sm font-extrabold text-[#14201a] bl-d flex items-center gap-2">
                            <span>Penyewa Sedang Terlambat</span>
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $pesananTerlambatList->isNotEmpty() ? 'bg-rose-100 text-rose-800 font-mono font-bold' : 'bg-emerald-100 text-emerald-800' }}">
                                {{ $pesananTerlambatList->count() }} Aktif
                            </span>
                        </h2>
                        <p class="text-[11px] text-stone-500 mt-0.5">Unit perlengkapan masih di tangan penyewa melebihi batas waktu 12:00 WIB. Denda keterlambatan berjalan terhitung otomatis per 24 jam.</p>
                    </div>
                </div>

                @if($pesananTerlambatList->isNotEmpty())
                    <div class="text-xs text-rose-900 bg-rose-50 px-3 py-1.5 rounded-xl border border-rose-200 font-semibold flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Estimasi Denda Berjalan: <strong class="font-mono text-rose-950 font-black">Rp{{ number_format($totalEstimasiDendaBerjalan, 0, ',', '.') }}</strong></span>
                    </div>
                @endif
            </div>

            @if($pesananTerlambatList->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-stone-700">
                        <thead class="bg-stone-50/90 text-stone-500 uppercase text-[10px] font-bold border-b border-stone-200 tracking-wider">
                            <tr>
                                <th class="py-3 px-5">No. Resi & Jadwal</th>
                                <th class="py-3 px-4">Penyewa & Kontak</th>
                                <th class="py-3 px-4 text-center">Durasi Terlambat</th>
                                <th class="py-3 px-4 text-right">Tarif / Hari</th>
                                <th class="py-3 px-4 text-right">Denda Berjalan</th>
                                <th class="py-3 px-4 text-center">Jaminan Fisik</th>
                                <th class="py-3 px-5 text-right">Aksi Cepat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @foreach($pesananTerlambatList as $p)
                                <tr class="hover:bg-rose-50/30 transition">
                                    <td class="py-3.5 px-5">
                                        <div class="font-mono font-bold text-sm text-[#0e1b15]">
                                            <a href="{{ route('admin.pesanan.show', $p->id) }}" class="hover:underline">
                                                {{ $p->no_resi }}
                                            </a>
                                        </div>
                                        <div class="text-[11px] text-stone-500 mt-0.5">
                                            Batas: <span class="font-medium text-rose-700">{{ $p->tanggal_selesai->format('d/m/Y') }} 12:00 WIB</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-[#14201a]">{{ $p->pelanggan->nama_lengkap }}</div>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="font-mono text-[11px] text-stone-500">{{ $p->pelanggan->no_hp }}</span>
                                            <a href="{{ wa_link($p->pelanggan->no_hp, \App\Services\WhatsAppService::pesanPeringatanTerlambat($p, $p->info_telat['hari'], $p->info_telat['jumlah'])) }}"
                                               target="_blank"
                                               class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-2 py-0.5 rounded-md border border-emerald-200 transition"
                                               title="Hubungi dan ingatkan denda keterlambatan via WhatsApp">
                                                <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.173.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.159.57 4.184 1.564 5.938l-1.564 5.714 5.861-1.537c1.7 1.006 3.684 1.585 5.807 1.585 6.627 0 12-5.373 12-12 0-6.627-5.373-12-12-12z"/></svg>
                                                <span>Chat WA</span>
                                            </a>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-rose-100 text-rose-900 border border-rose-300 inline-block animate-pulse">
                                            Terlambat {{ $p->info_telat['hari'] }} Hari
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono text-stone-600">
                                        Rp{{ number_format($p->info_telat['tarif_harian'], 0, ',', '.') }}/hari
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="font-mono font-black text-rose-600 text-sm">
                                            Rp{{ number_format($p->info_telat['jumlah'], 0, ',', '.') }}
                                        </div>
                                        <span class="text-[10px] text-stone-400 font-medium">otomatis bertambah</span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if($p->jaminan)
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-900 border border-amber-200">
                                                {{ $p->jaminan->jenis->label() }} (Ditahan)
                                            </span>
                                        @else
                                            <span class="text-[10px] text-stone-400 italic">Belum Ada</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-5 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.pesanan.pengembalian.create', $p->id) }}"
                                               class="px-3 py-1.5 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-extrabold transition shadow-2xs inline-flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                <span>Proses Kembali</span>
                                            </a>
                                            <a href="{{ route('admin.pesanan.show', $p->id) }}"
                                               class="px-2.5 py-1.5 rounded-xl bg-stone-100 hover:bg-[#0e1b15] hover:text-[#5edd5b] text-stone-700 font-bold text-[11px] transition inline-flex items-center gap-1 shadow-2xs">
                                                <span>Detail</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-8 text-center text-xs text-stone-500">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center mx-auto mb-2.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="font-extrabold text-[#0e1b15] block text-sm">Tidak Ada Penyewa Terlambat</span>
                    <span class="text-stone-400 mt-0.5 block">Seluruh unit sewa yang sedang berjalan masih berada dalam jadwal waktu pengembalian yang aman.</span>
                </div>
            @endif
        </div>
    </div>

    <!-- SEKSI 2: RIWAYAT DENDA & GANTI RUGI TERCATAT -->
    <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div>
            <h2 class="text-base font-extrabold text-[#14201a] bl-d">Riwayat Denda & Ganti Rugi Tercatat</h2>
            <p class="text-xs text-stone-500">Daftar denda yang telah dibukukan saat serah terima pengembalian alat atau ditambahkan manual oleh admin</p>
        </div>
    </div>

    <!-- Tab Filter Status Bayar -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 mb-4 scrollbar-none">
        <a href="{{ route('admin.denda.index', array_filter(['q' => request('q'), 'jenis' => request('jenis')])) }}"
           class="px-3.5 py-2 rounded-xl text-xs font-bold shrink-0 transition flex items-center gap-2 {{ empty(request('status_bayar')) ? 'bg-[#0e1b15] text-[#5edd5b] shadow-xs' : 'bg-white text-stone-600 hover:bg-stone-50 border border-stone-200/90' }}">
            <span>Semua</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ empty(request('status_bayar')) ? 'bg-[#5edd5b]/20 text-[#5edd5b]' : 'bg-stone-100 text-stone-500' }}">{{ $countSemua }}</span>
        </a>

        <a href="{{ route('admin.denda.index', array_filter(['status_bayar' => 'belum_lunas', 'q' => request('q'), 'jenis' => request('jenis')])) }}"
           class="px-3.5 py-2 rounded-xl text-xs font-bold shrink-0 transition flex items-center gap-2 {{ request('status_bayar') === 'belum_lunas' ? 'bg-[#0e1b15] text-[#5edd5b] shadow-xs' : 'bg-white text-stone-600 hover:bg-stone-50 border border-stone-200/90' }}">
            <span>Belum Lunas</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ request('status_bayar') === 'belum_lunas' ? 'bg-[#5edd5b]/20 text-[#5edd5b]' : 'bg-stone-100 text-stone-500' }}">{{ $countBelumLunas }}</span>
        </a>

        <a href="{{ route('admin.denda.index', array_filter(['status_bayar' => 'lunas', 'q' => request('q'), 'jenis' => request('jenis')])) }}"
           class="px-3.5 py-2 rounded-xl text-xs font-bold shrink-0 transition flex items-center gap-2 {{ request('status_bayar') === 'lunas' ? 'bg-[#0e1b15] text-[#5edd5b] shadow-xs' : 'bg-white text-stone-600 hover:bg-stone-50 border border-stone-200/90' }}">
            <span>Sudah Lunas</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ request('status_bayar') === 'lunas' ? 'bg-[#5edd5b]/20 text-[#5edd5b]' : 'bg-stone-100 text-stone-500' }}">{{ $countLunas }}</span>
        </a>
    </div>

    <!-- Filter Pencarian -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-stone-200/90 shadow-xs mb-6">
        <form method="GET" action="{{ route('admin.denda.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            @if(request('status_bayar'))
                <input type="hidden" name="status_bayar" value="{{ request('status_bayar') }}">
            @endif

            <div class="sm:col-span-2">
                <label for="q" class="block text-[11px] font-bold text-[#14201a] uppercase tracking-wider mb-1.5">Pencarian Resi, Pelanggan, atau Keterangan</label>
                <div class="relative">
                    <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Cari nomor resi atau nama penyewa..."
                        class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs text-[#14201a] pl-9">
                    <svg class="w-4 h-4 text-stone-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <div>
                <label for="jenis" class="block text-[11px] font-bold text-[#14201a] uppercase tracking-wider mb-1.5">Jenis Denda</label>
                <select id="jenis" name="jenis" class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs text-[#14201a]">
                    <option value="">-- Semua Jenis --</option>
                    <option value="keterlambatan" {{ request('jenis') === 'keterlambatan' ? 'selected' : '' }}>Denda Keterlambatan</option>
                    <option value="kerusakan" {{ request('jenis') === 'kerusakan' ? 'selected' : '' }}>Biaya Kerusakan / Kehilangan</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-extrabold transition shadow-sm cursor-pointer inline-flex items-center justify-center gap-1.5">
                    <span>Filter</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
                @if(request()->hasAny(['q', 'status_bayar', 'jenis']))
                    <a href="{{ route('admin.denda.index') }}" class="px-3.5 py-2.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-600 text-xs font-bold transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Daftar Denda -->
    <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-stone-700">
                <thead class="bg-stone-50/80 text-stone-500 uppercase text-[10px] font-bold border-b border-stone-200 tracking-wider">
                    <tr>
                        <th class="py-3 px-5 w-12 text-center">No</th>
                        <th class="py-3 px-4">No. Resi</th>
                        <th class="py-3 px-4">Penyewa</th>
                        <th class="py-3 px-4">Jenis & Keterangan</th>
                        <th class="py-3 px-4 text-right">Nominal</th>
                        <th class="py-3 px-4 text-center">Status Bayar</th>
                        <th class="py-3 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($dendaList as $index => $d)
                        <tr class="hover:bg-stone-50/60 transition">
                            <td class="py-3.5 px-5 text-center font-bold text-stone-400">
                                {{ $dendaList->firstItem() + $index }}
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold text-[#14201a]">
                                <a href="{{ route('admin.pesanan.show', $d->pesanan_id) }}" class="hover:underline text-[#0e1b15]">
                                    {{ $d->pesanan->no_resi }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-[#14201a] bl-d">{{ $d->pesanan->pelanggan->nama_lengkap }}</div>
                                <div class="text-[10px] text-stone-400 font-mono">{{ $d->pesanan->pelanggan->no_hp }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold {{ $d->jenis->value === 'keterlambatan' ? 'bg-amber-100 text-amber-900' : 'bg-rose-100 text-rose-900' }}">
                                        {{ $d->jenis->label() }}
                                    </span>
                                </div>
                                <span class="text-xs text-stone-600 block">{{ $d->keterangan }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-bold text-base text-[#14201a] font-mono">
                                Rp{{ number_format($d->jumlah, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($d->status_bayar === 'lunas')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        Lunas
                                    </span>
                                    <div class="text-[9px] text-stone-400 font-mono mt-0.5">
                                        {{ $d->dilunasi_pada?->format('d/m/Y H:i') }}
                                    </div>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-rose-100 text-rose-900 border border-rose-300">
                                        Belum Lunas
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($d->status_bayar !== 'lunas')
                                        <form method="POST" action="{{ route('admin.denda.lunas', $d->id) }}"
                                              data-konfirmasi="primary"
                                              data-judul="Konfirmasi Pelunasan Denda"
                                              data-pesan="Tandai lunas denda Rp{{ number_format($d->jumlah, 0, ',', '.') }} untuk pesanan [{{ $d->pesanan->no_resi }}]?"
                                              data-tombol="Ya, Tandai Lunas">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-extrabold transition shadow-2xs cursor-pointer inline-flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                <span>Tandai Lunas</span>
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('admin.pesanan.show', $d->pesanan_id) }}"
                                       class="px-2.5 py-1.5 rounded-xl bg-stone-100 hover:bg-[#0e1b15] hover:text-[#5edd5b] text-stone-700 font-bold text-[11px] transition inline-flex items-center gap-1 shadow-2xs">
                                        <span>Resi</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-stone-400">
                                <svg class="w-8 h-8 mx-auto mb-2 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Tidak ada data denda atau ganti rugi yang cocok.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($dendaList->hasPages())
            <div class="p-4 border-t border-stone-200/80 bg-stone-50/40">
                {{ $dendaList->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
