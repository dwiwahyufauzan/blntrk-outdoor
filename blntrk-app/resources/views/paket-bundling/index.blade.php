<x-layouts.publik>
    <x-slot:title>Paket Bundling Hemat Sewa Alat Outdoor - BLNTRK Outdoor Subang</x-slot:title>

    <div class="bg-[#f4f5f6] min-h-screen py-10 sm:py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- HERO HEADER PAKET BUNDLING (FORESTA ADVENTURE STYLE) -->
            <div class="relative overflow-hidden rounded-[28px] sm:rounded-[36px] bg-[#0e1b15] text-white p-8 sm:p-14 shadow-xl mb-12">
                <div class="relative z-10 max-w-2xl">
                    <span class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-[#5edd5b] text-black mb-3 shadow-sm">
                        <svg class="w-3.5 h-3.5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Paling Praktis &amp; Hemat</span>
                    </span>
                    <h1 class="text-3xl sm:text-5xl font-black font-urbanist text-white tracking-tight leading-tight mb-4">
                        Paket Bundling Outdoor <br class="hidden sm:inline">Pilihan Paket 1, 2, &amp; 3
                    </h1>
                    <p class="text-xs sm:text-sm text-stone-300 leading-relaxed mb-6">
                        Pilihan paket bundling lengkap yang sudah dikurasi sesuai kebutuhan pendakian dan camping Anda. Tidak perlu repot memilih alat satu per satu dan nikmati <strong>potongan tarif sewa harian hingga 20%</strong> lebih hemat!
                    </p>
                    <div class="flex flex-wrap items-center gap-3 text-xs font-semibold text-stone-300">
                        <span class="inline-flex items-center gap-1.5 bg-white/10 px-3.5 py-1.5 rounded-full border border-white/15">
                            <svg class="w-4 h-4 text-[#5edd5b]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Langsung Masuk Keranjang / Formulir</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 bg-white/10 px-3.5 py-1.5 rounded-full border border-white/15">
                            <svg class="w-4 h-4 text-[#5edd5b]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Bayar di Toko Subang (Buka 24 Jam)</span>
                        </span>
                    </div>
                </div>

                <div class="absolute right-0 bottom-0 top-0 w-1/3 opacity-10 hidden lg:flex items-center justify-center pointer-events-none">
                    <svg class="w-96 h-96 text-white" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M14 6l4 8h-3l2 4H7l4-7 2 3 1-2v-6zm0-4L9 11l-3-5L1 18h22L14 2z"/>
                    </svg>
                </div>
            </div>

            <!-- Flash Alert -->
            @if(session('status'))
                <div class="mb-8 p-4 rounded-2xl bg-[#0e1b15] text-[#5edd5b] text-xs font-bold border border-[#1e3a2c] shadow-xs flex items-center gap-3">
                    <svg class="w-5 h-5 text-[#5edd5b] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- GRID 3 KARTU PAKET BUNDLING (PAKET 1, 2, 3) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch mb-16">
                @foreach($paketList as $paket)
                    @php
                        $nomorBadgeClass = match($paket->nomor_paket) {
                            1 => 'bg-emerald-100 text-emerald-900 border-emerald-300',
                            2 => 'bg-[#5edd5b] text-black font-black',
                            3 => 'bg-amber-100 text-amber-900 border-amber-300',
                            default => 'bg-stone-100 text-stone-800'
                        };
                    @endphp

                    <div class="bg-white rounded-3xl border border-stone-200/90 shadow-sm hover:shadow-xl transition-all duration-300 p-6 sm:p-7 flex flex-col justify-between group relative overflow-hidden">
                        
                        <!-- Header Kartu -->
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider {{ $nomorBadgeClass }}">
                                    Paket {{ $paket->nomor_paket }}
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-800 border border-rose-200">
                                    Hemat Rp{{ number_format($paket->hemat_per_hari, 0, ',', '.') }}/hari
                                </span>
                            </div>

                            <h2 class="font-urbanist font-black text-xl text-stone-900 group-hover:text-[#0e1b15] transition">
                                {{ $paket->nama }}
                            </h2>

                            <div class="flex items-center gap-2 mt-1.5 text-xs text-stone-500 font-medium">
                                <span class="inline-flex items-center gap-1 font-bold text-stone-700">
                                    <svg class="w-3.5 h-3.5 text-[#1e3a2c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    <span>Kapasitas: {{ $paket->kapasitas_orang }}</span>
                                </span>
                            </div>

                            <p class="text-xs text-stone-600 mt-3 leading-relaxed">
                                {{ $paket->deskripsi }}
                            </p>

                            <!-- DAFTAR ALAT DI DALAM PAKET -->
                            <div class="mt-5 pt-4 border-t border-stone-100">
                                <span class="text-[10px] font-extrabold uppercase tracking-widest text-stone-400 block mb-2.5">
                                    Kelengkapan Alat Termasuk ({{ $paket->items->sum('jumlah') }} Unit):
                                </span>
                                <ul class="space-y-2 text-xs">
                                    @foreach($paket->items as $it)
                                        <li class="flex items-center justify-between gap-2 p-2 rounded-xl bg-stone-50/80 border border-stone-100">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="w-5 h-5 rounded-md bg-[#5edd5b]/25 text-emerald-950 font-bold text-[10px] flex items-center justify-center shrink-0">
                                                    {{ $it->jumlah }}x
                                                </span>
                                                <span class="font-bold text-stone-900 truncate">
                                                    {{ $it->alat?->nama ?? 'Alat' }}
                                                    @if($it->alat?->varian)
                                                        <span class="text-stone-400 font-normal">({{ $it->alat->varian }})</span>
                                                    @endif
                                                </span>
                                            </div>
                                            <span class="text-[10px] text-stone-400 font-mono shrink-0">
                                                {{ $it->alat?->kategori?->nama }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <!-- Footer Harga & 2 Tombol Aksi -->
                        <div class="mt-8 pt-5 border-t border-stone-100">
                            <!-- Perbandingan Harga Normal vs Paket -->
                            <div class="flex items-baseline justify-between mb-4">
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-xs text-stone-400 line-through font-mono">
                                            Rp{{ number_format($paket->harga_asli_per_hari, 0, ',', '.') }}
                                        </span>
                                        <span class="text-[10px] font-bold text-emerald-800 bg-emerald-50 px-1.5 py-0.2 rounded border border-emerald-200">
                                            -{{ $paket->persen_hemat }}%
                                        </span>
                                    </div>
                                    <div class="font-urbanist font-black text-2xl text-[#0e1b15] font-mono mt-0.5">
                                        Rp{{ number_format($paket->harga_paket_per_hari, 0, ',', '.') }}
                                        <span class="text-xs text-stone-500 font-normal">/hari</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] font-bold text-stone-400 block uppercase">Total Hemat</span>
                                    <span class="font-bold text-xs text-emerald-800 font-mono">
                                        Hemat Rp{{ number_format($paket->hemat_per_hari, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Tombol 1: Sewa Paket Langsung -->
                            <form method="POST" action="{{ route('paket-bundling.sewa-langsung', $paket->slug) }}" class="mb-2">
                                @csrf
                                <button type="submit"
                                        class="w-full py-3 px-4 rounded-xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs font-black transition shadow-xs flex items-center justify-center gap-2 cursor-pointer font-urbanist hover:scale-[1.02]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    <span>Sewa Paket {{ $paket->nomor_paket }} Langsung</span>
                                </button>
                            </form>

                            <!-- Tombol 2: Masukkan ke Keranjang Belanja -->
                            <form method="POST" action="{{ route('keranjang.tambah-paket', $paket->id) }}">
                                @csrf
                                <button type="submit"
                                        class="w-full py-2.5 px-4 rounded-xl bg-stone-100 hover:bg-[#0e1b15] hover:text-[#5edd5b] text-stone-800 text-xs font-bold transition flex items-center justify-center gap-2 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    <span>Masukkan ke Keranjang</span>
                                </button>
                            </form>
                        </div>

                    </div>
                @endforeach
            </div>

            <!-- BANNER PROMO KUSTOMISASI ALAT -->
            <div class="rounded-3xl bg-white border border-stone-200/90 p-8 sm:p-10 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-6">
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#4e7a5a] block mb-1">Butuh Kombinasi Alat Lain?</span>
                    <h3 class="font-urbanist font-black text-xl sm:text-2xl text-stone-900">
                        Mau Tambah Matras, Carrier, atau Lampu Tenda Tambahan?
                    </h3>
                    <p class="text-xs sm:text-sm text-stone-500 mt-1 max-w-xl leading-relaxed">
                        Anda dapat memasukkan paket bundling terlebih dahulu ke keranjang belanja, lalu menambahkan alat eceran lainnya dari katalog sesuka hati.
                    </p>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ route('katalog.index') }}" class="px-5 py-3 rounded-xl bg-[#0e1b15] hover:bg-[#1a3026] text-[#5edd5b] text-xs font-black transition shadow-xs">
                        Buka Katalog Lengkap
                    </a>
                    <a href="{{ route('keranjang.index') }}" class="px-5 py-3 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-800 text-xs font-bold transition">
                        Buka Keranjang
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-layouts.publik>

