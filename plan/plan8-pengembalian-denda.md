# Plan 8 – Pengembalian, Kondisi Alat, Denda, dan Scheduler

**Tujuan:** menyelesaikan siklus sewa: pengembalian, perhitungan denda, pembaruan stok, dan status otomatis. **Fitur:** F06. **Estimasi:** 5–6 hari.

## 1. DendaService
```php
public function hitungKeterlambatan(Pesanan $p, Carbon $waktuKembali): array
{
    $toleransi = (int) Pengaturan::ambil('toleransi_menit', 60);
    $persen = (float) Pengaturan::ambil('persen_denda_harian', 100) / 100;
    if ($waktuKembali->lte($p->jatuh_tempo->copy()->addMinutes($toleransi))) return ['hari' => 0, 'jumlah' => 0];

    $hari = (int) ceil($p->jatuh_tempo->diffInMinutes($waktuKembali) / 1440);
    $tarifHarian = $p->detail->sum(fn ($d) => $d->harga_per_hari * $d->jumlah);
    return ['hari' => $hari, 'jumlah' => $hari * $tarifHarian * $persen];
}
```
Contoh uji: tarif harian 110.000, terlambat 2 hari → 220.000 (BB-55).

## 2. PengembalianService::proses
```php
public function proses(Pesanan $p, User $admin, array $d): Pengembalian
{
    return DB::transaction(function () use ($p, $admin, $d) {
        $p = Pesanan::with('detail')->lockForUpdate()->findOrFail($p->id);
        $this->pastikan($p, [StatusPesanan::Disewa, StatusPesanan::Terlambat]);
        $waktu = Carbon::parse($d['dikembalikan_pada']);

        $kembali = Pengembalian::create(['pesanan_id'=>$p->id,'dikembalikan_pada'=>$waktu,'hari_terlambat'=>0,'diterima_oleh'=>$admin->id,'catatan'=>$d['catatan'] ?? null]);

        foreach ($p->detail as $det) {
            $x = $d['baris'][$det->id];
            $jumlahKondisi = $x['baik'] + $x['rusak_ringan'] + $x['rusak_berat'] + $x['hilang'];
            if ($jumlahKondisi !== $det->jumlah) throw ValidationException::withMessages(['baris' => "Jumlah kondisi {$det->alat->nama} harus {$det->jumlah}."]);

            $kd = $kembali->detail()->create(['pesanan_detail_id'=>$det->id,'jml_baik'=>$x['baik'],'jml_rusak_ringan'=>$x['rusak_ringan'],'jml_rusak_berat'=>$x['rusak_berat'],'jml_hilang'=>$x['hilang'],'catatan'=>$x['catatan'] ?? null]);

            $alat = Alat::withTrashed()->lockForUpdate()->find($det->alat_id);
            $alat->stok_perawatan += $x['rusak_ringan'] + $x['rusak_berat'];
            $alat->stok_total -= $x['hilang'];
            $alat->save();

            if (($x['biaya'] ?? 0) > 0) {
                $kembali->denda()->create(['pesanan_id'=>$p->id,'pengembalian_detail_id'=>$kd->id,'jenis'=>'kerusakan','keterangan'=>"Kerusakan/kehilangan {$det->alat->nama}",'jumlah'=>$x['biaya']]);
            }
        }

        $telat = $this->dendaService->hitungKeterlambatan($p, $waktu);
        if ($telat['jumlah'] > 0) {
            $kembali->update(['hari_terlambat' => $telat['hari']]);
            $kembali->denda()->create(['pesanan_id'=>$p->id,'jenis'=>'keterlambatan','keterangan'=>"Terlambat {$telat['hari']} hari",'jumlah'=>$telat['jumlah']]);
        }

        $p->update(['status' => StatusPesanan::Dikembalikan, 'diproses_oleh' => $admin->id]);
        return $kembali;
    });
}
```
Perhatian pada CHECK `stok_perawatan ≤ stok_total`: bila alat hilang sekaligus perawatan, perbarui stok dengan urutan aman atau abaikan CHECK dan validasi di aplikasi.

## 3. Pengembalian Jaminan
`PATCH /admin/pesanan/{pesanan}/jaminan/kembali`: set `status = dikembalikan`, `dikembalikan_pada`, `dikembalikan_oleh`. Kebijakan: jika ada denda `belum_lunas`, tampilkan peringatan (Admin yang memutuskan). Dicatat di log.

## 4. Form Pengembalian (A13)
- Tanggal dan jam kembali (default `now()`).
- Tabel alat: jumlah disewa, input Baik / Rusak Ringan / Rusak Berat / Hilang (default Baik = jumlah), kolom biaya, catatan.
- JavaScript: validasi jumlah kondisi realtime.
- Pratinjau denda keterlambatan (endpoint JSON memakai `DendaService`), total tagihan.
- Konfirmasi sebelum simpan.

## 5. Halaman Denda dan Pelunasan
`Admin\DendaController`: daftar (filter lunas/belum), `PATCH /admin/denda/{denda}/lunas` (set `lunas`, waktu, petugas). Pembayaran tetap manual.

## 6. Scheduler
```powershell
php artisan make:command PerbaruiStatusPesanan --command=pesanan:perbarui-status
```
```php
public function handle(): int
{
    $toleransi = (int) Pengaturan::ambil('toleransi_menit', 60);

    Pesanan::where('status', 'disewa')
        ->where('jatuh_tempo', '<', now()->subMinutes($toleransi))
        ->update(['status' => 'terlambat']);

    Pesanan::where('status', 'menunggu')->where('batas_ambil', '<', now())
        ->update(['status' => 'dibatalkan', 'dibatalkan_pada' => now(), 'alasan_batal' => 'Melewati batas pengambilan']);
    return self::SUCCESS;
}
```
`routes/console.php`:
```php
Schedule::command('pesanan:perbarui-status')->hourly();
```
- Demo di XAMPP: `php artisan schedule:work` di terminal terpisah.
- Pengaman: panggil `PesananService::sinkronStatus()` saat membuka daftar pesanan Admin dan Pelanggan.

## 7. Pengujian (BB-52 sampai BB-62)
Tepat waktu, dalam toleransi, terlambat 2 hari, rusak ringan (cek `stok_perawatan` dan ketersediaan), hilang (`stok_total`), jumlah kondisi salah, proses ulang (ditolak), denda lunas, jaminan kembali. Uji unit `DendaService`.

## 8. Definition of Done
- [ ] Pengembalian memperbarui status, stok, dan jaminan sesuai aturan
- [ ] Denda keterlambatan dan kerusakan terhitung benar
- [ ] Scheduler memperbarui status Terlambat dan pembatalan otomatis
- [ ] Denda dapat dilunasi manual dan tercatat
- [ ] BB-52 sampai BB-62 lolos
- [ ] **Alur sewa inti selesai dan siap didemokan ke pembimbing**
