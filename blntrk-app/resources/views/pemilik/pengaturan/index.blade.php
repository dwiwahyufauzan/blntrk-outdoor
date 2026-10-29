<x-pemilik-layout>
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
                    <span class="text-xs font-bold uppercase tracking-wider text-[#5edd5b]">Pengaturan Toko</span>
                </div>
                <h2 class="font-extrabold text-2xl text-[#0e1b15] tracking-tight">Konfigurasi Operasional & Denda</h2>
            </div>
            <div class="text-xs text-slate-500 bg-white px-3 py-1.5 rounded-lg border border-slate-200 shadow-sm flex items-center gap-1.5">
                <svg class="w-4 h-4 text-[#5edd5b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Hanya dapat diakses oleh Pemilik Toko</span>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl space-y-6">
        @if (session('status'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl flex items-center gap-3 text-sm font-semibold shadow-sm">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('pemilik.pengaturan.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- KARTU 1: IDENTITAS & KONTAK TOKO -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
                    <svg class="w-5 h-5 text-[#0e1b15]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <h3 class="font-extrabold text-base text-[#0e1b15]">Identitas & Kontak Toko</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Toko</label>
                        <input type="text" name="nama_toko" value="{{ old('nama_toko', $pengaturan['nama_toko']) }}"
                               class="w-full text-sm font-medium rounded-xl border-slate-200 focus:border-[#5edd5b] focus:ring-[#5edd5b]">
                        <p class="text-[11px] text-slate-500 mt-1">Dicetak pada kop resi pesanan dan laporan resmi.</p>
                        @error('nama_toko') <span class="text-xs text-rose-500 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nomor WhatsApp Toko</label>
                        <input type="text" name="no_wa_toko" value="{{ old('no_wa_toko', $pengaturan['no_wa_toko']) }}"
                               class="w-full text-sm font-mono rounded-xl border-slate-200 focus:border-[#5edd5b] focus:ring-[#5edd5b]">
                        <p class="text-[11px] text-slate-500 mt-1">Format angka internasional (contoh: 6281234567890).</p>
                        @error('no_wa_toko') <span class="text-xs text-rose-500 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Lengkap Toko</label>
                        <input type="text" name="alamat_toko" value="{{ old('alamat_toko', $pengaturan['alamat_toko']) }}"
                               class="w-full text-sm font-medium rounded-xl border-slate-200 focus:border-[#5edd5b] focus:ring-[#5edd5b]">
                        <p class="text-[11px] text-slate-500 mt-1">Lokasi fisik tempat pengambilan dan pengembalian perlengkapan.</p>
                        @error('alamat_toko') <span class="text-xs text-rose-500 font-semibold">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- KARTU 2: ATURAN RENTAL & PARAMETER DENDA -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
                    <svg class="w-5 h-5 text-[#0e1b15]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="font-extrabold text-base text-[#0e1b15]">Aturan Jadwal, Toleransi & Formula Denda</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Jam Batas Kembali</label>
                        <input type="text" name="jam_kembali" value="{{ old('jam_kembali', $pengaturan['jam_kembali']) }}"
                               placeholder="12:00"
                               class="w-full text-sm font-mono rounded-xl border-slate-200 focus:border-[#5edd5b] focus:ring-[#5edd5b]">
                        <p class="text-[11px] text-slate-500 mt-1">Format jam (HH:mm WIB) jatuh tempo selesai sewa.</p>
                        @error('jam_kembali') <span class="text-xs text-rose-500 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Toleransi Keterlambatan</label>
                        <div class="relative">
                            <input type="number" name="toleransi_menit" value="{{ old('toleransi_menit', $pengaturan['toleransi_menit']) }}"
                                   min="0" max="1440"
                                   class="w-full text-sm font-semibold rounded-xl border-slate-200 focus:border-[#5edd5b] focus:ring-[#5edd5b] pr-16">
                            <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs font-bold text-slate-400">Menit</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">Batas tenggang sebelum sistem mengenakan denda telat.</p>
                        @error('toleransi_menit') <span class="text-xs text-rose-500 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Persentase Denda Harian</label>
                        <div class="relative">
                            <input type="number" step="1" name="persen_denda_harian" value="{{ old('persen_denda_harian', $pengaturan['persen_denda_harian']) }}"
                                   min="0" max="500"
                                   class="w-full text-sm font-semibold rounded-xl border-slate-200 focus:border-[#5edd5b] focus:ring-[#5edd5b] pr-12">
                            <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs font-bold text-slate-400">%</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">Pengali tarif harian sewa (100% = sewa normal per hari).</p>
                        @error('persen_denda_harian') <span class="text-xs text-rose-500 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Maksimum Durasi Sewa</label>
                        <div class="relative">
                            <input type="number" name="maks_durasi_hari" value="{{ old('maks_durasi_hari', $pengaturan['maks_durasi_hari']) }}"
                                   min="1" max="90"
                                   class="w-full text-sm font-semibold rounded-xl border-slate-200 focus:border-[#5edd5b] focus:ring-[#5edd5b] pr-14">
                            <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs font-bold text-slate-400">Hari</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">Batas rentang booking pelanggan pada katalog.</p>
                        @error('maks_durasi_hari') <span class="text-xs text-rose-500 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Ambang Stok Kritis</label>
                        <div class="relative">
                            <input type="number" name="ambang_stok_kritis" value="{{ old('ambang_stok_kritis', $pengaturan['ambang_stok_kritis']) }}"
                                   min="0" max="50"
                                   class="w-full text-sm font-semibold rounded-xl border-slate-200 focus:border-[#5edd5b] focus:ring-[#5edd5b] pr-14">
                            <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs font-bold text-slate-400">Unit</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">Peringatan otomatis saat unit alat tersisa &le; angka ini.</p>
                        @error('ambang_stok_kritis') <span class="text-xs text-rose-500 font-semibold">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-4 p-3.5 bg-amber-50 rounded-xl border border-amber-200/80 flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-amber-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <p class="text-xs text-amber-800 leading-relaxed">
                        <strong>Catatan Integritas Data:</strong> Perubahan aturan denda dan jam kembali hanya berlaku untuk transaksi baru dan perhitungan pengembalian berikutnya. Data pesanan historis yang telah jatuh tempo tidak akan terdistorsi.
                    </p>
                </div>
            </div>

            <!-- TOMBOL SIMPAN -->
            <div class="flex items-center justify-end gap-3">
                <button type="submit"
                        class="inline-flex items-center gap-2 py-3 px-6 bg-[#0e1b15] hover:bg-[#1a2e24] text-white font-extrabold text-sm rounded-xl transition shadow-sm hover:shadow">
                    <svg class="w-4 h-4 text-[#5edd5b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Simpan Perubahan Pengaturan</span>
                </button>
            </div>
        </form>
    </div>
</x-pemilik-layout>
