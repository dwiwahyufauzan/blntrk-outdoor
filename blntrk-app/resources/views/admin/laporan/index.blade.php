<x-layouts.admin>
    <x-slot:title>Laporan Operasional & Keuangan - BLNTRK Outdoor</x-slot:title>

    <!-- Header Section -->
    <div class="mb-7 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="text-[10px] font-bold uppercase tracking-widest text-[#4e7a5a] block mb-1">Rekapitulasi Bisnis</span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#14201a] tracking-tight bl-d">Laporan & Arsip Dokumen</h1>
            <p class="text-xs text-stone-500 mt-0.5">Pantau data penyewaan, arus pendapatan kas riil, denda tertagih, dan peringkat alat terlaris</p>
        </div>

        <!-- Tombol Unduh PDF Resmi -->
        @php
            $pdfParams = array_filter([
                'jenis' => $jenis,
                'dari' => $dari->toDateString(),
                'sampai' => $sampai->toDateString(),
                'status' => $status,
            ]);
        @endphp
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.laporan.csv', $pdfParams) }}"
               class="px-3.5 py-2.5 rounded-xl bg-white hover:bg-stone-50 text-emerald-800 border border-emerald-300 text-xs font-extrabold shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer shrink-0">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Unduh Excel (CSV)</span>
            </a>
            <a href="{{ route('admin.laporan.pdf', $pdfParams) }}"
               class="px-4 py-2.5 rounded-xl bg-[#0e1b15] hover:bg-[#1a3327] text-[#5edd5b] text-xs font-extrabold shadow-sm transition inline-flex items-center gap-2 cursor-pointer shrink-0">
                <svg class="w-4 h-4 text-[#5edd5b]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Unduh PDF Resmi</span>
            </a>
        </div>
    </div>

    <!-- Tab 4 Jenis Laporan (Foresta Style) -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 mb-4 scrollbar-none">
        @php
            $tabs = [
                'penyewaan' => 'Laporan Penyewaan',
                'pendapatan' => 'Pendapatan Kas Riil',
                'denda' => 'Denda & Ganti Rugi',
                'terlaris' => 'Alat Terlaris Disewa',
            ];
        @endphp
        @foreach($tabs as $key => $label)
            @php
                $isActive = ($jenis === $key);
                $tabParams = array_filter([
                    'jenis' => $key,
                    'dari' => request('dari', $dari->toDateString()),
                    'sampai' => request('sampai', $sampai->toDateString()),
                ]);
            @endphp
            <a href="{{ route('admin.laporan.index', $tabParams) }}"
               class="px-4 py-2.5 rounded-xl text-xs font-bold shrink-0 transition flex items-center gap-2 {{ $isActive ? 'bg-[#0e1b15] text-[#5edd5b] shadow-xs' : 'bg-white text-stone-600 hover:bg-stone-50 border border-stone-200/90' }}">
                <span>{{ $label }}</span>
            </a>
        @endforeach
    </div>

    <!-- Filter Form: Tanggal Periode & Status -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-stone-200/90 shadow-xs mb-6">
        <form method="GET" action="{{ route('admin.laporan.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
            <input type="hidden" name="jenis" value="{{ $jenis }}">

            <div class="sm:col-span-3">
                <label for="dari" class="block text-[11px] font-bold text-[#14201a] uppercase tracking-wider mb-1.5">Dari Tanggal <span class="text-rose-500">*</span></label>
                <input type="date" id="dari" name="dari" value="{{ $dari->toDateString() }}" required
                       class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs text-[#14201a]">
            </div>

            <div class="sm:col-span-3">
                <label for="sampai" class="block text-[11px] font-bold text-[#14201a] uppercase tracking-wider mb-1.5">Sampai Tanggal <span class="text-rose-500">*</span></label>
                <input type="date" id="sampai" name="sampai" value="{{ $sampai->toDateString() }}" required
                       class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs text-[#14201a]">
            </div>

            @if($jenis === 'penyewaan')
                <div class="sm:col-span-3">
                    <label for="status" class="block text-[11px] font-bold text-[#14201a] uppercase tracking-wider mb-1.5">Filter Status</label>
                    <select id="status" name="status" class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs text-[#14201a]">
                        <option value="">Semua Status</option>
                        @foreach(\App\Enums\StatusPesanan::cases() as $st)
                            <option value="{{ $st->value }}" @selected($status === $st->value)>{{ $st->label() }}</option>
                        @endforeach
                    </select>
                </div>
            @elseif($jenis === 'denda')
                <div class="sm:col-span-3">
                    <label for="status" class="block text-[11px] font-bold text-[#14201a] uppercase tracking-wider mb-1.5">Status Pembayaran Denda</label>
                    <select id="status" name="status" class="w-full text-xs rounded-xl border-stone-300 focus:border-[#0e1b15] focus:ring-[#0e1b15] shadow-xs text-[#14201a]">
                        <option value="">Semua Status Bayar</option>
                        <option value="lunas" @selected($status === 'lunas')>Lunas</option>
                        <option value="belum_lunas" @selected($status === 'belum_lunas')>Belum Lunas</option>
                    </select>
                </div>
            @else
                <div class="sm:col-span-3">
                    <div class="text-[11px] font-medium text-stone-500 pb-2.5">
                        Filter periode bulanan / kustom aktif.
                    </div>
                </div>
            @endif

            <div class="sm:col-span-3 flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-extrabold transition shadow-sm cursor-pointer inline-flex items-center justify-center gap-1.5">
                    <span>Terapkan Filter</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
                <a href="{{ route('admin.laporan.index', ['jenis' => $jenis]) }}" class="px-3.5 py-2.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-600 text-xs font-bold transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- ============================================== -->
    <!-- TAB 1: LAPORAN PENYEWAAN -->
    <!-- ============================================== -->
    @if($jenis === 'penyewaan')
        <!-- Kartu Ringkasan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div class="bg-white p-5 rounded-2xl border border-stone-200/90 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400 block">Total Transaksi Sewa</span>
                    <span class="text-2xl font-black text-[#14201a] block mt-1">{{ $data['total_pesanan'] }} Pesanan</span>
                    <span class="text-[11px] text-stone-400 block mt-0.5">Periode {{ $dari->format('d/m/Y') }} &ndash; {{ $sampai->format('d/m/Y') }}</span>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-[#0e1b15] text-[#5edd5b] flex items-center justify-center shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-stone-200/90 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#4e7a5a] block">Akumulasi Nilai Sewa</span>
                    <span class="text-2xl font-black text-[#14201a] font-mono block mt-1">Rp{{ number_format($data['total_sewa'], 0, ',', '.') }}</span>
                    <span class="text-[11px] text-stone-400 block mt-0.5">Berdasarkan tanggal mulai sewa</span>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-[#5edd5b]/20 text-emerald-900 border border-[#5edd5b]/40 flex items-center justify-center shadow-2xs font-extrabold text-xs">
                    Rp
                </div>
            </div>
        </div>

        <!-- Tabel Data -->
        <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-stone-700">
                    <thead class="bg-stone-50/80 text-stone-500 uppercase text-[10px] font-bold border-b border-stone-200 tracking-wider">
                        <tr>
                            <th class="py-3 px-4 text-center w-12">No</th>
                            <th class="py-3 px-4">No. Resi</th>
                            <th class="py-3 px-4">Penyewa</th>
                            <th class="py-3 px-4">Rentang Jadwal</th>
                            <th class="py-3 px-4 text-center">Durasi</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Total Sewa</th>
                            <th class="py-3 px-4 text-right">Resi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($data['items'] as $index => $item)
                            <tr class="hover:bg-stone-50/60 transition">
                                <td class="py-3 px-4 text-center font-bold text-stone-400">{{ $index + 1 }}</td>
                                <td class="py-3 px-4 font-mono font-bold text-[#14201a]">
                                    <a href="{{ route('admin.pesanan.show', $item->id) }}" class="hover:underline">{{ $item->no_resi }}</a>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-[#14201a]">{{ $item->pelanggan?->nama_lengkap ?? '-' }}</div>
                                    <div class="text-[10px] text-stone-400 font-mono">{{ $item->pelanggan?->no_hp }}</div>
                                </td>
                                <td class="py-3 px-4 text-stone-600">
                                    {{ $item->tanggal_mulai->format('d/m/Y') }} &ndash; {{ $item->tanggal_selesai->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-4 text-center font-bold text-stone-700">{{ $item->durasi_hari }} Hari</td>
                                <td class="py-3 px-4 text-center">
                                    <x-badge-status :status="$item->status" />
                                </td>
                                <td class="py-3 px-4 text-right font-bold text-[#14201a] font-mono">
                                    Rp{{ number_format($item->total_sewa, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <a href="{{ route('admin.pesanan.pdf', $item->id) }}" title="Unduh PDF Resi"
                                       class="px-2.5 py-1 rounded-lg bg-stone-100 hover:bg-[#0e1b15] hover:text-[#5edd5b] text-stone-700 font-bold text-[11px] transition inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span>PDF</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-stone-400 font-medium">
                                    Data laporan tidak tersedia
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    <!-- ============================================== -->
    <!-- TAB 2: LAPORAN PENDAPATAN KAS RIIL -->
    <!-- ============================================== -->
    @elseif($jenis === 'pendapatan')
        <!-- Kartu Finansial 3 Kolom -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white p-5 rounded-2xl border border-stone-200/90 shadow-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400 block">Pendapatan Sewa Riil</span>
                <span class="text-2xl font-black text-[#14201a] font-mono block mt-1">Rp{{ number_format($data['total_pendapatan_sewa'], 0, ',', '.') }}</span>
                <span class="text-[11px] text-stone-400 block mt-0.5">{{ $data['pembayaran_sewa']->count() }} transaksi pembayaran</span>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-stone-200/90 shadow-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700 block">Pendapatan Denda Lunas</span>
                <span class="text-2xl font-black text-[#14201a] font-mono block mt-1">Rp{{ number_format($data['total_pendapatan_denda'], 0, ',', '.') }}</span>
                <span class="text-[11px] text-stone-400 block mt-0.5">{{ $data['denda_lunas']->count() }} kasus denda terlunasi</span>
            </div>

            <div class="bg-[#0e1b15] text-white p-5 rounded-2xl border border-stone-800 shadow-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-[#5edd5b] block">Total Kas Masuk Bersih</span>
                <span class="text-2xl font-black text-white font-mono block mt-1">Rp{{ number_format($data['total_pendapatan_bersih'], 0, ',', '.') }}</span>
                <span class="text-[11px] text-stone-300 block mt-0.5">Sewa murni + Denda lunas di kasir</span>
            </div>
        </div>

        <!-- Tabel 1: Kas Masuk Sewa -->
        <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs overflow-hidden mb-6">
            <div class="p-4 bg-stone-50/70 border-b border-stone-200/80 flex items-center justify-between">
                <h3 class="text-xs font-extrabold text-[#14201a] uppercase tracking-wider">1. Rincian Kas Masuk Pembayaran Sewa</h3>
                <span class="text-xs font-bold text-stone-500">Subtotal: Rp{{ number_format($data['total_pendapatan_sewa'], 0, ',', '.') }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-stone-700">
                    <thead class="bg-stone-50/50 text-stone-500 uppercase text-[10px] font-bold border-b border-stone-200 tracking-wider">
                        <tr>
                            <th class="py-2.5 px-4 text-center w-12">No</th>
                            <th class="py-2.5 px-4">Waktu Bayar</th>
                            <th class="py-2.5 px-4">No. Resi</th>
                            <th class="py-2.5 px-4">Pelanggan</th>
                            <th class="py-2.5 px-4">Metode</th>
                            <th class="py-2.5 px-4 text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($data['pembayaran_sewa'] as $idx => $p)
                            <tr class="hover:bg-stone-50/60 transition">
                                <td class="py-2.5 px-4 text-center font-bold text-stone-400">{{ $idx + 1 }}</td>
                                <td class="py-2.5 px-4 font-mono text-stone-600">{{ $p->dibayar_pada?->format('d/m/Y H:i') }}</td>
                                <td class="py-2.5 px-4 font-mono font-bold text-[#14201a]">
                                    <a href="{{ route('admin.pesanan.show', $p->pesanan_id) }}" class="hover:underline">{{ $p->pesanan?->no_resi ?? '-' }}</a>
                                </td>
                                <td class="py-2.5 px-4 font-bold text-stone-800">{{ $p->pesanan?->pelanggan?->nama_lengkap ?? '-' }}</td>
                                <td class="py-2.5 px-4">{{ $p->metode->label() }}</td>
                                <td class="py-2.5 px-4 text-right font-mono font-bold text-[#14201a]">
                                    Rp{{ number_format($p->jumlah, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-stone-400 font-medium">
                                    Data laporan tidak tersedia
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tabel 2: Kas Masuk Denda Lunas -->
        <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs overflow-hidden">
            <div class="p-4 bg-stone-50/70 border-b border-stone-200/80 flex items-center justify-between">
                <h3 class="text-xs font-extrabold text-[#14201a] uppercase tracking-wider">2. Rincian Kas Masuk Denda & Ganti Rugi (Lunas)</h3>
                <span class="text-xs font-bold text-stone-500">Subtotal: Rp{{ number_format($data['total_pendapatan_denda'], 0, ',', '.') }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-stone-700">
                    <thead class="bg-stone-50/50 text-stone-500 uppercase text-[10px] font-bold border-b border-stone-200 tracking-wider">
                        <tr>
                            <th class="py-2.5 px-4 text-center w-12">No</th>
                            <th class="py-2.5 px-4">Waktu Lunas</th>
                            <th class="py-2.5 px-4">No. Resi</th>
                            <th class="py-2.5 px-4">Pelanggan</th>
                            <th class="py-2.5 px-4">Jenis Denda</th>
                            <th class="py-2.5 px-4 text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($data['denda_lunas'] as $idx => $d)
                            <tr class="hover:bg-stone-50/60 transition">
                                <td class="py-2.5 px-4 text-center font-bold text-stone-400">{{ $idx + 1 }}</td>
                                <td class="py-2.5 px-4 font-mono text-stone-600">{{ ($d->dilunasi_pada ?? $d->created_at)->format('d/m/Y H:i') }}</td>
                                <td class="py-2.5 px-4 font-mono font-bold text-[#14201a]">
                                    <a href="{{ route('admin.pesanan.show', $d->pesanan_id) }}" class="hover:underline">{{ $d->pesanan?->no_resi ?? '-' }}</a>
                                </td>
                                <td class="py-2.5 px-4 font-bold text-stone-800">{{ $d->pesanan?->pelanggan?->nama_lengkap ?? '-' }}</td>
                                <td class="py-2.5 px-4">{{ $d->jenis === \App\Enums\JenisDenda::Keterlambatan ? 'Keterlambatan' : 'Kerusakan/Hilang' }}</td>
                                <td class="py-2.5 px-4 text-right font-mono font-bold text-[#14201a]">
                                    Rp{{ number_format($d->jumlah, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-stone-400 font-medium">
                                    Data laporan tidak tersedia
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    <!-- ============================================== -->
    <!-- TAB 3: LAPORAN DENDA & GANTI RUGI -->
    <!-- ============================================== -->
    @elseif($jenis === 'denda')
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white p-5 rounded-2xl border border-stone-200/90 shadow-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400 block">Total Denda Tercatat</span>
                <span class="text-2xl font-black text-[#14201a] font-mono block mt-1">Rp{{ number_format($data['total_denda'], 0, ',', '.') }}</span>
                <span class="text-[11px] text-stone-400 block mt-0.5">{{ $data['jumlah_kasus'] }} kasus keterlambatan/kerusakan</span>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-stone-200/90 shadow-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 block">Denda Telah Dilunasi</span>
                <span class="text-2xl font-black text-emerald-800 font-mono block mt-1">Rp{{ number_format($data['total_lunas'], 0, ',', '.') }}</span>
                <span class="text-[11px] text-emerald-700 block mt-0.5">Sudah masuk kasir toko</span>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-stone-200/90 shadow-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-rose-700 block">Tertunggak (Belum Lunas)</span>
                <span class="text-2xl font-black text-rose-700 font-mono block mt-1">Rp{{ number_format($data['total_belum_lunas'], 0, ',', '.') }}</span>
                <span class="text-[11px] text-rose-600 block mt-0.5">Menunggu pelunasan penyewa</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-stone-700">
                    <thead class="bg-stone-50/80 text-stone-500 uppercase text-[10px] font-bold border-b border-stone-200 tracking-wider">
                        <tr>
                            <th class="py-3 px-4 text-center w-12">No</th>
                            <th class="py-3 px-4">Tanggal Catat</th>
                            <th class="py-3 px-4">No. Resi</th>
                            <th class="py-3 px-4">Penyewa</th>
                            <th class="py-3 px-4">Kasus Denda</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($data['items'] as $index => $item)
                            <tr class="hover:bg-stone-50/60 transition">
                                <td class="py-3 px-4 text-center font-bold text-stone-400">{{ $index + 1 }}</td>
                                <td class="py-3 px-4 font-mono text-stone-600">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                <td class="py-3 px-4 font-mono font-bold text-[#14201a]">
                                    <a href="{{ route('admin.pesanan.show', $item->pesanan_id) }}" class="hover:underline">{{ $item->pesanan?->no_resi ?? '-' }}</a>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-[#14201a]">{{ $item->pesanan?->pelanggan?->nama_lengkap ?? '-' }}</div>
                                    <div class="text-[10px] text-stone-400 font-mono">{{ $item->pesanan?->pelanggan?->no_hp }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-stone-900">{{ $item->jenis === \App\Enums\JenisDenda::Keterlambatan ? 'Keterlambatan' : 'Kerusakan/Kehilangan' }}</div>
                                    <div class="text-[11px] text-stone-500">{{ $item->keterangan }}</div>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($item->status_bayar === 'lunas')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-[#5edd5b]/20 text-emerald-900 border border-[#5edd5b]/40">
                                            LUNAS
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-900 border border-rose-300">
                                            BELUM LUNAS
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-[#14201a]">
                                    Rp{{ number_format($item->jumlah, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-stone-400 font-medium">
                                    Data laporan tidak tersedia
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    <!-- ============================================== -->
    <!-- TAB 4: LAPORAN ALAT TERLARIS -->
    <!-- ============================================== -->
    @elseif($jenis === 'terlaris')
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div class="bg-white p-5 rounded-2xl border border-stone-200/90 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400 block">Total Unit Disewa</span>
                    <span class="text-2xl font-black text-[#14201a] block mt-1">{{ $data['total_unit_disewa'] }} Unit Alat</span>
                    <span class="text-[11px] text-stone-400 block mt-0.5">Top perlengkapan paling diminati</span>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-[#0e1b15] text-[#5edd5b] flex items-center justify-center shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-stone-200/90 shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#4e7a5a] block">Total Omzet Dari Alat</span>
                    <span class="text-2xl font-black text-[#14201a] font-mono block mt-1">Rp{{ number_format($data['total_omzet_alat'], 0, ',', '.') }}</span>
                    <span class="text-[11px] text-stone-400 block mt-0.5">Kontribusi langsung perlengkapan</span>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-[#5edd5b]/20 text-emerald-900 border border-[#5edd5b]/40 flex items-center justify-center shadow-2xs font-extrabold text-xs">
                    Rp
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-stone-700">
                    <thead class="bg-stone-50/80 text-stone-500 uppercase text-[10px] font-bold border-b border-stone-200 tracking-wider">
                        <tr>
                            <th class="py-3 px-4 text-center w-14">Rank</th>
                            <th class="py-3 px-4">Perlengkapan Outdoor</th>
                            <th class="py-3 px-4">Kategori</th>
                            <th class="py-3 px-4 text-center">Tarif / Hari</th>
                            <th class="py-3 px-4 text-center">Unit Disewa</th>
                            <th class="py-3 px-4 text-right">Total Perolehan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($data['items'] as $index => $item)
                            <tr class="hover:bg-stone-50/60 transition">
                                <td class="py-3 px-4 text-center">
                                    <span class="w-7 h-7 rounded-xl flex items-center justify-center font-black mx-auto {{ $index === 0 ? 'bg-amber-100 text-amber-900 border border-amber-300' : ($index === 1 ? 'bg-stone-200 text-stone-800' : ($index === 2 ? 'bg-amber-50 text-amber-800' : 'text-stone-400')) }}">
                                        #{{ $index + 1 }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-extrabold text-[#14201a] text-sm">{{ $item->alat?->nama ?? 'Alat Dihapus' }}</div>
                                    <span class="text-[10px] text-stone-400 font-mono">{{ $item->alat?->kode_alat }}</span>
                                </td>
                                <td class="py-3 px-4 text-stone-600">{{ $item->alat?->kategori?->nama ?? '-' }}</td>
                                <td class="py-3 px-4 text-center font-mono text-stone-700">
                                    Rp{{ number_format($item->alat?->harga_per_hari ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-center font-black text-stone-900 text-sm">
                                    {{ $item->total_disewa }} unit
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-black text-[#14201a] text-sm">
                                    Rp{{ number_format($item->total_pendapatan, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-stone-400 font-medium">
                                    Data laporan tidak tersedia
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</x-layouts.admin>
