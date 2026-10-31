<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Resi Sewa #{{ $pesanan->no_resi }} - {{ $toko }}</title>
    <style>
        @page {
            margin: 1.2cm 1.5cm 1.5cm 1.5cm;
        }
        body {
            font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif;
            color: #1a202c;
            font-size: 9pt;
            line-height: 1.4;
        }
        .header-table {
            width: 100%;
            border-bottom: 3px double #0e1b15;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .brand-title {
            font-size: 16pt;
            font-weight: bold;
            color: #0e1b15;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .brand-subtitle {
            font-size: 8pt;
            color: #4a5568;
            margin-top: 2px;
            line-height: 1.3;
        }
        .resi-title {
            font-size: 12pt;
            font-weight: bold;
            color: #0e1b15;
            text-align: right;
            font-family: monospace;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 7.5pt;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
            margin-top: 3px;
        }
        .badge-dibayar { background-color: #f0fff4; color: #22543d; border: 1px solid #9ae6b4; }
        .badge-disewa { background-color: #ebf8ff; color: #2a4365; border: 1px solid #90cdf4; }
        .badge-selesai { background-color: #e6fffa; color: #234e52; border: 1px solid #81e6d9; }
        .badge-menunggu { background-color: #fffaf0; color: #744210; border: 1px solid #fbd38d; }
        .badge-terlambat { background-color: #fff5f5; color: #742a2a; border: 1px solid #feb2b2; }
        .badge-dibatalkan { background-color: #edf2f7; color: #4a5568; border: 1px solid #cbd5e0; }
        
        .info-table {
            width: 100%;
            margin-top: 6px;
            margin-bottom: 10px;
            border-collapse: collapse;
        }
        .info-box {
            padding: 8px;
            border: 1px solid #cbd5e0;
            background-color: #f8fafc;
            border-radius: 4px;
            vertical-align: top;
            font-size: 8.5pt;
        }
        .info-label {
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #4a5568;
            margin-bottom: 4px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 2px;
        }

        .section-heading {
            font-size: 9pt;
            font-weight: bold;
            color: #0e1b15;
            text-transform: uppercase;
            margin-top: 10px;
            margin-bottom: 4px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            font-size: 8.5pt;
        }
        .data-table th {
            background-color: #0e1b15;
            color: #ffffff;
            font-weight: bold;
            padding: 5px 8px;
            text-align: left;
            border: 1px solid #0e1b15;
        }
        .data-table td {
            padding: 5px 8px;
            border: 1px solid #cbd5e0;
            vertical-align: top;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

        .terms-box {
            margin-top: 10px;
            padding: 7px 10px;
            background-color: #f8fafc;
            border: 1px solid #cbd5e0;
            border-radius: 4px;
            font-size: 7.5pt;
            color: #4a5568;
            line-height: 1.4;
        }
        .terms-title {
            font-weight: bold;
            color: #0e1b15;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .signature-table {
            width: 100%;
            margin-top: 16px;
            page-break-inside: avoid;
        }
        .sig-col {
            width: 50%;
            text-align: center;
            font-size: 8.5pt;
        }
    </style>
</head>
<body>

    <!-- KOP RESI RESMI DOKUMEN -->
    <table class="header-table">
        <tr>
            <td style="width: 60%; vertical-align: middle;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 58px; vertical-align: middle; padding-right: 8px;">
                            <img src="{{ public_path('images/logo.png') }}" width="52" height="52" style="border-radius: 50%;">
                        </td>
                        <td style="vertical-align: middle;">
                            <div class="brand-title">{{ $toko }}</div>
                            <div class="brand-subtitle">{{ $alamat }}</div>
                            <div class="brand-subtitle">
                                WhatsApp: {{ \App\Models\Pengaturan::ambil('no_wa_toko', '0812-3456-7890') }} &bull; Email: blntrkoutdoor@gmail.com &bull; Buka 24 Jam
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 40%; text-align: right; vertical-align: middle;">
                <div class="brand-subtitle" style="text-transform: uppercase; font-weight: bold; color: #4a5568;">SURAT BUKTI SEWA & RESI TRANSAKSI</div>
                <div class="resi-title">#{{ $pesanan->no_resi }}</div>
                @php
                    $badgeClass = match($pesanan->status->value) {
                        'dibayar' => 'badge-dibayar',
                        'disewa' => 'badge-disewa',
                        'selesai' => 'badge-selesai',
                        'menunggu' => 'badge-menunggu',
                        'terlambat' => 'badge-terlambat',
                        default => 'badge-dibatalkan',
                    };
                @endphp
                <div>
                    <span class="status-badge {{ $badgeClass }}">{{ $pesanan->status->label() }}</span>
                </div>
                <div style="font-size: 7.5pt; color: #718096; margin-top: 2px;">
                    Tgl Reservasi: {{ $pesanan->created_at->format('d/m/Y H:i') }} WIB
                </div>
            </td>
        </tr>
    </table>

    <!-- INFO PENYEWA & JADWAL SEWA -->
    <table class="info-table">
        <tr>
            <td style="width: 50%; padding-right: 5px;">
                <div class="info-box">
                    <div class="info-label">IDENTITAS PENYEWA (PIHAK KEDUA)</div>
                    <div style="font-size: 9.5pt; font-weight: bold; color: #0e1b15;">{{ $pesanan->pelanggan->nama_lengkap }}</div>
                    <div style="font-family: monospace; color: #2d3748; margin-top: 1px;">No. WhatsApp: {{ $pesanan->pelanggan->no_hp }}</div>
                    <div style="color: #4a5568; margin-top: 1px;">{{ $pesanan->pelanggan->alamat }}</div>
                </div>
            </td>
            <td style="width: 50%; padding-left: 5px;">
                <div class="info-box">
                    <div class="info-label">JADWAL & WAKTU SEWA</div>
                    <div><strong>Tgl Pengambilan:</strong> {{ $pesanan->tanggal_mulai->translatedFormat('d F Y') }}</div>
                    <div><strong>Batas Pengembalian:</strong> {{ $pesanan->tanggal_selesai->translatedFormat('d F Y') }} (Pukul 12:00 WIB)</div>
                    <div><strong>Durasi Penyewaan:</strong> {{ $pesanan->durasi_hari }} Hari Penuh</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- STATUS PEMBAYARAN SEWA & JAMINAN IDENTITAS -->
    @php
        $adaBayar = $pesanan->pembayaran && $pesanan->pembayaran->isNotEmpty();
        $bayarPertama = $adaBayar ? $pesanan->pembayaran->first() : null;
        $totalBayar = $adaBayar ? $pesanan->pembayaran->sum('jumlah') : 0;
    @endphp
    @if($adaBayar || $pesanan->jaminan)
        <table class="info-table" style="margin-top: 0;">
            <tr>
                @if($adaBayar)
                    <td style="width: {{ $pesanan->jaminan ? '50%' : '100%' }}; padding-right: {{ $pesanan->jaminan ? '5px' : '0' }};">
                        <div class="info-box" style="background-color: #f0fff4; border-color: #9ae6b4;">
                            <div class="info-label" style="color: #22543d; border-color: #c6f6d5;">PEMBAYARAN BIAYA SEWA</div>
                            <div>Status: <strong>LUNAS</strong> &bull; Total: <strong>Rp {{ number_format($totalBayar, 0, ',', '.') }}</strong></div>
                            <div style="font-size: 7.5pt; color: #4a5568; margin-top: 2px;">
                                Metode: {{ $bayarPertama->metode->label() }} &bull; Tgl: {{ $bayarPertama->dibayar_pada?->format('d/m/Y H:i') }} WIB
                            </div>
                        </div>
                    </td>
                @endif
                @if($pesanan->jaminan)
                    <td style="width: {{ $adaBayar ? '50%' : '100%' }}; padding-left: {{ $adaBayar ? '5px' : '0' }};">
                        <div class="info-box" style="background-color: #ebf8ff; border-color: #90cdf4;">
                            <div class="info-label" style="color: #2a4365; border-color: #bee3f8;">JAMINAN IDENTITAS FISIK</div>
                            <div>Jenis: <strong>{{ is_string($pesanan->jaminan->jenis) ? strtoupper($pesanan->jaminan->jenis) : $pesanan->jaminan->jenis->label() }}</strong> (Asli)</div>
                            <div style="font-size: 7.5pt; color: #4a5568; margin-top: 2px;">
                                No: <span style="font-family: monospace; font-weight: bold;">{{ $pesanan->jaminan->nomor_identitas_samar ?? $pesanan->jaminan->nomor_identitas }}</span>
                                &bull; Status: {{ $pesanan->jaminan->dikembalikan_pada ? 'Sudah Dikembalikan' : 'Disimpan di Kasir Toko' }}
                            </div>
                        </div>
                    </td>
                @endif
            </tr>
        </table>
    @endif

    <!-- TABEL DAFTAR ALAT YANG DISEWA -->
    <div class="section-heading">Rincian Peralatan yang Disewa:</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 45%;">Nama Perlengkapan & Kategori</th>
                <th style="width: 15%;" class="text-center">Jumlah</th>
                <th style="width: 17%;" class="text-right">Tarif / Hari</th>
                <th style="width: 18%;" class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pesanan->detail as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->alat?->nama ?? 'Alat Outdoor' }}</strong><br>
                        <span style="font-size: 7.5pt; color: #4a5568;">Kode: {{ $item->alat?->kode_alat }} &bull; {{ $item->alat?->kategori?->nama }}</span>
                    </td>
                    <td class="text-center font-bold">{{ $item->jumlah }} unit</td>
                    <td class="text-right">Rp {{ number_format($item->harga_per_hari, 0, ',', '.') }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
@if($pesanan->diskon_paket > 0)
            <tr>
                <td colspan="4" class="text-right font-bold" style="padding: 4px 8px; color: #2f8f2c;">
                    DISKON {{ strtoupper($pesanan->paketBundling?->nama ?? 'Paket Bundling') }}{{ $pesanan->jumlah_paket > 1 ? ' x'.$pesanan->jumlah_paket : '' }}:
                </td>
                <td class="text-right font-bold" style="padding: 4px 8px; color: #2f8f2c;">
                    -Rp {{ number_format($pesanan->diskon_paket, 0, ',', '.') }}
                </td>
            </tr>
            @endif
                        <tr style="background-color: #edf2f7;">
                <td colspan="4" class="text-right font-bold" style="padding: 5px 8px;">
                    TOTAL BIAYA SEWA ({{ $pesanan->durasi_hari }} HARI):
                </td>
                <td class="text-right font-bold" style="padding: 5px 8px; font-size: 9.5pt; color: #0e1b15;">
                    Rp {{ number_format($pesanan->total_sewa, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- TABEL RINCIAN DENDA / GANTI RUGI (JIKA ADA) -->
    @if($pesanan->denda && $pesanan->denda->isNotEmpty())
        @php
            $totalDendaPesanan = $pesanan->denda->sum('jumlah');
            $totalDendaLunas = $pesanan->denda->where('status.value', 'lunas')->sum('jumlah');
            $sisaDenda = $totalDendaPesanan - $totalDendaLunas;
        @endphp
        <div class="section-heading" style="color: #742a2a; margin-top: 12px;">Rincian Denda & Penggantian Kerusakan / Kehilangan:</div>
        <table class="data-table">
            <thead>
                <tr style="background-color: #742a2a;">
                    <th style="width: 5%; background-color: #742a2a;" class="text-center">No</th>
                    <th style="width: 25%; background-color: #742a2a;">Kategori Denda</th>
                    <th style="width: 40%; background-color: #742a2a;">Keterangan / Kondisi</th>
                    <th style="width: 15%; background-color: #742a2a;" class="text-center">Status</th>
                    <th style="width: 15%; background-color: #742a2a;" class="text-right">Nominal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pesanan->denda as $idxDenda => $d)
                    <tr>
                        <td class="text-center">{{ $idxDenda + 1 }}</td>
                        <td>
                            <strong>{{ $d->kategoriDenda?->nama ?? $d->jenis->label() }}</strong>
                        </td>
                        <td>{{ $d->keterangan ?? '-' }}</td>
                        <td class="text-center">
                            @if($d->status->value === 'lunas')
                                <span style="color: #22543d; font-weight: bold;">Lunas</span>
                            @else
                                <span style="color: #742a2a; font-weight: bold;">Belum Lunas</span>
                            @endif
                        </td>
                        <td class="text-right font-bold">Rp {{ number_format($d->jumlah, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #fff5f5;">
                    <td colspan="4" class="text-right font-bold">TOTAL DENDA / GANTI RUGI:</td>
                    <td class="text-right font-bold" style="color: #742a2a;">Rp {{ number_format($totalDendaPesanan, 0, ',', '.') }}</td>
                </tr>
                @if($sisaDenda > 0)
                    <tr style="background-color: #fed7d7;">
                        <td colspan="4" class="text-right font-bold" style="color: #742a2a;">SISA DENDA WAJIB DIBAYAR (JAMINAN DITAHAN):</td>
                        <td class="text-right font-bold" style="color: #742a2a; font-size: 9.5pt;">Rp {{ number_format($sisaDenda, 0, ',', '.') }}</td>
                    </tr>
                @endif
            </tfoot>
        </table>
    @endif

    <!-- REKAPITULASI PEMBAYARAN KESELURUHAN -->
    @php
        $grandTotal = $pesanan->total_sewa + ($pesanan->denda ? $pesanan->denda->sum('jumlah') : 0);
        $totalTerbayarSemua = $totalBayar + ($pesanan->denda ? $pesanan->denda->where('status.value', 'lunas')->sum('jumlah') : 0);
        $totalKurangBayar = max(0, $grandTotal - $totalTerbayarSemua);
    @endphp
    <table style="width: 100%; margin-top: 8px; border-collapse: collapse;">
        <tr>
            <td style="width: 55%; vertical-align: top; padding-right: 10px;">
                <!-- KETENTUAN DAN SOP TOKO -->
                <div class="terms-box" style="margin-top: 0;">
                    <div class="terms-title">Ketentuan Rental & Pengembalian:</div>
                    <ol style="margin: 0; padding-left: 14px;">
                        <li>Tunjukkan lembar resi ini (cetak atau layar handphone) saat serah terima barang di toko.</li>
                        <li>Penyewa wajib menyerahkan kartu identitas fisik asli (KTP/SIM) yang ditahan selama masa sewa.</li>
                        <li>Batas pengembalian alat maksimal pukul <strong>12:00 WIB</strong> pada tanggal selesai sewa. Keterlambatan dikenakan denda sesuai tarif harian.</li>
                        <li>Alat yang hilang atau rusak wajib diganti rugi sesuai harga beli/perbaikan sebelum jaminan dikembalikan.</li>
                    </ol>
                </div>
            </td>
            <td style="width: 45%; vertical-align: top;">
                <table style="width: 100%; border: 1px solid #cbd5e0; border-collapse: collapse; font-size: 8.5pt;">
                    <tr>
                        <td style="padding: 4px 8px; border-bottom: 1px solid #e2e8f0;">Biaya Sewa Alat:</td>
                        <td style="padding: 4px 8px; border-bottom: 1px solid #e2e8f0; text-align: right; font-weight: bold;">
                            Rp {{ number_format($pesanan->total_sewa, 0, ',', '.') }}
                        </td>
                    </tr>
                    @if($pesanan->denda && $pesanan->denda->isNotEmpty())
                        <tr>
                            <td style="padding: 4px 8px; border-bottom: 1px solid #e2e8f0; color: #742a2a;">Denda & Kerusakan:</td>
                            <td style="padding: 4px 8px; border-bottom: 1px solid #e2e8f0; text-align: right; font-weight: bold; color: #742a2a;">
                                Rp {{ number_format($pesanan->denda->sum('jumlah'), 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif
                    <tr style="background-color: #edf2f7;">
                        <td style="padding: 5px 8px; font-weight: bold; border-bottom: 1px solid #cbd5e0;">TOTAL KESELURUHAN:</td>
                        <td style="padding: 5px 8px; text-align: right; font-weight: bold; font-size: 9.5pt; color: #0e1b15; border-bottom: 1px solid #cbd5e0;">
                            Rp {{ number_format($grandTotal, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 4px 8px; border-bottom: 1px solid #e2e8f0; color: #22543d;">Total Pembayaran Diterima:</td>
                        <td style="padding: 4px 8px; border-bottom: 1px solid #e2e8f0; text-align: right; font-weight: bold; color: #22543d;">
                            Rp {{ number_format($totalTerbayarSemua, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr style="background-color: {{ $totalKurangBayar > 0 ? '#fff5f5' : '#f0fff4' }};">
                        <td style="padding: 5px 8px; font-weight: bold; color: {{ $totalKurangBayar > 0 ? '#742a2a' : '#22543d' }};">
                            {{ $totalKurangBayar > 0 ? 'SISA TAGIHAN:' : 'STATUS PEMBAYARAN:' }}
                        </td>
                        <td style="padding: 5px 8px; text-align: right; font-weight: bold; font-size: 9.5pt; color: {{ $totalKurangBayar > 0 ? '#742a2a' : '#22543d' }};">
                            {{ $totalKurangBayar > 0 ? 'Rp ' . number_format($totalKurangBayar, 0, ',', '.') : 'LUNAS' }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- TANDA TANGAN SERAH TERIMA -->
    <table class="signature-table">
        <tr>
            <td class="sig-col">
                <div>Penyewa (Pihak Kedua),</div>
                <div style="height: 40px;"></div>
                <div style="font-weight: bold; text-decoration: underline;">{{ $pesanan->pelanggan->nama_lengkap }}</div>
                <div style="font-size: 7.5pt; color: #718096;">Tanda Tangan & Nama Terang</div>
            </td>
            <td class="sig-col">
                <div>Subang, {{ now()->translatedFormat('d F Y') }}<br>Petugas Kasir Toko (Pihak Pertama),</div>
                <div style="height: 40px;"></div>
                <div style="font-weight: bold; text-decoration: underline;">{{ $petugas }}</div>
                <div style="font-size: 7.5pt; color: #718096;">BLNTRK Outdoor Subang</div>
            </td>
        </tr>
    </table>

</body>
</html>