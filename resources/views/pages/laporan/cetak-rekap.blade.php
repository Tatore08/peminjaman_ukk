<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekap Periode - {{ \Carbon\Carbon::parse($tanggalMulai)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d/m/Y') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: Arial, sans-serif; 
            padding: 30px;
            font-size: 10pt;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .kop-surat {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 10px;
        }
        .logo {
            width: 60px;
            height: 60px;
            background: #f0f0f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20pt;
            color: #666;
        }
        .info-sekolah {
            flex: 1;
            text-align: center;
        }
        .info-sekolah h2 {
            font-size: 14pt;
            margin-bottom: 3px;
        }
        .info-sekolah p {
            font-size: 8pt;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 9pt;
        }
        table th {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin: 20px 0;
        }
        .stat-card {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
        }
        .stat-card h4 {
            font-size: 8pt;
            color: #666;
            margin-bottom: 5px;
        }
        .stat-card p {
            font-size: 16pt;
            font-weight: bold;
        }
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            margin: 20px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #333;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 9pt;
        }
        .signature {
            margin-top: 50px;
            text-align: right;
        }
        .signature-box {
            display: inline-block;
            width: 200px;
            text-align: center;
        }
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }
        @media print {
            body { padding: 15px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    {{-- Tombol Print --}}
    <div class="no-print" style="text-align: right; margin-bottom: 20px;">
        <button onclick="window.print()" style="background: #3b82f6; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
            🖨️ Cetak Laporan
        </button>
        <button onclick="window.close()" style="background: #6b7280; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
            ✕ Tutup
        </button>
    </div>

    {{-- Header --}}
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

    {{-- Judul --}}
    <div style="text-align: center; margin: 20px 0;">
        <h1 style="font-size: 14pt;">LAPORAN REKAP PEMINJAMAN ALAT</h1>
        <p style="font-size: 9pt; color: #666; margin-top: 5px;">
            Periode: {{ \Carbon\Carbon::parse($tanggalMulai)->format('d F Y') }} s/d {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d F Y') }}
        </p>
    </div>

    {{-- Statistik --}}
    <div class="stats-grid">
        <div class="stat-card">
            <h4>Total Peminjaman</h4>
            <p>{{ $stats['total_peminjaman'] }}</p>
        </div>
        <div class="stat-card">
            <h4>Disetujui</h4>
            <p style="color: #10b981;">{{ $stats['total_approved'] }}</p>
        </div>
        <div class="stat-card">
            <h4>Pengembalian</h4>
            <p style="color: #8b5cf6;">{{ $stats['total_pengembalian'] }}</p>
        </div>
        <div class="stat-card">
            <h4>Total Denda</h4>
            <p style="color: #ef4444;">Rp {{ number_format($stats['total_denda'], 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Data Peminjaman --}}
    <div class="section-title">📋 DATA PEMINJAMAN ({{ $peminjaman->count() }})</div>
    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Tanggal</th>
                <th>Peminjam</th>
                <th>Alat</th>
                <th>Kode</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($peminjaman as $index => $p)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $p->tanggal_peminjaman->format('d/m/Y') }}</td>
                <td>{{ $p->user->username }}</td>
                <td>{{ $p->alat->nama_alat }}</td>
                <td>{{ $p->alat->kode_alat }}</td>
                <td>
                    @if($p->status == 'pending') Pending
                    @elseif($p->status == 'approved') Disetujui
                    @elseif($p->status == 'rejected') Ditolak
                    @else Selesai
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; color: #999;">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Data Pengembalian --}}
    <div class="section-title">🔄 DATA PENGEMBALIAN ({{ $pengembalian->count() }})</div>
    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Tanggal</th>
                <th>Peminjam</th>
                <th>Alat</th>
                <th>Kode</th>
                <th>Kondisi</th>
                <th>Terlambat</th>
                <th>Denda</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengembalian as $index => $pg)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($pg->tanggal_kembali_aktual)->format('d/m/Y') }}</td>
                <td>{{ $pg->peminjaman->user->username }}</td>
                <td>{{ $pg->peminjaman->alat->nama_alat }}</td>
                <td>{{ $pg->peminjaman->alat->kode_alat }}</td>
                <td>{{ ucfirst($pg->kondisi_alat) }}</td>
                <td>{{ $pg->keterlambatan_hari > 0 ? $pg->keterlambatan_hari . ' hari' : '-' }}</td>
                <td style="text-align: right;">{{ $pg->getTotalDenda() > 0 ? 'Rp ' . number_format($pg->getTotalDenda(), 0, ',', '.') : '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; color: #999;">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Ringkasan --}}
    <div style="margin-top: 30px; padding: 15px; background: #f9fafb; border: 1px solid #ddd; border-radius: 5px;">
        <h3 style="font-size: 11pt; margin-bottom: 10px;">RINGKASAN</h3>
        <table style="border: none;">
            <tr>
                <td style="border: none; width: 250px;">Total Peminjaman Periode Ini</td>
                <td style="border: none; width: 20px;">:</td>
                <td style="border: none;"><strong>{{ $stats['total_peminjaman'] }} peminjaman</strong></td>
            </tr>
            <tr>
                <td style="border: none;">Total Pengembalian</td>
                <td style="border: none;">:</td>
                <td style="border: none;"><strong>{{ $stats['total_pengembalian'] }} pengembalian</strong></td>
            </tr>
            <tr>
                <td style="border: none;">Total Keterlambatan</td>
                <td style="border: none;">:</td>
                <td style="border: none;"><strong>{{ $stats['total_terlambat'] }} kasus</strong></td>
            </tr>
            <tr>
                <td style="border: none;">Total Denda Terkumpul</td>
                <td style="border: none;">:</td>
                <td style="border: none;"><strong style="color: #dc2626;">Rp {{ number_format($stats['total_denda'], 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>

    {{-- Tanda Tangan --}}
    <div class="signature">
        <div class="signature-box">
            <p>{{ now()->format('d F Y') }}</p>
            <p style="margin-top: 5px;">Petugas</p>
            <div class="signature-line">
                <strong>________________</strong>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d F Y, H:i') }} WIB</p>
    </div>

</body>
</html>