@if($statistik['pesanan_noshow'] > 0)
    <div class="mb-6">
        <a href="{{ route('admin.pesanan.index', ['status' => 'dibayar']) }}"
           class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-950 text-xs sm:text-sm flex items-start gap-3 hover:bg-amber-100/70 transition">
            <span class="w-6 h-6 rounded-full bg-amber-500 text-white flex items-center justify-center shrink-0 font-black">!</span>
            <span><strong>{{ $statistik['pesanan_noshow'] }} pesanan sudah dibayar tetapi tidak diambil</strong> hingga lewat batas pengambilan. Stoknya masih tertahan; sistem membatalkannya otomatis (no-show) sesuai pengaturan, atau batalkan manual.</span>
        </a>
    </div>
@endif
