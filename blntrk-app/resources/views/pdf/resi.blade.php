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
            border-bottom: 2px solid #0e1b15;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .brand-title {
            font-size: 15pt;
            font-weight: bold;
            color: #0e1b15;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .brand-subtitle {
            font-size: 8pt;
            color: #4a5568;
            margin-top: 1px;
        }
        .resi-title {
            font-size: 13pt;
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
            margin-top: 2px;
        }
        .badge-dibayar { background-color: #f0fff4; color: #22543d; border: 1px solid #9ae6b4; }
        .badge-disewa { background-color: #ebf8ff; color: #2a4365; border: 1px solid #90cdf4; }
        .badge-selesai { background-color: #e6fffa; color: #234e52; border: 1px solid #81e6d9; }
        .badge-menunggu { background-color: #fffaf0; color: #744210; border: 1px solid #fbd38d; }
        .badge-terlambat { background-color: #fff5f5; color: #742a2a; border: 1px solid #feb2b2; }
        .badge-dibatalkan { background-color: #edf2f7; color: #4a5568; border: 1px solid #cbd5e0; }
        
        .info-table {
            width: 100%;
            margin-top: 8px;
            margin-bottom: 12px;
            border-collapse: collapse;
        }
        .info-box {
            padding: 8px;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            border-radius: 4px;
            vertical-align: top;
        }
        .info-label {
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #718096;
            margin-bottom: 3px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
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
            background-color: #f7fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

        .terms-box {
            margin-top: 12px;
            padding: 8px 10px;
            background-color: #f7fafc;
            border: 1px solid #e2e8f0;
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
            margin-top: 20px;
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

    <!-- KOP RESI -->
    <table class="header-table">
        <tr>
            <td style="width: 60%;">
                <div class="brand-title">{{ $toko }}</div>
                <div class="brand-subtitle">{{ $alamat }}</div>
                <div class="brand-subtitle">WhatsApp Toko: {{ \App\Models\Pengaturan::ambil('no_wa_toko', '0812-3456-7890') }} &bull; Buka 24 Jam</div>
            </td>
            <td style="width: 40%; text-align: right;">
                <div class="brand-subtitle" style="text-transform: uppercase; font-weight: bold; color: #718096;">Bukti Reservasi & Resi Sewa</div>
                <div class="resi-title">{{ $pesanan->no_resi }}</div>
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
            </td>
        </tr>
    </table>

    <!-- INFO PENYEWA & JADWAL SEWA -->
    <table class="info-table">
        <tr>
            <td style="width: 50%; padding-right: 5px;">
                <div class="info-box">
                    <div class="info-label">Identitas Penyewa:</div>
                    <div style="font-size: 10pt; font-weight: bold; color: #0e1b15;">{{ $pesanan->pelanggan->nama_lengkap }}</div>
                    <div style="font-family: monospace; color: #2d3748; margin-top: 1px;">{{ $pesanan->pelanggan->no_hp }}</div>
                    <div style="color: #4a5568; margin-top: 1px;">{{ $pesanan->pelanggan->alamat }}</div>
                </div>
            </td>
            <td style="width: 50%; padding-left: 5px;">
                <div class="info-box">
                    <div class="info-label">Jadwal & Waktu Sewa:</div>
                    <div><strong>Tgl Mulai Ambil:</strong> {{ $pesanan->tanggal_mulai->translatedFormat('d F Y') }}</div>
                    <div><strong>Batas Pengembalian:</strong> {{ $pesanan->tanggal_selesai->translatedFormat('d F Y') }} (12:00 WIB)</div>
                    <div><strong>Durasi Sewa:</strong> {{ $pesanan->durasi_hari }} Hari (24 Jam x {{ $pesanan->durasi_hari }})</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- STATUS PEMBAYARAN & JAMINAN (JIKA ADA) -->
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
                            <div class="info-label" style="color: #22543d;">Pembayaran Dikonfirmasi:</div>
                            <div>Nominal: <strong>Rp {{ number_format($totalBayar, 0, ',', '.') }}</strong> ({{ $bayarPertama->metode->label() }})</div>
                            <div style="font-size: 7.5pt; color: #4a5568;">Lunas pada {{ $bayarPertama->dibayar_pada?->format('d/m/Y H:i') }} WIB &bull; Kasir: {{ $bayarPertama->pencatat?->nama ?? 'Admin' }}</div>
                        </div>
                    </td>
                @endif
                @if($pesanan->jaminan)
                    <td style="width: {{ $adaBayar ? '50%' : '100%' }}; padding-left: {{ $adaBayar ? '5px' : '0' }};">
                        <div class="info-box" style="background-color: #ebf8ff; border-color: #90cdf4;">
                            <div class="info-label" style="color: #2a4365;">Jaminan Kartu Identitas Fisik:</div>
                            <div>Kartu: <strong>{{ is_string($pesanan->jaminan->jenis) ? strtoupper($pesanan->jaminan->jenis) : $pesanan->jaminan->jenis->label() }}</strong> &bull; No: <span style="font-family: monospace;">{{ $pesanan->jaminan->nomor_identitas_samar ?? $pesanan->jaminan->nomor_identitas }}</span></div>
                            <div style="font-size: 7.5pt; color: #4a5568;">Status: {{ $pesanan->jaminan->dikembalikan_pada ? 'Dikembalikan (' . $pesanan->jaminan->dikembalikan_pada->format('d/m/Y H:i') . ')' : 'Disimpan di kasir toko' }}</div>
                        </div>
                    </td>
                @endif
            </tr>
        </table>
    @endif

    <!-- TABEL DAFTAR ALAT -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 45%;">Perlengkapan Outdoor</th>
                <th style="width: 15%;" class="text-center">Kuantitas</th>
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
                        <span style="font-size: 7.5pt; color: #718096;">Kode: {{ $item->alat?->kode_alat }} &bull; {{ $item->alat?->kategori?->nama }}</span>
                    </td>
                    <td class="text-center font-bold">{{ $item->jumlah }} unit</td>
                    <td class="text-right">Rp {{ number_format($item->harga_per_hari, 0, ',', '.') }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #edf2f7;">
                <td colspan="4" class="text-right font-bold" style="padding: 6px 8px;">
                    TOTAL BIAYA SEWA ({{ $pesanan->durasi_hari }} HARI):
                </td>
                <td class="text-right font-bold" style="padding: 6px 8px; font-size: 10pt; color: #0e1b15;">
                    Rp {{ number_format($pesanan->total_sewa, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- SYARAT & KETENTUAN PENGAMBILAN -->
    <div class="terms-box">
        <div class="terms-title">Petunjuk Pengambilan & Ketentuan Rental BLNTRK Outdoor:</div>
        <ol style="margin: 0; padding-left: 14px;">
            <li>Tunjukkan dokumen resi resmi ini (cetak fisik atau tampilan layar ponsel) kepada petugas kasir saat pengambilan.</li>
            <li>Wajib menyerahkan kartu identitas fisik asli (KTP/SIM/Kartu Pelajar) yang masih aktif sebagai jaminan keamanan peralatan.</li>
            <li>Batas waktu pengembalian alat adalah maksimal tanggal <strong>{{ $pesanan->tanggal_selesai->format('d/m/Y') }}</strong> pukul <strong>12:00 WIB</strong>. Keterlambatan dikenakan tarif sewa harian.</li>
            <li>Segala bentuk kerusakan alat atau kehilangan perlengkapan menjadi tanggung jawab penyewa sesuai tarif penggantian toko.</li>
        </ol>
    </div>

    <!-- TANDA TANGAN SERAH TERIMA -->
    <table class="signature-table">
        <tr>
            <td class="sig-col">
                <div>Penyewa,</div>
                <div style="height: 45px;"></div>
                <div style="font-weight: bold; text-decoration: underline;">{{ $pesanan->pelanggan->nama_lengkap }}</div>
                <div style="font-size: 7.5pt; color: #718096;">Pihak Kedua</div>
            </td>
            <td class="sig-col">
                <div>Subang, {{ now()->translatedFormat('d F Y') }}<br>Petugas Kasir / Admin,</div>
                <div style="height: 45px;"></div>
                <div style="font-weight: bold; text-decoration: underline;">{{ $petugas }}</div>
                <div style="font-size: 7.5pt; color: #718096;">BLNTRK Outdoor</div>
            </td>
        </tr>
    </table>

</body>
</html>
