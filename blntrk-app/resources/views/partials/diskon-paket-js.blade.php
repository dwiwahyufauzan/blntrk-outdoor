{{-- Perhitungan diskon paket bundling sisi klien; harus identik dengan PaketBundlingService::hitungDiskon --}}
<script>
    window.daftarPaketBundling = @json($paketData ?? []);

    window.hitungDiskonPaket = function (items, daftarAlat, durasi) {
        const kuantitas = {};
        items.forEach(it => {
            const id = parseInt(it.alat_id);
            const jml = parseInt(it.jumlah) || 0;
            if (id && jml > 0) kuantitas[id] = (kuantitas[id] || 0) + jml;
        });

        let terbaik = { nama: null, jumlah: 0, diskon: 0 };
        let hematTerbaik = 0;

        window.daftarPaketBundling.forEach(paket => {
            let set = Infinity;
            let eceran = 0;
            Object.entries(paket.items).forEach(([alatId, perlu]) => {
                set = Math.min(set, Math.floor((kuantitas[alatId] || 0) / Math.max(1, perlu)));
                const alat = daftarAlat.find(a => a.id == alatId);
                eceran += (alat ? parseFloat(alat.harga_per_hari) : 0) * perlu;
            });
            if (!isFinite(set) || set < 1) return;

            const hemat = Math.max(0, eceran - paket.harga) * set;
            if (hemat > hematTerbaik) {
                hematTerbaik = hemat;
                terbaik = { nama: paket.nama, jumlah: set, diskon: hemat * Math.max(1, durasi) };
            }
        });

        return terbaik;
    };
</script>
