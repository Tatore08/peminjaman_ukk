<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pengembalian - {{ $pengembalian->peminjaman->alat->kode_alat }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: Arial, sans-serif; 
            padding: 40px;
            font-size: 12pt;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 18pt;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 10pt;
            color: #666;
        }
        .kop-surat {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 10px;
        }
        .logo {
            width: 80px;
            height: 80px;
            background: #f0f0f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24pt;
            color: #666;
        }
        .info-sekolah {
            flex: 1;
            text-align: center;
        }
        .info-sekolah h2 {
            font-size: 16pt;
            margin-bottom: 3px;
        }
        .info-sekolah p {
            font-size: 9pt;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .detail-table td {
            padding: 8px;
            border: none;
        }
        .detail-table td:first-child {
            width: 200px;
            font-weight: bold;
        }
        .detail-table td:nth-child(2) {
            width: 20px;
        }
        .box {
            border: 2px solid #333;
            padding: 20px;
            margin: 20px 0;
        }
        .signature {
            display: flex;
            justify-content: space-between;
            margin-top: 60px;
        }
        .signature-box {
            width: 45%;
            text-align: center;
        }
        .signature-line {
            margin-top: 80px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 9pt;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .highlight {
            background: #fef3c7;
            padding: 15px;
            border-left: 4px solid #f59e0b;
            margin: 20px 0;
        }
        .kondisi-baik {
            color: #065f46;
            font-weight: bold;
        }
        .kondisi-rusak {
            color: #991b1b;
            font-weight: bold;
        }
        .denda-box {
            background: #fee2e2;
            border: 2px solid #ef4444;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .denda-box h3 {
            font-size: 14pt;
            color: #991b1b;
            margin-bottom: 10px;
        }
        .denda-amount {
            font-size: 24pt;
            font-weight: bold;
            color: #dc2626;
        }
        @media print {
            body { padding: 20px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    {{-- Tombol Print --}}
    <div class="no-print" style="text-align: right; margin-bottom: 20px;">
        <button onclick="window.print()" style="background: #10b981; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 11pt;">
            🖨️ Cetak Laporan
        </button>
        <button onclick="window.close()" style="background: #6b7280; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 11pt; margin-left: 10px;">
            ✕ Tutup
        </button>
    </div>

    {{-- Header / Kop Surat --}}
    <div class="header">
        <div class="kop-surat">
            <div class="logo">🔧</div>
            <div class="info-sekolah">
                <h2>SISTEM PEMINJAMAN ALAT</h2>
                <p>SMK NEGERI / SWASTA</p>
                <p>Jl. Contoh Alamat No. 123, Kota, Provinsi | Telp: (021) 1234567</p>
            </div>
            <div class="logo">🔧</div>
        </div>
    </div>

    {{-- Judul Laporan --}}
    <div style="text-align: center; margin: 30px 0;">
        <h1 style="font-size: 16pt;">BUKTI PENGEMBALIAN ALAT</h1>
        <p style="font-size: 10pt; color: #666;">No. Pengembalian: #{{ $pengembalian->pengembalian_id }}</p>
    </div>

    {{-- Detail Peminjaman Asal --}}
    <div class="box">
        <h3 style="margin-bottom: 15px; border-bottom: 1px solid #ddd; padding-bottom: 10px;">DATA PEMINJAMAN</h3>
        <table class="detail-table">
            <tr>
                <td>Nama Peminjam</td>
                <td>:</td>
                <td><strong>{{ $pengembalian->peminjaman->user->username }}</strong></td>
            </tr>
            <tr>
                <td>Nama Alat</td>
                <td>:</td>
                <td><strong>{{ $pengembalian->peminjaman->alat->nama_alat }}</strong></td>
            </tr>
            <tr>
                <td>Kode Alat</td>
                <td>:</td>
                <td><strong>{{ $pengembalian->peminjaman->alat->kode_alat }}</strong></td>
            </tr>
            <tr>
                <td>Tanggal Peminjaman</td>
                <td>:</td>
                <td>{{ $pengembalian->peminjaman->tanggal_peminjaman->format('d F Y') }}</td>
            </tr>
            <tr>
                <td>Tanggal Kembali (Rencana)</td>
                <td>:</td>
                <td>{{ $pengembalian->peminjaman->tanggal_kembali_rencana->format('d F Y') }}</td>
            </tr>
        </table>
    </div>

    {{-- Detail Pengembalian --}}
    <div class="box">
        <h3 style="margin-bottom: 15px; border-bottom: 1px solid #ddd; padding-bottom: 10px;">DATA PENGEMBALIAN</h3>
        <table class="detail-table">
            <tr>
                <td>Tanggal Kembali (Aktual)</td>
                <td>:</td>
                <td><strong>{{ \Carbon\Carbon::parse($pengembalian->tanggal_kembali_aktual)->format('d F Y') }}</strong></td>
            </tr>
            <tr>
                <td>Keterlambatan</td>
                <td>:</td>
                <td>
                    @if($pengembalian->keterlambatan_hari > 0)
                        <strong style="color: #dc2626;">{{ $pengembalian->keterlambatan_hari }} hari</strong>
                    @else
                        <strong style="color: #059669;">Tepat waktu</strong>
                    @endif
                </td>
            </tr>
            <tr>
                <td>Kondisi Alat</td>
                <td>:</td>
                <td>
                    @if($pengembalian->kondisi_alat == 'baik')
                        <span class="kondisi-baik">✓ BAIK - Tidak Ada Kerusakan</span>
                    @else
                        <span class="kondisi-rusak">✗ RUSAK - Ada Kerusakan</span>
                    @endif
                </td>
            </tr>
            @if($pengembalian->catatan)
            <tr>
                <td>Catatan Kerusakan</td>
                <td>:</td>
                <td style="color: #dc2626;">{{ $pengembalian->catatan }}</td>
            </tr>
            @endif
        </table>
    </div>

    {{-- Denda --}}
@php $totalDenda = $pengembalian->getTotalDenda(); @endphp

@if($totalDenda > 0)
<div class="denda-box">
    <h3>⚠️ RINCIAN DENDA</h3>
    
    <table style="width:100%; margin: 15px 0; text-align: left; font-size: 11pt;">
        {{-- Denda Keterlambatan --}}
        @if($pengembalian->denda_keterlambatan > 0)
        <tr>
            <td style="padding: 5px 0;">Denda Keterlambatan</td>
            <td style="padding: 5px 0;">:</td>
            <td style="padding: 5px 0;">
                {{ $pengembalian->keterlambatan_hari }} hari × Rp 5.000
            </td>
            <td style="padding: 5px 0; text-align: right; font-weight: bold;">
                Rp {{ number_format($pengembalian->denda_keterlambatan, 0, ',', '.') }}
            </td>
        </tr>
        @endif

        {{-- Denda Kerusakan --}}
        @if($pengembalian->persen_kerusakan > 0)
        <tr>
            <td style="padding: 5px 0;">Denda Kerusakan</td>
            <td style="padding: 5px 0;">:</td>
            <td style="padding: 5px 0;">
                {{ $pengembalian->persen_kerusakan }}% × Rp {{ number_format($pengembalian->peminjaman->alat->harga_beli, 0, ',', '.') }}
            </td>
            <td style="padding: 5px 0; text-align: right; font-weight: bold;">
                Rp {{ number_format($pengembalian->denda_kerusakan, 0, ',', '.') }}
            </td>
        </tr>
        @endif

        {{-- Garis pemisah --}}
        <tr>
            <td colspan="4" style="border-top: 2px solid #991b1b; padding-top: 8px;"></td>
        </tr>

        {{-- Total --}}
        <tr>
            <td colspan="3" style="padding: 5px 0; font-weight: bold; font-size: 13pt;">TOTAL DENDA</td>
            <td style="padding: 5px 0; text-align: right;">
                <div class="denda-amount">Rp {{ number_format($totalDenda, 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>
</div>

@else
<div style="background: #d1fae5; padding: 20px; text-align: center; border: 2px solid #10b981; margin: 20px 0;">
    <h3 style="color: #065f46; font-size: 14pt;">✓ TIDAK ADA DENDA</h3>
    <p style="color: #047857; margin-top: 10px;">Alat dikembalikan tepat waktu dan dalam kondisi baik</p>
</div>
@endif

    {{-- Tanda Tangan --}}
    <div class="signature">
        <div class="signature-box">
            <p>Peminjam</p>
            <div class="signature-line">
                <strong>{{ $pengembalian->peminjaman->user->username }}</strong>
            </div>
        </div>
        <div class="signature-box">
            <p>Petugas Penerima</p>
            <div class="signature-line">
                <strong>________________</strong>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d F Y, H:i') }} WIB</p>
        <p>Dokumen ini sah tanpa tanda tangan basah</p>
    </div>

</body>
</html>