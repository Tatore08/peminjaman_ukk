@extends('layouts.app')

@section('title', 'Laporan')

@section('content')

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Laporan</h2>
    <p class="text-gray-600 text-sm mt-1">Cetak laporan peminjaman dan pengembalian alat</p>
</div>

{{-- Menu Cards --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    
    {{-- Laporan Rekap Periode --}}
    <a href="{{ route('laporan.rekap') }}" class="block">
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500 hover:shadow-md transition group">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="bg-blue-100 p-3 rounded-full">
                            <i class="fas fa-calendar-alt text-blue-600 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 group-hover:text-blue-600 transition">
                            Laporan Rekap Periode
                        </h3>
                    </div>
                    <p class="text-sm text-gray-600">
                        Rekap peminjaman dan pengembalian berdasarkan periode tertentu dengan statistik lengkap
                    </p>
                    <div class="mt-4 flex items-center text-blue-600 text-sm font-medium">
                        <span>Lihat Laporan</span>
                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition"></i>
                    </div>
                </div>
            </div>
        </div>
    </a>

    {{-- Info Card --}}
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl shadow-sm p-6 text-white">
        <div class="flex items-start space-x-3">
            <div class="text-3xl">
                <i class="fas fa-info-circle"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold mb-2">Cara Menggunakan</h3>
                <ul class="space-y-2 text-sm text-indigo-100">
                    <li class="flex items-start">
                        <i class="fas fa-check-circle mr-2 mt-1"></i>
                        <span>Laporan peminjaman & pengembalian bisa dicetak langsung dari halaman detail</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle mr-2 mt-1"></i>
                        <span>Laporan rekap menampilkan statistik berdasarkan periode yang dipilih</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle mr-2 mt-1"></i>
                        <span>Gunakan tombol print atau Ctrl+P untuk mencetak</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

</div>

{{-- Quick Stats --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Peminjaman Bulan Ini</p>
                <h3 class="text-2xl font-bold text-gray-800">
                    {{ \App\Models\Peminjaman::whereMonth('tanggal_peminjaman', now()->month)->count() }}
                </h3>
            </div>
            <div class="bg-green-100 p-3 rounded-full">
                <i class="fas fa-calendar-check text-green-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Pengembalian Bulan Ini</p>
                <h3 class="text-2xl font-bold text-gray-800">
                    {{ \App\Models\Pengembalian::whereMonth('tanggal_kembali_aktual', now()->month)->where('status_pengembalian', 'approved')->count() }}
                </h3>
            </div>
            <div class="bg-purple-100 p-3 rounded-full">
                <i class="fas fa-undo text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Denda Bulan Ini</p>
                <h3 class="text-xl font-bold text-red-600">
                    Rp {{ number_format(\App\Models\Pengembalian::whereMonth('tanggal_kembali_aktual', now()->month)->where('status_pengembalian', 'approved')->get()->sum(fn($p) => $p->getTotalDenda()), 0, ',', '.') }}
                </h3>
            </div>
            <div class="bg-red-100 p-3 rounded-full">
                <i class="fas fa-money-bill-wave text-red-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

@endsection