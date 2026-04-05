@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>
    <p class="text-gray-600 text-sm mt-1">Selamat datang, <strong>{{ auth()->user()->username }}</strong>!</p>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    
    {{-- Total Users (Admin only) --}}
    @if(auth()->user()->level == 'admin')
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Total Users</p>
                <h3 class="text-3xl font-bold text-gray-800">{{ $stats['total_users'] }}</h3>
            </div>
            <div class="bg-blue-100 p-4 rounded-full">
                <i class="fas fa-users text-blue-500 text-2xl"></i>
            </div>
        </div>
    </div>
    @endif

    {{-- Total Alat --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Total Alat</p>
                <h3 class="text-3xl font-bold text-gray-800">{{ $stats['total_alat'] }}</h3>
                <p class="text-xs text-gray-500 mt-1">
                    Tersedia: {{ $stats['alat_tersedia'] }} | Rusak: {{ $stats['alat_rusak'] }}
                </p>
            </div>
            <div class="bg-green-100 p-4 rounded-full">
                <i class="fas fa-box text-green-500 text-2xl"></i>
            </div>
        </div>
    </div>

    {{-- Peminjaman Pending --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Peminjaman Pending</p>
                <h3 class="text-3xl font-bold text-gray-800">{{ $stats['peminjaman_pending'] }}</h3>
            </div>
            <div class="bg-yellow-100 p-4 rounded-full">
                <i class="fas fa-hourglass-half text-yellow-500 text-2xl"></i>
            </div>
        </div>
    </div>

    {{-- Peminjaman Aktif --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Peminjaman Aktif</p>
                <h3 class="text-3xl font-bold text-gray-800">{{ $stats['peminjaman_aktif'] }}</h3>
                @if($stats['peminjaman_terlambat'] > 0)
                <p class="text-xs text-red-500 mt-1">
                    <i class="fas fa-exclamation-triangle"></i> {{ $stats['peminjaman_terlambat'] }} terlambat
                </p>
                @endif
            </div>
            <div class="bg-purple-100 p-4 rounded-full">
                <i class="fas fa-clipboard-list text-purple-500 text-2xl"></i>
            </div>
        </div>
    </div>

    {{-- Total Pengembalian --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-indigo-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Total Pengembalian</p>
                <h3 class="text-3xl font-bold text-gray-800">{{ $stats['pengembalian_total'] }}</h3>
                @if($stats['pengembalian_pending'] > 0)
                <p class="text-xs text-yellow-600 mt-1">
                    <i class="fas fa-clock"></i> {{ $stats['pengembalian_pending'] }} pending
                </p>
                @endif
            </div>
            <div class="bg-indigo-100 p-4 rounded-full">
                <i class="fas fa-check-circle text-indigo-500 text-2xl"></i>
            </div>
        </div>
    </div>

    {{-- Total Denda --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Total Denda</p>
                <h3 class="text-3xl font-bold text-gray-800">Rp {{ number_format($stats['total_denda'], 0, ',', '.') }}</h3>
            </div>
            <div class="bg-red-100 p-4 rounded-full">
                <i class="fas fa-money-bill-wave text-red-500 text-2xl"></i>
            </div>
        </div>
    </div>

</div>

{{-- Recent Activities --}}
<div class="bg-white rounded-xl shadow-sm p-6 mb-8">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-gray-800">
            <i class="fas fa-history text-gray-600 mr-2"></i>
            Aktivitas Terbaru
        </h3>
        <a href="{{ route('peminjaman.index') }}" class="text-sm text-blue-500 hover:text-blue-700">
            Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>

    @if($recentPeminjaman->count() > 0)
    <div class="space-y-3">
        @foreach($recentPeminjaman as $item)
        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
            <div class="flex items-center space-x-4">
                {{-- Icon Status --}}
                <div class="flex-shrink-0">
                    @if($item->status == 'pending')
                        <div class="bg-yellow-100 p-3 rounded-full">
                            <i class="fas fa-clock text-yellow-600"></i>
                        </div>
                    @elseif($item->status == 'approved')
                        <div class="bg-blue-100 p-3 rounded-full">
                            <i class="fas fa-check text-blue-600"></i>
                        </div>
                    @elseif($item->status == 'rejected')
                        <div class="bg-red-100 p-3 rounded-full">
                            <i class="fas fa-times text-red-600"></i>
                        </div>
                    @else
                        <div class="bg-green-100 p-3 rounded-full">
                            <i class="fas fa-check-double text-green-600"></i>
                        </div>
                    @endif
                </div>

                {{-- Info --}}
                <div>
                    <div class="font-medium text-gray-900">
                        {{ $item->alat->nama_alat }}
                        <span class="text-xs text-gray-500 font-mono ml-2">({{ $item->alat->kode_alat }})</span>
                    </div>
                    <div class="text-sm text-gray-600">
                        @if(auth()->user()->level != 'peminjam')
                            {{ $item->user->username }} •
                        @endif
                        {{ $item->tanggal_peminjaman->format('d M Y') }}
                    </div>
                </div>
            </div>

            {{-- Status Badge --}}
            <div>
                @if($item->status == 'pending')
                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                        Pending
                    </span>
                @elseif($item->status == 'approved')
                    @if($item->isTerlambat())
                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                            Terlambat {{ $item->hariTerlambat() }}h
                        </span>
                    @else
                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            Dipinjam
                        </span>
                    @endif
                @elseif($item->status == 'rejected')
                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                        Ditolak
                    </span>
                @else
                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                        Selesai
                    </span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-12 text-gray-400">
        <i class="fas fa-inbox text-4xl mb-3"></i>
        <p class="text-sm">Belum ada aktivitas</p>
    </div>
    @endif
</div>

{{-- Welcome Message
<div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-sm p-6 text-white">
    <div class="flex items-center space-x-3">
        <div class="text-4xl">
            <i class="fas fa-hand-paper"></i>
        </div>
        <div>
            <h3 class="text-xl font-bold mb-1">Selamat Datang!</h3>
            <p class="text-blue-100 text-sm">
                Sistem Peminjaman Alat ini membantu Anda mengelola peminjaman alat dengan mudah. 
                Gunakan menu di sidebar untuk mengakses berbagai fitur.
            </p>
        </div>
    </div>
</div> --}}

@endsection