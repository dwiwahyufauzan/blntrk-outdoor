<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pendapatan Keuangan - {{ $toko }}</title>
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
        .section-title {
            font-size: 10pt;
            font-weight: bold;
            color: #0e1b15;
            text-transform: uppercase;
            margin-top: 16px;
            margin-bottom: 6px;
            padding-bottom: 3px;
            border-bottom: 1px solid #cbd5e0;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
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
            padding: 5px 8px;
            border: 1px solid #cbd5e0;
            vertical-align: top;
        }
        .data-table tr:nth-child(even) {
            background-color: #f7fafc;
        }
        .summary-card {
            width: 100%;
            margin-top: 16px;
            border-collapse: collapse;
        }
        .summary-box {
            padding: 10px;
            border: 1px solid #cbd5e0;
            background-color: #edf2f7;
            text-align: center;
        }
        .summary-label {
            font-size: 8pt;
            text-transform: uppercase;
            color: #4a5568;
            font-weight: bold;
        }
        .summary-value {
            font-size: 11pt;
            font-weight: bold;
            color: #0e1b15;
            margin-top: 3px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .empty-state {
            padding: 25px;
            text-align: center;
            color: #718096;
            font-style: italic;
            border: 1px dashed #cbd5e0;
            background-color: #f7fafc;
            margin-top: 6px;
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
    <div class="doc-title">Laporan Penerimaan Kas & Pendapatan</div>
    <div class="doc-period">
        Periode: {{ $dari->translatedFormat('d F Y') }} s/d {{ $sampai->translatedFormat('d F Y') }}
    </div>

    <!-- RINGKASAN KARTU PENDAPATAN -->
    <table class="summary-card">
        <tr>
            <td style="width: 33.33%; padding-right: 5px;">
                <div class="summary-box">
                    <div class="summary-label">Pendapatan Sewa Riil</div>
                    <div class="summary-value">Rp {{ number_format($data['total_pendapatan_sewa'], 0, ',', '.') }}</div>
                </div>
            </td>
            <td style="width: 33.33%; padding-left: 2px; padding-right: 2px;">
                <div class="summary-box">
                    <div class="summary-label">Pendapatan Denda Lunas</div>
                    <div class="summary-value">Rp {{ number_format($data['total_pendapatan_denda'], 0, ',', '.') }}</div>
                </div>
            </td>
            <td style="width: 33.33%; padding-left: 5px;">
                <div class="summary-box" style="background-color: #e6fffa; border-color: #81e6d9;">
                    <div class="summary-label" style="color: #234e52;">Total Penerimaan Kas</div>
                    <div class="summary-value" style="color: #234e52;">Rp {{ number_format($data['total_pendapatan_bersih'], 0, ',', '.') }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- BAGIAN 1: PEMBAYARAN SEWA -->
    <div class="section-title">1. Rincian Kas Masuk Sewa Alat (Berdasarkan Waktu Pembayaran)</div>
    @if($data['pembayaran_sewa']->isEmpty())
        <div class="empty-state">
            Data laporan tidak tersedia
        </div>
    @else
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;" class="text-center">No</th>
                    <th style="width: 18%;">Waktu Bayar</th>
                    <th style="width: 18%;">No. Resi</th>
                    <th style="width: 25%;">Pelanggan</th>
                    <th style="width: 16%;">Metode</th>
                    <th style="width: 18%;" class="text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['pembayaran_sewa'] as $index => $bayar)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $bayar->dibayar_pada?->format('d/m/Y H:i') }}</td>
                        <td class="font-bold">{{ $bayar->pesanan?->no_resi ?? '-' }}</td>
                        <td>{{ $bayar->pesanan?->pelanggan?->nama_lengkap ?? '-' }}</td>
                        <td>{{ $bayar->metode->label() }}</td>
                        <td class="text-right font-bold">Rp {{ number_format($bayar->jumlah, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #edf2f7;">
                    <td colspan="5" class="text-right font-bold">Subtotal Pembayaran Sewa:</td>
                    <td class="text-right font-bold">Rp {{ number_format($data['total_pendapatan_sewa'], 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    @endif

    <!-- BAGIAN 2: DENDA LUNAS -->
    <div class="section-title" style="margin-top: 20px;">2. Rincian Kas Masuk Denda & Ganti Rugi (Lunas)</div>
    @if($data['denda_lunas']->isEmpty())
        <div class="empty-state">
            Data laporan tidak tersedia
        </div>
    @else
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;" class="text-center">No</th>
                    <th style="width: 18%;">Waktu Lunas</th>
                    <th style="width: 18%;">No. Resi</th>
                    <th style="width: 25%;">Pelanggan</th>
                    <th style="width: 16%;">Jenis Denda</th>
                    <th style="width: 18%;" class="text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['denda_lunas'] as $index => $denda)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ ($denda->dilunasi_pada ?? $denda->created_at)->format('d/m/Y H:i') }}</td>
                        <td class="font-bold">{{ $denda->pesanan?->no_resi ?? '-' }}</td>
                        <td>{{ $denda->pesanan?->pelanggan?->nama_lengkap ?? '-' }}</td>
                        <td>{{ $denda->jenis === \App\Enums\JenisDenda::Keterlambatan ? 'Keterlambatan' : 'Kerusakan/Hilang' }}</td>
                        <td class="text-right font-bold">Rp {{ number_format($denda->jumlah, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #edf2f7;">
                    <td colspan="5" class="text-right font-bold">Subtotal Denda Lunas:</td>
                    <td class="text-right font-bold">Rp {{ number_format($data['total_pendapatan_denda'], 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    @endif

    <!-- TANDA TANGAN & PENGESAHAN -->
    <table class="signature-table">
        <tr>
            <td style="width: 60%;">
                <div style="font-size: 8pt; color: #718096;">
                    * Pendapatan sewa dihitung berdasarkan kas masuk riil (bukan tanggal pesanan).<br>
                    * Denda yang dihitung sebagai pendapatan hanya denda yang telah lunas.
                </div>
            </td>
            <td style="width: 40%;" align="right">
                <div class="signature-box">
                    <div>Subang, {{ now()->translatedFormat('d F Y') }}</div>
                    <div style="margin-top: 4px; font-weight: bold;">Penanggung Jawab,</div>
                    <div style="height: 50px;"></div>
                    <div style="font-weight: bold; text-decoration: underline;">{{ $petugas }}</div>
                    <div style="font-size: 8pt; color: #4a5568;">Laporan Finansial</div>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
