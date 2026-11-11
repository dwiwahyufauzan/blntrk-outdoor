<?php

/*
|--------------------------------------------------------------------------
| Identitas Bawaan Toko
|--------------------------------------------------------------------------
|
| Nilai cadangan bila pemilik belum mengisinya di halaman Pengaturan. Nilai yang berlaku sehari-hari
| tetap dibaca dari tabel `pengaturan` lewat App\Support\PengaturanToko.
|
*/

return [
    'nama' => env('TOKO_NAMA', 'BLNTRK Outdoor'),
    'alamat' => env('TOKO_ALAMAT', 'Desa Kumpay, Kec. Jalancagak, Kab. Subang, Jawa Barat'),
    'no_wa' => env('TOKO_NO_WA', '6281234567890'),
];
