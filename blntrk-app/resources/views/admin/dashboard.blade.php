<x-layouts.admin>
    <x-slot:title>Dashboard Operasional Toko</x-slot:title>

    @php
        $totalSewa7Hari = array_sum($chartData);
        $hariChart = max(count($chartData), 1);
        $rataSewaHarian = round($totalSewa7Hari / $hariChart, 1);
        $puncakSewa = count($chartData) ? max($chartData) : 0;
        $perluPerhatian = $statistik['pesanan_terlambat'] + $statistik['perlu_diambil_hari_ini'] + $statistik['pesanan_menunggu'];
        $namaAdmin = explode(' ', trim(auth()->user()?->nama ?? auth()->user()?->name ?? 'Admin'))[0];
    @endphp

    @include('admin.dashboard.partials.peringatan-noshow')

    @include('admin.dashboard.partials.hero')

    @include('admin.dashboard.partials.kartu-metrik')

    <!-- ========================= TINDAKAN + GRAFIK ========================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6 mb-6 sm:mb-8">

        <!-- Kiri: Perlu Tindakan -->
        @include('admin.dashboard.partials.perlu-tindakan')

        <!-- Kanan: Grafik -->
        @include('admin.dashboard.partials.grafik-tren')
    </div>

    @include('admin.dashboard.partials.pesanan-terbaru')

    @include('admin.dashboard.partials.script-grafik')
</x-layouts.admin>
