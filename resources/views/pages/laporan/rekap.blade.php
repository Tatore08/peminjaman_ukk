@extends('layouts.app')

@section('title', 'Laporan Rekap Periode')

@section('content')

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Laporan Rekap Periode</h2>
    <p class="text-gray-600 text-sm mt-1">Rekap data peminjaman dan pengembalian berdasarkan periode</p>
</div>

{{-- Filter Periode --}}
<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <form method="GET" action="{{ route('laporan.rekap') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
            <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai }}" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
            <input type="date" name="tanggal_selesai" value="{{ $tanggalSelesai }}" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg transition">
                <i class="fas fa-filter mr-2"></i> Tampilkan
            </button>
            <a href="{{ route('laporan.cetak-rekap', ['tanggal_mulai' => $tanggalMulai, 'tanggal_selesai' => $tanggalSelesai]) }}" 
                target="_blank"
                class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-lg transition">
                <i class="fas fa-print"></i>
            </a>
        </div>
    </form>
</div>

{{-- Statistik Cards --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500">
        <p class="text-sm text-gray-600">Total Peminjaman</p>
        <h3 class="text-2xl font-bold text-gray-800">{{ $stats['total_peminjaman'] }}</h3>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
        <p class="text-sm text-gray-600">Disetujui</p>
        <h3 class="text-2xl font-bold text-gray-800">{{ $stats['total_approved'] }}</h3>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-purple-500">
        <p class="text-sm text-gray-600">Dikembalikan</p>
        <h3 class="text-2xl font-bold text-gray-800">{{ $stats['total_pengembalian'] }}</h3>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-red-500">
        <p class="text-sm text-gray-600">Total Denda</p>
        <h3 class="text-2xl font-bold text-red-600">Rp {{ number_format($stats['total_denda'], 0, ',', '.') }}</h3>
    </div>
</div>

{{-- Tabel Peminjaman --}}
<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4">
        <i class="fas fa-clipboard-list text-blue-600 mr-2"></i>
        Data Peminjaman ({{ $peminjaman->count() }})
    </h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peminjam</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alat</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($peminjaman as $index => $p)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm">{{ $index + 1 }}</td>
                    <td class="px-4 py-3 text-sm">{{ $p->tanggal_peminjaman->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-sm">{{ $p->user->username }}</td>
                    <td class="px-4 py-3 text-sm">
                        {{ $p->alat->nama_alat }}
                        <span class="text-xs text-gray-500">({{ $p->alat->kode_alat }})</span>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        @if($p->status == 'pending')
                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                        @elseif($p->status == 'approved')
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Disetujui</span>
                        @elseif($p->status == 'rejected')
                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Ditolak</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Selesai</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                        Tidak ada data peminjaman dalam periode ini
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Tabel Pengembalian --}}
<div class="bg-white rounded-lg shadow-sm p-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4">
        <i class="fas fa-undo text-green-600 mr-2"></i>
        Data Pengembalian ({{ $pengembalian->count() }})
    </h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peminjam</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alat</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kondisi</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Terlambat</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Denda</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($pengembalian as $index => $pg)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm">{{ $index + 1 }}</td>
                    <td class="px-4 py-3 text-sm">{{ \Carbon\Carbon::parse($pg->tanggal_kembali_aktual)->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-sm">{{ $pg->peminjaman->user->username }}</td>
                    <td class="px-4 py-3 text-sm">
                        {{ $pg->peminjaman->alat->nama_alat }}
                        <span class="text-xs text-gray-500">({{ $pg->peminjaman->alat->kode_alat }})</span>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        @if($pg->kondisi_alat == 'baik')
                            <span class="text-green-600">Baik</span>
                        @else
                            <span class="text-red-600">Rusak</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm">
                        @if($pg->keterlambatan_hari > 0)
                            <span class="text-red-600 font-semibold">{{ $pg->keterlambatan_hari }} hari</span>
                        @else
                            <span class="text-green-600">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm font-medium">
                        @if($pg->getTotalDenda() > 0)
                            <span class="text-red-600">Rp {{ number_format($pg->getTotalDenda(), 0, ',', '.') }}</span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                        Tidak ada data pengembalian dalam periode ini
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection