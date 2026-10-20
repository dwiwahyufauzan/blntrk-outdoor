@php
    // Ganti dengan foto toko sendiri: simpan di public/images/hero-blntrk.jpg
    $heroLocal = public_path('images/hero-blntrk.jpg');
    $heroImg = file_exists($heroLocal)
        ? asset('images/hero-blntrk.jpg')
        : 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?auto=format&fit=crop&w=2000&q=80';

    $trust = [
        ['<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>', '24 jam standby', 'Ambil alat kapan saja'],
        ['<path d="M3 7h16a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M3 7l12-3v3M17 14h2"/>', 'Bayar di toko', 'Tunai atau QRIS saat serah terima'],
        ['<rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9" cy="11" r="2"/><path d="M6 16c.5-1.5 5.5-1.5 6 0M15 10h3M15 14h3"/>', 'Jaminan KTP/SIM asli', 'Tanpa deposit uang'],
        ['<path d="M12 3l7 3v5c0 5-3 8-7 10-4-2-7-5-7-10V6z"/><path d="m9 12 2 2 4-4"/>', 'Alat bersih dan terawat', 'Dicek sebelum disewakan'],
    ];
    $steps = [
        ['Pilih alat dan tanggal', 'Tentukan tanggal ambil dan kembali. Sistem langsung mengecek unit yang tersedia.'],
        ['Buat pesanan online', 'Masuk atau daftar akun, lalu simpan pesanan dengan nomor resi sewa.'],
        ['Datang ke toko', 'Tunjukkan nomor resi dan KTP/SIM asli sebagai jaminan, lalu bayar tunai atau QRIS.'],
        ['Ambil dan kembalikan', 'Cek kelengkapan bersama admin. Kembalikan sebelum batas jam 12:00 siang.'],
    ];
    $faq = [
        ['Apakah harus transfer atau bayar online?', 'Tidak. Pesanan dibuat online, pembayaran dilakukan langsung di toko saat alat diambil, tunai atau QRIS.'],
        ['Jaminan apa yang dibutuhkan?', 'KTP atau SIM asli. Jaminan dikembalikan setelah alat dicek dan dinyatakan lengkap.'],
        ['Jam berapa alat harus dikembalikan?', 'Batas pengembalian pukul 12:00 siang pada tanggal kembali yang dipesan.'],
        ['Bisa ambil alat tengah malam?', 'Bisa. Toko buka 24 jam, jadi pendaki yang berangkat dini hari tetap terlayani.'],
    ];
@endphp

<x-layouts.publik>
    <x-slot:title>Sewa Perlengkapan Outdoor Subang - Buka 24 Jam</x-slot:title>

    <script>document.documentElement.classList.add('js');</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        .bl-body{font-family:'Plus Jakarta Sans',ui-sans-serif,system-ui,sans-serif;color:#14201a}
        .bl-d{font-family:'Bricolage Grotesque',ui-sans-serif,system-ui,sans-serif;letter-spacing:-.02em}
        .bl-lamp{background:
            radial-gradient(circle 360px at var(--mx,74%) var(--my,28%),rgba(246,196,83,.18),transparent 62%),
            radial-gradient(circle 560px at var(--mx,74%) var(--my,28%),transparent 0,rgba(4,10,7,.5) 100%)}
        .bl-line{display:block;overflow:hidden;padding-bottom:.08em}
        .bl-scroll{scrollbar-width:none}.bl-scroll::-webkit-scrollbar{display:none}
        input[type=date]{min-height:44px}
        .bl-stars{position:absolute;inset:0;z-index:-10;width:100%;height:100%;pointer-events:none;mix-blend-mode:screen}
        .bl-tilt{transform-style:preserve-3d;will-change:transform}
        .js [data-reveal].is-in.bl-tilt{transition:transform .25s ease-out,box-shadow .3s}
        .bl-glare{position:absolute;inset:0;pointer-events:none;opacity:0;transition:opacity .3s;background:radial-gradient(circle at var(--gx,50%) var(--gy,0%),rgba(255,255,255,.4),transparent 55%)}
        .bl-tilt:hover .bl-glare{opacity:1}
        .bl-glow{background:radial-gradient(220px circle at var(--gx,50%) var(--gy,50%),rgba(246,196,83,.3),transparent 70%)}
        .bl-marq{display:flex;width:max-content}
        #bl-bar{position:fixed;top:0;left:0;height:3px;width:100%;transform:scaleX(0);transform-origin:left;background:#f6c453;z-index:60}
        @media (prefers-reduced-motion:no-preference){
            .bl-kb{animation:blkb 14s ease-out both}
            .bl-line>span{display:block;transform:translateY(110%);animation:blup .9s cubic-bezier(.2,.8,.2,1) forwards}
            .bl-line:nth-child(2)>span{animation-delay:.14s}
            .bl-in{opacity:0;transform:translateY(16px);animation:blfade .8s ease forwards;animation-delay:var(--d,.5s)}
            .bl-float{animation:blfl 6s ease-in-out infinite}
            .bl-ping{animation:blping 2s cubic-bezier(0,0,.2,1) infinite}
            .js [data-reveal]{opacity:0;transform:translateY(22px);transition:opacity .7s ease,transform .7s cubic-bezier(.2,.8,.2,1);transition-delay:var(--d,0ms)}
            .js [data-reveal].is-in{opacity:1;transform:none}
            .bl-rail{transform:scaleX(0);transform-origin:left;transition:transform 1.4s cubic-bezier(.3,.7,.2,1) .2s}
            .is-in .bl-rail,.bl-rail.is-in{transform:scaleX(1)}
            @keyframes blkb{from{transform:scale(1.12)}to{transform:scale(1)}}
            @keyframes blup{to{transform:none}}
            .bl-marq{animation:blmq 40s linear infinite}.bl-marq:hover{animation-play-state:paused}
            @keyframes blmq{to{transform:translateX(-50%)}}
            @keyframes blfade{to{opacity:1;transform:none}}
            @keyframes blfl{50%{transform:translateY(-8px)}}
            @keyframes blping{75%,100%{transform:scale(2.2);opacity:0}}
        }
    </style>

    <div class="bl-body">
        <div id="bl-bar"></div>

        {{-- HERO --}}
        <section id="bl-hero" class="relative isolate overflow-hidden rounded-[28px] sm:rounded-[36px] min-h-[700px] md:min-h-[760px] mb-12 text-white bg-[#0e1b15]">
            <img id="bl-img" style="transition:translate .5s ease-out" src="{{ $heroImg }}" alt="Tenda camping di bawah langit malam" fetchpriority="high"
                 onerror="this.style.display='none'"
                 class="bl-kb absolute -inset-[3%] -z-20 h-[106%] w-[106%] max-w-none object-cover">
            <canvas id="bl-stars" class="bl-stars" aria-hidden="true"></canvas>
            <div class="absolute inset-0 -z-10 bg-gradient-to-r from-[#0a140f]/90 via-[#0a140f]/55 to-[#0a140f]/10"></div>
            <div class="absolute inset-0 -z-10 bg-gradient-to-t from-[#0a140f] via-transparent to-transparent"></div>
            <div class="bl-lamp pointer-events-none absolute inset-0 -z-10"></div>

            <div class="relative flex min-h-[inherit] flex-col justify-between gap-10 px-5 pb-5 pt-10 sm:px-10 sm:pb-8 sm:pt-14 lg:px-14">
                <div class="max-w-2xl">
                    <div class="bl-in inline-flex items-center gap-2.5 rounded-full border border-white/20 bg-white/10 px-3.5 py-1.5 text-xs font-semibold backdrop-blur-md" style="--d:.2s">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="bl-ping absolute inline-flex h-full w-full rounded-full bg-[#f6c453] opacity-70"></span>
                            <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-[#f6c453]"></span>
                        </span>
                        Buka sekarang, 24 jam di Jalancagak, Subang
                    </div>

                    <h1 class="bl-d mt-6 text-[2.6rem] font-extrabold leading-[1.02] sm:text-6xl lg:text-7xl">
                        <span class="bl-line"><span>Berangkat tengah malam?</span></span>
                        <span class="bl-line"><span>Alatnya sudah menunggu.</span></span>
                    </h1>

                    <p class="bl-in mt-5 max-w-xl text-base leading-relaxed text-white/80 sm:text-lg" style="--d:.6s">
                        Sewa tenda, carrier, sleeping bag, dan sepatu gunung yang bersih dan terawat. Cek stok dan pesan dari HP, bayar tunai atau QRIS saat ambil alat.
                    </p>

                    <div class="bl-in mt-7 flex flex-col gap-3 sm:flex-row" style="--d:.8s">
                        <a href="{{ url('/alat') }}" class="bl-mag inline-flex items-center justify-center gap-2 rounded-xl bg-[#f6c453] px-6 py-3.5 text-sm font-bold text-[#14201a] shadow-lg shadow-black/30 transition hover:-translate-y-0.5 hover:bg-[#ffd36e] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                            Lihat semua alat
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                        </a>
                        <a href="#alur-sewa" class="inline-flex items-center justify-center rounded-xl border border-white/30 bg-white/10 px-6 py-3.5 text-sm font-semibold backdrop-blur-md transition hover:bg-white/20">
                            Cara dan ketentuan sewa
                        </a>
                    </div>
                </div>

                {{-- Stat melayang (desktop) --}}
                <div class="bl-in absolute right-10 top-14 hidden w-56 flex-col gap-3 lg:flex" style="--d:1s">
                    <div class="bl-float rounded-2xl border border-white/20 bg-white/10 p-4 backdrop-blur-xl">
                        <div class="bl-d text-3xl font-extrabold"><span data-count="60">60</span>+</div>
                        <div class="text-xs text-white/70">pelanggan dilayani setiap hari</div>
                    </div>
                    <div class="bl-float rounded-2xl border border-white/20 bg-white/10 p-4 backdrop-blur-xl" style="animation-delay:-3s">
                        <div class="bl-d text-3xl font-extrabold"><span data-count="{{ (int) $kategoriList->sum('alat_count') }}">{{ (int) $kategoriList->sum('alat_count') }}</span> jenis</div>
                        <div class="text-xs text-white/70">alat siap disewa</div>
                    </div>
                </div>

                {{-- Cek ketersediaan --}}
                <form action="{{ url('/alat') }}" method="GET" class="bl-in grid gap-3 rounded-2xl border border-white/20 bg-white/10 p-3 backdrop-blur-xl sm:grid-cols-[1fr_1fr_auto] sm:items-end sm:p-4" style="--d:1.1s">
                    <label class="block text-xs font-semibold text-white/80">Tanggal ambil
                        <input id="bl-mulai" type="date" name="mulai" min="{{ now()->format('Y-m-d') }}" class="mt-1.5 w-full rounded-lg border border-white/20 bg-black/30 px-3 text-sm text-white [color-scheme:dark] focus:outline focus:outline-2 focus:outline-[#f6c453]">
                    </label>
                    <label class="block text-xs font-semibold text-white/80">Tanggal kembali
                        <input id="bl-selesai" type="date" name="selesai" min="{{ now()->format('Y-m-d') }}" class="mt-1.5 w-full rounded-lg border border-white/20 bg-black/30 px-3 text-sm text-white [color-scheme:dark] focus:outline focus:outline-2 focus:outline-[#f6c453]">
                    </label>
                    <button type="submit" class="min-h-[44px] rounded-lg bg-white px-6 text-sm font-bold text-[#14201a] transition hover:bg-[#f6c453]">Cek alat tersedia</button>
                </form>
            </div>
        </section>

        {{-- MARQUEE --}}
        @php $mq = $kategoriList->pluck('nama')->all() ?: ['Tenda', 'Carrier', 'Sleeping bag', 'Sepatu gunung']; @endphp
        <div class="mb-14 overflow-hidden border-y border-stone-200 py-4 [mask-image:linear-gradient(90deg,transparent,#000_10%,#000_90%,transparent)]">
            <div class="bl-marq bl-d gap-10 text-xl font-extrabold text-[#1e3a2c]/80">
                @for($k = 0; $k < 4; $k++)
                    @foreach($mq as $m)
                        <span class="flex shrink-0 items-center gap-10" @if($k > 0) aria-hidden="true" @endif>{{ $m }}<span class="h-1.5 w-1.5 rounded-full bg-[#f6c453]"></span></span>
                    @endforeach
                @endfor
            </div>
        </div>

        {{-- TRUST --}}
        <section class="mb-14 grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($trust as $i => $t)
                <div data-reveal style="--d:{{ $i * 80 }}ms" class="flex items-start gap-3.5">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#1e3a2c] text-[#f6c453]">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">{!! $t[0] !!}</svg>
                    </span>
                    <div>
                        <div class="text-sm font-bold">{{ $t[1] }}</div>
                        <div class="text-xs leading-relaxed text-stone-500">{{ $t[2] }}</div>
                    </div>
                </div>
            @endforeach
        </section>

        {{-- KATEGORI --}}
        <section class="mb-16">
            <div data-reveal class="mb-6 flex items-end justify-between gap-4">
                <div>
                    <h2 class="bl-d text-2xl font-extrabold sm:text-4xl">Pilih perlengkapan sesuai rencana</h2>
                    <p class="mt-1 text-sm text-stone-500">Camping santai sampai pendakian malam, semuanya ada.</p>
                </div>
                <a href="{{ url('/alat') }}" class="shrink-0 text-sm font-bold text-[#1e3a2c] underline-offset-4 hover:underline">Lihat semua</a>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-4">
                @forelse($kategoriList as $i => $kat)
                    <a data-reveal style="--d:{{ ($i % 8) * 60 }}ms" href="{{ url('/alat?kategori=' . $kat->slug) }}"
                       class="group relative isolate overflow-hidden rounded-2xl border border-stone-200 bg-white p-4 transition duration-300 hover:-translate-y-1 hover:border-[#1e3a2c] hover:bg-[#1e3a2c] hover:shadow-xl sm:p-5">
                        <span class="bl-glow pointer-events-none absolute inset-0 -z-10 opacity-0 transition group-hover:opacity-100"></span>
                        <svg class="mb-6 h-8 w-8 text-[#4e7a5a] transition group-hover:text-[#f6c453]" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M3 20 12 4l9 16z"/><path d="M12 20v-6l-3 6M12 14l3 6"/></svg>
                        <div class="text-sm font-bold transition group-hover:text-white sm:text-base">{{ $kat->nama }}</div>
                        <div class="mt-0.5 text-xs text-stone-500 transition group-hover:text-white/70">{{ $kat->alat_count }} jenis alat</div>
                    </a>
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-stone-300 bg-white p-8 text-center text-sm text-stone-500">
                        Belum ada kategori. Tambahkan lewat menu Kelola Data Alat.
                    </div>
                @endforelse
            </div>
        </section>

        {{-- ALAT POPULER --}}
        <section class="mb-16">
            <div data-reveal class="mb-6 flex items-end justify-between gap-4">
                <div>
                    <h2 class="bl-d text-2xl font-extrabold sm:text-4xl">Paling sering disewa</h2>
                    <p class="mt-1 text-sm text-stone-500">Geser untuk melihat lainnya, stok diperbarui langsung dari toko.</p>
                </div>
                <a href="{{ url('/alat') }}" class="shrink-0 text-sm font-bold text-[#1e3a2c] underline-offset-4 hover:underline">Katalog lengkap</a>
            </div>

            <div class="bl-scroll -mx-5 flex snap-x snap-mandatory gap-4 overflow-x-auto px-5 pb-4 sm:mx-0 sm:px-0 md:grid md:grid-cols-2 md:overflow-visible lg:grid-cols-4">
                @forelse($alatUnggulan as $i => $alat)
                    @php $pct = $alat->stok_total > 0 ? round($alat->stok_tersedia / $alat->stok_total * 100) : 0; @endphp
                    <article data-reveal style="--d:{{ ($i % 4) * 90 }}ms" class="bl-tilt group relative flex min-w-[78%] snap-start flex-col overflow-hidden rounded-2xl border border-stone-200 bg-white hover:shadow-2xl sm:min-w-[46%] md:min-w-0">
                        <div class="bl-glare z-10"></div>
                        <div class="relative h-48 overflow-hidden bg-stone-100">
                            @if($alat->foto)
                                <img src="{{ asset('storage/' . $alat->foto) }}" alt="{{ $alat->nama }}" loading="lazy" class="h-full w-full object-cover transition duration-700 group-hover:scale-110">
                            @else
                                <div class="flex h-full flex-col items-center justify-center text-stone-300">
                                    <svg class="h-12 w-12" fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M3 20 12 4l9 16z"/><path d="M12 20v-6l-3 6M12 14l3 6"/></svg>
                                    <span class="mt-1 text-xs text-stone-400">{{ $alat->kategori?->nama ?? 'Alat outdoor' }}</span>
                                </div>
                            @endif
                            <span class="absolute left-3 top-3 rounded-full bg-white/90 px-2.5 py-1 text-[11px] font-semibold shadow-sm backdrop-blur">{{ $alat->kategori?->nama ?? 'Umum' }}</span>
                            <span class="absolute right-3 top-3 rounded-full px-2.5 py-1 text-[11px] font-bold text-white shadow-sm {{ $alat->stok_tersedia > 0 ? 'bg-[#1e3a2c]' : 'bg-rose-600' }}">
                                {{ $alat->stok_tersedia > 0 ? 'Tersedia ' . $alat->stok_tersedia : 'Stok habis' }}
                            </span>
                        </div>

                        <div class="flex flex-1 flex-col p-4">
                            <h3 class="line-clamp-1 text-base font-bold" title="{{ $alat->nama }}">{{ $alat->nama }}</h3>
                            <p class="mt-1 line-clamp-2 text-xs leading-relaxed text-stone-500">{{ $alat->deskripsi ?? 'Bersih, terawat, dan siap pakai untuk aktivitas outdoor.' }}</p>

                            <div class="mt-4" title="{{ $alat->stok_tersedia }} dari {{ $alat->stok_total }} unit tersedia">
                                <div class="h-1.5 overflow-hidden rounded-full bg-stone-100">
                                    <div class="h-full rounded-full {{ $pct > 30 ? 'bg-[#4e7a5a]' : 'bg-[#e0a526]' }}" style="width: {{ $pct }}%"></div>
                                </div>
                                <div class="mt-1 text-[11px] text-stone-400">{{ $alat->stok_tersedia }} dari {{ $alat->stok_total }} unit</div>
                            </div>

                            <div class="mt-auto flex items-end justify-between pt-4">
                                <div>
                                    <span class="bl-d text-xl font-extrabold text-[#1e3a2c]">Rp {{ number_format($alat->harga_per_hari, 0, ',', '.') }}</span>
                                    <span class="text-xs text-stone-500">/hari</span>
                                </div>
                            </div>
                            <a href="{{ url('/alat/' . $alat->id) }}" class="mt-3 block rounded-xl bg-[#14201a] py-2.5 text-center text-sm font-bold text-white transition hover:bg-[#1e3a2c] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1e3a2c]">
                                Pilih dan cek jadwal
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full w-full rounded-2xl border border-dashed border-stone-300 bg-white p-10 text-center text-sm text-stone-500">
                        Belum ada alat yang ditampilkan.
                    </div>
                @endforelse
            </div>
        </section>

        {{-- ALUR SEWA --}}
        <section id="alur-sewa" class="relative mb-16 overflow-hidden rounded-[28px] bg-[#0e1b15] p-6 text-white sm:rounded-[36px] sm:p-12">
            <div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-[#f6c453]/15 blur-3xl"></div>
            <div data-reveal class="relative mb-10 max-w-xl">
                <h2 class="bl-d text-3xl font-extrabold sm:text-4xl">Empat langkah sampai alat di tangan</h2>
                <p class="mt-2 text-sm text-white/60">Pesan dari rumah, selesaikan di toko tanpa antre panjang.</p>
            </div>

            <div class="relative">
                <div data-reveal class="absolute left-0 right-0 top-5 hidden h-px bg-white/15 md:block"><div class="bl-rail h-full bg-[#f6c453]"></div></div>
                <ol class="relative grid gap-4 sm:grid-cols-2 md:grid-cols-4 md:gap-6">
                    @foreach($steps as $i => $s)
                        <li data-reveal style="--d:{{ $i * 120 }}ms" class="rounded-2xl border border-white/10 bg-white/5 p-5 backdrop-blur-sm transition hover:bg-white/10">
                            <div class="bl-d mb-4 flex h-10 w-10 items-center justify-center rounded-full bg-[#f6c453] text-base font-extrabold text-[#14201a]">{{ $i + 1 }}</div>
                            <h3 class="text-sm font-bold">{{ $s[0] }}</h3>
                            <p class="mt-1.5 text-xs leading-relaxed text-white/60">{{ $s[1] }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>

        {{-- FAQ --}}
        <section class="mb-16 grid gap-8 lg:grid-cols-[1fr_1.4fr]">
            <div data-reveal>
                <h2 class="bl-d text-2xl font-extrabold sm:text-4xl">Pertanyaan yang sering muncul</h2>
                <p class="mt-2 text-sm text-stone-500">Belum ketemu jawabannya? Tanya langsung lewat WhatsApp.</p>
            </div>
            <div class="divide-y divide-stone-200 rounded-2xl border border-stone-200 bg-white">
                @foreach($faq as $i => $f)
                    <details data-reveal style="--d:{{ $i * 70 }}ms" class="group p-5" @if($i === 0) open @endif>
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-4 text-sm font-bold [&::-webkit-details-marker]:hidden">
                            {{ $f[0] }}
                            <svg class="h-5 w-5 shrink-0 text-[#4e7a5a] transition-transform duration-300 group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                        </summary>
                        <p class="mt-3 text-sm leading-relaxed text-stone-600">{{ $f[1] }}</p>
                    </details>
                @endforeach
            </div>
        </section>

        {{-- LOKASI --}}
        <section data-reveal class="relative overflow-hidden rounded-[28px] bg-[#1e3a2c] p-6 text-white sm:rounded-[36px] sm:p-12">
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_85%_20%,rgba(246,196,83,.22),transparent_45%)]"></div>
            <div class="relative flex flex-col items-start justify-between gap-8 md:flex-row md:items-center">
                <div class="max-w-lg">
                    <h3 class="bl-d text-2xl font-extrabold sm:text-3xl">{{ $toko['nama'] }}</h3>
                    <p class="mt-3 flex items-start gap-2 text-sm text-white/80">
                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-[#f6c453]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s7-6.2 7-11a7 7 0 0 0-14 0c0 4.8 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
                        {{ $toko['alamat'] }}
                    </p>
                    <p class="mt-2 text-sm text-white/60">Buka 24 jam untuk pendaki yang berangkat tengah malam atau dini hari.</p>
                </div>
                <div class="flex w-full flex-col gap-3 sm:flex-row md:w-auto">
                    <a href="https://wa.me/{{ $toko['wa'] }}" target="_blank" rel="noopener" class="rounded-xl bg-[#f6c453] px-6 py-3.5 text-center text-sm font-bold text-[#14201a] transition hover:-translate-y-0.5 hover:bg-[#ffd36e]">Hubungi lewat WhatsApp</a>
                    <a href="{{ url('/alat') }}" class="rounded-xl border border-white/30 px-6 py-3.5 text-center text-sm font-semibold transition hover:bg-white/10">Pesan sekarang</a>
                </div>
            </div>
        </section>

        {{-- Tombol WhatsApp melayang --}}
        <a href="https://wa.me/{{ $toko['wa'] }}" target="_blank" rel="noopener" aria-label="Chat WhatsApp BLNTRK Outdoor"
           class="fixed bottom-5 right-5 z-40 flex h-14 w-14 items-center justify-center rounded-full bg-[#f6c453] text-[#14201a] shadow-xl shadow-black/25 transition hover:scale-105">
            <span class="bl-ping absolute inline-flex h-full w-full rounded-full bg-[#f6c453] opacity-40"></span>
            <svg class="relative h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a8 8 0 0 1-11.5 7.2L4 20l1-4.5A8 8 0 1 1 21 12z"/></svg>
        </a>
    </div>

    <script>
        (function () {
            var d = document;
            var rm = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            // Muncul saat di-scroll + hitung angka naik
            function countUp(el) {
                var to = parseInt(el.getAttribute('data-count'), 10) || 0;
                if (rm) { el.textContent = to; return; }
                var t0 = null;
                (function tick(t) {
                    if (!t0) t0 = t;
                    var p = Math.min((t - t0) / 1200, 1);
                    el.textContent = Math.round(to * (1 - Math.pow(1 - p, 3)));
                    if (p < 1) requestAnimationFrame(tick);
                })(performance.now());
            }
            d.querySelectorAll('[data-count]').forEach(countUp);

            var items = d.querySelectorAll('[data-reveal]');
            if (rm || !('IntersectionObserver' in window)) {
                items.forEach(function (e) { e.classList.add('is-in'); });
            } else {
                var io = new IntersectionObserver(function (list) {
                    list.forEach(function (x) {
                        if (x.isIntersecting) { x.target.classList.add('is-in'); io.unobserve(x.target); }
                    });
                }, { threshold: 0.15 });
                items.forEach(function (e) { io.observe(e); });
            }

            // Lampu senter mengikuti kursor di hero (perangkat dengan mouse)
            var hero = d.getElementById('bl-hero');
            if (hero && !rm && window.matchMedia('(hover: hover)').matches) {
                hero.addEventListener('pointermove', function (e) {
                    var r = hero.getBoundingClientRect();
                    hero.style.setProperty('--mx', (e.clientX - r.left) + 'px');
                    hero.style.setProperty('--my', (e.clientY - r.top) + 'px');
                });
            }

            // Bar progres scroll
            var bar = d.getElementById('bl-bar');
            if (bar) {
                var up = function () {
                    var h = d.documentElement;
                    bar.style.transform = 'scaleX(' + (h.scrollTop / Math.max(1, h.scrollHeight - h.clientHeight)) + ')';
                };
                window.addEventListener('scroll', up, { passive: true }); up();
            }

            var fine = !rm && window.matchMedia('(hover: hover)').matches;

            // Parallax gambar hero
            var im = d.getElementById('bl-img');
            if (hero && im && fine) {
                hero.addEventListener('pointermove', function (e) {
                    var r = hero.getBoundingClientRect();
                    var px = (e.clientX - r.left) / r.width - 0.5, py = (e.clientY - r.top) / r.height - 0.5;
                    im.style.translate = (-px * 28) + 'px ' + (-py * 18) + 'px';
                });
            }

            // Bintang berkelip dan bintang jatuh di hero
            var cv = d.getElementById('bl-stars');
            if (cv && hero && !rm) {
                var cx = cv.getContext('2d'), S = [], W = 0, H = 0, sh = null, vis = true;
                var size = function () {
                    W = cv.width = hero.offsetWidth; H = cv.height = hero.offsetHeight; S = [];
                    for (var i = 0; i < 90; i++) S.push({ x: Math.random() * W, y: Math.random() * H * 0.6, r: Math.random() * 1.2 + 0.3, p: Math.random() * 6.3, s: 0.5 + Math.random() * 1.5 });
                };
                size(); window.addEventListener('resize', size);
                if ('IntersectionObserver' in window) new IntersectionObserver(function (e) { vis = e[0].isIntersecting; }).observe(hero);
                var frame = function (t) {
                    requestAnimationFrame(frame);
                    if (!vis) return;
                    cx.clearRect(0, 0, W, H); cx.fillStyle = '#fff';
                    S.forEach(function (s) {
                        cx.globalAlpha = 0.35 + 0.65 * Math.abs(Math.sin(t / 1000 * s.s + s.p));
                        cx.beginPath(); cx.arc(s.x, s.y, s.r, 0, 6.3); cx.fill();
                    });
                    if (!sh && Math.random() < 0.004) sh = { x: W * (0.4 + Math.random() * 0.5), y: H * 0.05, l: 0 };
                    if (sh) {
                        sh.x -= 10; sh.y += 4; sh.l++;
                        cx.globalAlpha = Math.max(0, 1 - sh.l / 40); cx.strokeStyle = '#fff'; cx.lineWidth = 1.6;
                        cx.beginPath(); cx.moveTo(sh.x, sh.y); cx.lineTo(sh.x + 60, sh.y - 24); cx.stroke();
                        if (sh.l > 40) sh = null;
                    }
                };
                requestAnimationFrame(frame);
            }

            if (fine) {
                // Kartu alat miring 3D mengikuti kursor
                d.querySelectorAll('.bl-tilt').forEach(function (c) {
                    c.addEventListener('pointermove', function (e) {
                        var r = c.getBoundingClientRect(), x = (e.clientX - r.left) / r.width, y = (e.clientY - r.top) / r.height;
                        c.style.transform = 'perspective(900px) rotateX(' + ((0.5 - y) * 8) + 'deg) rotateY(' + ((x - 0.5) * 10) + 'deg) translateY(-6px)';
                        c.style.setProperty('--gx', (x * 100) + '%'); c.style.setProperty('--gy', (y * 100) + '%');
                    });
                    c.addEventListener('pointerleave', function () { c.style.transform = ''; });
                });
                // Cahaya mengikuti kursor di tile kategori
                d.querySelectorAll('.bl-glow').forEach(function (g) {
                    var p = g.parentElement;
                    p.addEventListener('pointermove', function (e) {
                        var r = p.getBoundingClientRect();
                        g.style.setProperty('--gx', (e.clientX - r.left) + 'px'); g.style.setProperty('--gy', (e.clientY - r.top) + 'px');
                    });
                });
                // Tombol utama tertarik ke kursor
                d.querySelectorAll('.bl-mag').forEach(function (b) {
                    b.style.transition = 'translate .25s ease-out';
                    b.addEventListener('pointermove', function (e) {
                        var r = b.getBoundingClientRect();
                        b.style.translate = ((e.clientX - r.left - r.width / 2) * 0.22) + 'px ' + ((e.clientY - r.top - r.height / 2) * 0.35) + 'px';
                    });
                    b.addEventListener('pointerleave', function () { b.style.translate = ''; });
                });
            }

            // Tanggal kembali tidak boleh sebelum tanggal ambil
            var a = d.getElementById('bl-mulai'), b = d.getElementById('bl-selesai');
            if (a && b) {
                a.addEventListener('change', function () {
                    b.min = a.value;
                    if (b.value && b.value < a.value) b.value = a.value;
                });
            }
        })();
    </script>
</x-layouts.publik>