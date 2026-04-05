@extends('layouts.app')

@section('title', 'Log Aktivitas')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Log Aktivitas</h2>
        <button onclick="if(confirm('Yakin hapus semua log?')) document.getElementById('clearForm').submit()" 
            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition">
            <i class="fas fa-trash"></i>
            <span>Hapus Semua Log</span>
        </button>
        <form id="clearForm" action="{{ route('log.clear') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 flex justify-between items-center">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- Filter --}}
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('log.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            
            {{-- Filter User --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">User</label>
                <select name="user_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">Semua User</option>
                    @foreach($users as $user)
                        <option value="{{ $user->user_id }}" {{ request('user_id') == $user->user_id ? 'selected' : '' }}>
                            {{ $user->username }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Modul --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Modul</label>
                <select name="modul" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">Semua Modul</option>
                    @foreach($moduls as $modul)
                        <option value="{{ $modul }}" {{ request('modul') == $modul ? 'selected' : '' }}>
                            {{ $modul }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Tanggal --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>

            {{-- Tombol --}}
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg text-sm transition">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                <a href="{{ route('log.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-4 rounded-lg text-sm transition">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modul</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aktivitas</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            <div>{{ $log->timestamp->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $log->timestamp->format('H:i:s') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600 text-xs"></i>
                                </div>
                                <div class="ml-3">
                                    <div class="font-medium text-gray-900">{{ $log->user->username }}</div>
                                    <div class="text-xs text-gray-500">{{ ucfirst($log->user->level) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $log->modul == 'Auth' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ $log->modul == 'Peminjaman' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $log->modul == 'Pengembalian' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $log->modul == 'Alat' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $log->modul == 'Kategori' ? 'bg-indigo-100 text-indigo-800' : '' }}
                                {{ $log->modul == 'Users' ? 'bg-pink-100 text-pink-800' : '' }}
                                {{ !in_array($log->modul, ['Auth', 'Peminjaman', 'Pengembalian', 'Alat', 'Kategori', 'Users']) ? 'bg-gray-100 text-gray-800' : '' }}
                            ">
                                {{ $log->modul }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $log->aktivitas }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-clipboard-list text-4xl text-gray-300 mb-3 block"></i>
                            <p>Belum ada log aktivitas.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $logs->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    {{-- Info --}}
    <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <p class="text-sm text-blue-700">
            <i class="fas fa-info-circle mr-2"></i>
            <strong>Total Log:</strong> {{ $logs->total() }} aktivitas tercatat
        </p>
    </div>

@endsection