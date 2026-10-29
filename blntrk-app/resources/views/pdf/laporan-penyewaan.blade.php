<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penyewaan - {{ $toko }}</title>
    <style>
        @page {
            margin: 1.5cm 1.5cm 2cm 1.5cm;
        }
        body {
            font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif;
            color: #1a202c;
            font-size: 9.5pt;
            line-height: 1.4;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0e1b15;
            padding-bottom: 8px;
            margin-bottom: 16px;
        }
        .brand-title {
            font-size: 16pt;
            font-weight: bold;
            color: #0e1b15;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .brand-subtitle {
            font-size: 8.5pt;
            color: #4a5568;
            margin-top: 2px;
        }
        .doc-title {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            color: #0e1b15;
            text-transform: uppercase;
            margin-top: 10px;
            margin-bottom: 3px;
        }
        .doc-period {
            text-align: center;
            font-size: 9pt;
            color: #4a5568;
            margin-bottom: 16px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 8.5pt;
        }
        .data-table th {
            background-color: #0e1b15;
            color: #ffffff;
            font-weight: bold;
            padding: 6px 8px;
            text-align: left;
            border: 1px solid #0e1b15;
        }
        .data-table td {
            padding: 6px 8px;
            border: 1px solid #cbd5e0;
            vertical-align: top;
        }
        .data-table tr:nth-child(even) {
            background-color: #f7fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 7.5pt;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .badge-selesai { background-color: #e6fffa; color: #234e52; border: 1px solid #81e6d9; }
        .badge-disewa { background-color: #ebf8ff; color: #2a4365; border: 1px solid #90cdf4; }
        .badge-dibayar { background-color: #f0fff4; color: #22543d; border: 1px solid #9ae6b4; }
        .badge-menunggu { background-color: #fffaf0; color: #744210; border: 1px solid #fbd38d; }
        .badge-terlambat { background-color: #fff5f5; color: #742a2a; border: 1px solid #feb2b2; }
        .badge-dibatalkan { background-color: #edf2f7; color: #4a5568; border: 1px solid #cbd5e0; }
        .empty-state {
            padding: 30px;
            text-align: center;
            color: #718096;
            font-style: italic;
            border: 1px dashed #cbd5e0;
            background-color: #f7fafc;
        }
        .signature-table {
            width: 100%;
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .signature-box {
            width: 220px;
            text-align: center;
            font-size: 9pt;
        }
    </style>
</head>
<body>

    <!-- KOP DOKUMEN -->
    <table class="header-table">
        <tr>
            <td style="width: 70%;">
                <div class="brand-title">{{ $toko }}</div>
                <div class="brand-subtitle">{{ $alamat }}</div>
                <div class="brand-subtitle">Layanan Reservasi & Rental Perlengkapan Outdoor Subang</div>
            </td>
            <td style="width: 30%; text-align: right; font-size: 8pt; color: #718096;">
                Dicetak: {{ $tanggalCetak }} WIB<br>
                Petugas: {{ $petugas }}
            </td>
        </tr>
    </table>

    <!-- JUDUL LAPORAN -->
    <div class="doc-title">Laporan Transaksi Penyewaan</div>
    <div class="doc-period">
        Periode: {{ $dari->translatedFormat('d F Y') }} s/d {{ $sampai->translatedFormat('d F Y') }}
        @if(!empty($status))
            &bull; Status: {{ strtoupper($status) }}
        @endif
    </div>

    <!-- TABEL DATA -->
    @if($data['items']->isEmpty())
        <div class="empty-state">
            Data laporan tidak tersedia
        </div>
    @else
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 4%;" class="text-center">No</th>
                    <th style="width: 16%;">No. Resi</th>
                    <th style="width: 20%;">Penyewa</th>
                    <th style="width: 20%;">Jadwal Sewa</th>
                    <th style="width: 8%;" class="text-center">Durasi</th>
                    <th style="width: 14%;" class="text-center">Status</th>
                    <th style="width: 18%;" class="text-right">Total Biaya</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['items'] as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="font-bold">{{ $item->no_resi }}</td>
                        <td>
                            <strong>{{ $item->pelanggan?->nama_lengkap ?? '-' }}</strong><br>
                            <span style="font-size: 7.5pt; color: #718096;">{{ $item->pelanggan?->no_hp ?? '' }}</span>
                        </td>
                        <td>
                            {{ $item->tanggal_mulai->format('d/m/Y') }} &ndash; {{ $item->tanggal_selesai->format('d/m/Y') }}
                        </td>
                        <td class="text-center">{{ $item->durasi_hari }} Hari</td>
                        <td class="text-center">
                            @php
                                $badgeClass = match($item->status->value) {
                                    'selesai' => 'badge-selesai',
                                    'disewa' => 'badge-disewa',
                                    'dibayar' => 'badge-dibayar',
                                    'menunggu' => 'badge-menunggu',
                                    'terlambat' => 'badge-terlambat',
                                    default => 'badge-dibatalkan',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $item->status->label() }}</span>
                        </td>
                        <td class="text-right font-bold">
                            Rp {{ number_format($item->total_sewa, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #edf2f7;">
                    <td colspan="6" class="text-right font-bold" style="padding: 8px;">
                        TOTAL NILAI PENYEWAAN ({{ $data['total_pesanan'] }} Pesanan):
                    </td>
                    <td class="text-right font-bold" style="padding: 8px; font-size: 9.5pt; color: #0e1b15;">
                        Rp {{ number_format($data['total_sewa'], 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    @endif

    <!-- TANDA TANGAN & PENGESAHAN -->
    <table class="signature-table">
        <tr>
            <td style="width: 60%;">
                <div style="font-size: 8pt; color: #718096;">
                    * Dokumen laporan ini dibuat otomatis oleh sistem BLNTRK Outdoor.<br>
                    * Sah tanpa tanda tangan basah jika terdapat nomor resi dan stempel elektronik.
                </div>
            </td>
            <td style="width: 40%;" align="right">
                <div class="signature-box">
                    <div>Subang, {{ now()->translatedFormat('d F Y') }}</div>
                    <div style="margin-top: 4px; font-weight: bold;">Penanggung Jawab,</div>
                    <div style="height: 50px;"></div>
                    <div style="font-weight: bold; text-decoration: underline;">{{ $petugas }}</div>
                    <div style="font-size: 8pt; color: #4a5568;">Petugas Operasional</div>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
