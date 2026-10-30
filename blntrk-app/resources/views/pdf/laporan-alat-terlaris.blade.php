<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Alat Terlaris - {{ $toko }}</title>
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
            border-bottom: 3px double #0e1b15;
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
            <td style="width: 65%;">
                <div class="brand-title">{{ $toko }}</div>
                <div class="brand-subtitle">{{ $alamat }}</div>
                <div class="brand-subtitle">WhatsApp Toko: {{ \App\Models\Pengaturan::ambil('no_wa_toko', '0812-3456-7890') }} &bull; Email: blntrkoutdoor@gmail.com &bull; Buka 24 Jam</div>
            </td>
            <td style="width: 35%; text-align: right; font-size: 8pt; color: #718096; vertical-align: bottom;">
                <div>Tgl Cetak: {{ $tanggalCetak }} WIB</div>
                <div>Petugas: <strong>{{ $petugas }}</strong></div>
            </td>
        </tr>
    </table>

    <!-- JUDUL LAPORAN -->
    <div class="doc-title">Laporan Peringkat Alat Terlaris Disewa</div>
    <div class="doc-period">
        Periode: {{ $dari->translatedFormat('d F Y') }} s/d {{ $sampai->translatedFormat('d F Y') }}
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
                    <th style="width: 6%;" class="text-center">Rank</th>
                    <th style="width: 14%;">Kode Alat</th>
                    <th style="width: 30%;">Nama Perlengkapan</th>
                    <th style="width: 16%;">Kategori</th>
                    <th style="width: 14%;" class="text-center">Total Disewa</th>
                    <th style="width: 20%;" class="text-right">Total Perolehan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['items'] as $index => $item)
                    <tr>
                        <td class="text-center font-bold" style="background-color: {{ $index < 3 ? '#e6fffa' : 'transparent' }};">
                            #{{ $index + 1 }}
                        </td>
                        <td class="font-bold">{{ $item->alat?->kode_alat ?? '-' }}</td>
                        <td>
                            <strong>{{ $item->alat?->nama ?? 'Alat Dihapus' }}</strong><br>
                            <span style="font-size: 7.5pt; color: #718096;">Tarif: Rp {{ number_format($item->alat?->harga_per_hari ?? 0, 0, ',', '.') }} /hari</span>
                        </td>
                        <td>{{ $item->alat?->kategori?->nama ?? '-' }}</td>
                        <td class="text-center font-bold" style="font-size: 9.5pt;">
                            {{ $item->total_disewa }} unit
                        </td>
                        <td class="text-right font-bold" style="font-size: 9pt; color: #0e1b15;">
                            Rp {{ number_format($item->total_pendapatan, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #edf2f7;">
                    <td colspan="4" class="text-right font-bold" style="padding: 8px;">
                        TOTAL UNIT TERSEWA & OMZET:
                    </td>
                    <td class="text-center font-bold" style="padding: 8px; font-size: 9.5pt;">
                        {{ $data['total_unit_disewa'] }} Unit
                    </td>
                    <td class="text-right font-bold" style="padding: 8px; font-size: 9.5pt; color: #0e1b15;">
                        Rp {{ number_format($data['total_omzet_alat'], 0, ',', '.') }}
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
                    * Urutan dihitung berdasarkan akumulasi kuantitas unit yang disewa pelanggan.<br>
                    * Tidak termasuk pesanan sewa yang telah dibatalkan.
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
