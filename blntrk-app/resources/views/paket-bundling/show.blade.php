<x-layouts.publik>
    <x-slot:title>{{ $paket->nama }} - BLNTRK Outdoor Subang</x-slot:title>

    <div class="bg-[#f4f5f6] min-h-screen py-10 sm:py-14">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Breadcrumbs -->
            <div class="mb-6 flex items-center gap-2 text-xs font-semibold text-stone-500">
                <a href="{{ route('beranda') }}" class="hover:text-[#0e1b15]">Beranda</a>
                <span>&rsaquo;</span>
                <a href="{{ route('paket-bundling.index') }}" class="hover:text-[#0e1b15]">Paket Bundling</a>
                <span>&rsaquo;</span>
                <span class="text-stone-900 font-bold truncate">Paket {{ $paket->nomor_paket }}</span>
            </div>

            <!-- Detail Card Paket -->
            <div class="bg-white rounded-3xl border border-stone-200/90 shadow-md overflow-hidden p-6 sm:p-10 mb-10">
                <div class="flex flex-col md:flex-row gap-8">
                    
                    <!-- Cover Foto / Ilustrasi Paket -->
                    <div class="w-full md:w-2/5 shrink-0">
                        <div class="aspect-4/3 sm:aspect-square rounded-2xl overflow-hidden bg-stone-100 border border-stone-200 relative">
                            <img src="{{ $paket->foto_url }}" alt="{{ $paket->nama }}" class="w-full h-full object-cover">
                            <div class="absolute top-3 left-3 flex flex-col gap-1.5">
                                <span class="bg-[#0e1b15] text-[#5edd5b] text-[10px] font-black uppercase px-3 py-1 rounded-full shadow-md">
                                    Paket {{ $paket->nomor_paket }}
                                </span>
                                <span class="bg-rose-600 text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full shadow-md">
                                    Diskon {{ $paket->persen_hemat }}%
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Paket & Harga -->
                    <div class="flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 text-xs font-bold text-stone-500 mb-1.5">
                                <span>Kapasitas: {{ $paket->kapasitas_orang }}</span>
                                <span>&bull;</span>
                                <span class="text-emerald-800">Hemat Rp{{ number_format($paket->hemat_per_hari, 0, ',', '.') }}/hari</span>
                            </div>

                            <h1 class="text-2xl sm:text-3xl font-black font-urbanist text-stone-900 tracking-tight">
                                {{ $paket->nama }}
                            </h1>

                            <p class="text-xs sm:text-sm text-stone-600 mt-3 leading-relaxed">
                                {{ $paket->deskripsi }}
                            </p>

                            <!-- Kotak Harga -->
                            <div class="mt-6 p-4 rounded-2xl bg-stone-50 border border-stone-200/80 flex items-baseline justify-between">
                                <div>
                                    <span class="text-xs text-stone-400 line-through font-mono block">
                                        Rp{{ number_format($paket->harga_asli_per_hari, 0, ',', '.') }}
                                    </span>
                                    <div class="text-2xl sm:text-3xl font-black font-urbanist text-[#0e1b15] font-mono mt-0.5">
                                        Rp{{ number_format($paket->harga_paket_per_hari, 0, ',', '.') }}
                                        <span class="text-xs text-stone-500 font-normal">/hari</span>
                                    </div>
                                </div>
                                <span class="text-xs font-bold text-emerald-800 bg-emerald-100 px-3 py-1 rounded-full border border-emerald-200">
                                    Hemat Rp{{ number_format($paket->hemat_per_hari, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="mt-8 pt-6 border-t border-stone-100 flex flex-col sm:flex-row gap-3">
                            <form method="POST" action="{{ route('paket-bundling.sewa-langsung', $paket->slug) }}" class="flex-1">
                                @csrf
                                <button type="submit"
                                        class="w-full py-3.5 px-6 rounded-2xl bg-[#5edd5b] hover:bg-[#4ed04b] text-black text-xs sm:text-sm font-black transition shadow-md flex items-center justify-center gap-2 cursor-pointer font-urbanist">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    <span>Sewa Paket Ini Langsung</span>
                                </button>
                            </form>

                            <form method="POST" action="{{ route('keranjang.tambah-paket', $paket->id) }}" class="flex-1">
                                @csrf
                                <button type="submit"
                                        class="w-full py-3.5 px-6 rounded-2xl bg-[#0e1b15] hover:bg-[#1a3026] text-[#5edd5b] text-xs sm:text-sm font-black transition shadow-md flex items-center justify-center gap-2 cursor-pointer font-urbanist">
                                    <svg class="w-4 h-4 text-[#5edd5b]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    <span>Masukkan ke Keranjang</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Rincian Seluruh Alat di Paket Ini -->
                <div class="mt-10 pt-8 border-t border-stone-200/80">
                    <h3 class="text-base font-extrabold text-[#0e1b15] uppercase tracking-wider mb-4 font-urbanist">
                        Daftar Rincian Perlengkapan di Paket Ini ({{ $paket->items->sum('jumlah') }} Unit):
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        @foreach($paket->items as $it)
                            <div class="p-3.5 rounded-2xl bg-stone-50 border border-stone-200/80 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-[#0e1b15] text-[#5edd5b] flex items-center justify-center font-bold text-xs shrink-0 font-mono">
                                        {{ $it->jumlah }}x
                                    </div>
                                    <div>
                                        <div class="font-bold text-xs sm:text-sm text-stone-900">{{ $it->alat?->nama }}</div>
                                        <div class="text-[10px] text-stone-400 font-mono">{{ $it->alat?->kategori?->nama }} {{ $it->alat?->varian ? '&bull; ' . $it->alat->varian : '' }}</div>
                                    </div>
                                </div>
                                <span class="text-xs font-bold font-mono text-stone-700">
                                    Rp{{ number_format(($it->alat?->harga_per_hari ?? 0) * $it->jumlah, 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-layouts.publik>

