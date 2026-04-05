<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Peminjaman - {{ $peminjaman->alat->kode_alat }}</title>
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
        .box-title {
            font-weight: bold;
            font-size: 14pt;
            margin-bottom: 15px;
            text-align: center;
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
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 10pt;
        }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-approved { background: #dbeafe; color: #1e40af; }
        .status-rejected { background: #fee2e2; color: #991b1b; }
        .status-returned { background: #d1fae5; color: #065f46; }
        @media print {
            body { padding: 20px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    {{-- Tombol Print --}}
    <div class="no-print" style="text-align: right; margin-bottom: 20px;">
        <button onclick="window.print()" style="background: #3b82f6; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 11pt;">
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
        <h1 style="font-size: 16pt;">BUKTI PEMINJAMAN ALAT</h1>
        <p style="font-size: 10pt; color: #666;">No. Peminjaman: #{{ $peminjaman->peminjaman_id }}</p>
    </div>

    {{-- Detail Peminjaman --}}
    <div class="box">
        <table class="detail-table">
            <tr>
                <td>Nama Peminjam</td>
                <td>:</td>
                <td><strong>{{ $peminjaman->user->username }}</strong></td>
            </tr>
            <tr>
                <td>Nama Alat</td>
                <td>:</td>
                <td><strong>{{ $peminjaman->alat->nama_alat }}</strong></td>
            </tr>
            <tr>
                <td>Kode Alat</td>
                <td>:</td>
                <td><strong>{{ $peminjaman->alat->kode_alat }}</strong></td>
            </tr>
            <tr>
                <td>Kategori</td>
                <td>:</td>
                <td>{{ $peminjaman->alat->kategori->nama_kategori }}</td>
            </tr>
            <tr>
                <td>Tanggal Peminjaman</td>
                <td>:</td>
                <td>{{ $peminjaman->tanggal_peminjaman->format('d F Y') }}</td>
            </tr>
            <tr>
                <td>Tanggal Kembali (Rencana)</td>
                <td>:</td>
                <td>{{ $peminjaman->tanggal_kembali_rencana->format('d F Y') }}</td>
            </tr>
            <tr>
                <td>Tujuan Peminjaman</td>
                <td>:</td>
                <td>{{ $peminjaman->catatan ?? '-' }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>:</td>
                <td>
                    @if($peminjaman->status == 'pending')
                        <span class="status-badge status-pending">PENDING</span>
                    @elseif($peminjaman->status == 'approved')
                        <span class="status-badge status-approved">DISETUJUI</span>
                    @elseif($peminjaman->status == 'rejected')
                        <span class="status-badge status-rejected">DITOLAK</span>
                    @else
                        <span class="status-badge status-returned">DIKEMBALIKAN</span>
                    @endif
                </td>
            </tr>
            @if($peminjaman->disetujui_oleh)
            <tr>
                <td>Disetujui Oleh</td>
                <td>:</td>
                <td>{{ $peminjaman->penyetuju->username ?? '-' }}</td>
            </tr>
            @endif
        </table>
    </div>

    {{-- Catatan Penting --}}
    <div style="background: #fef3c7; padding: 15px; border-left: 4px solid #f59e0b; margin: 20px 0;">
        <strong>⚠️ PERHATIAN:</strong>
        <ul style="margin-left: 20px; margin-top: 10px;">
            <li>Alat harus dikembalikan sesuai tanggal yang tertera</li>
            <li>Keterlambatan pengembalian dikenakan denda Rp 5.000/hari</li>
            <li>Kerusakan alat menjadi tanggung jawab peminjam</li>
            <li>Harap kembalikan alat dalam kondisi baik</li>
        </ul>
    </div>

    {{-- Tanda Tangan --}}
    <div class="signature">
        <div class="signature-box">
            <p>Peminjam</p>
            <div class="signature-line">
                <strong>{{ $peminjaman->user->username }}</strong>
            </div>
        </div>
        <div class="signature-box">
            <p>Petugas</p>
            <div class="signature-line">
                <strong>{{ $peminjaman->penyetuju->username ?? '________________' }}</strong>
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