@extends('layouts.app')

@section('title', 'Peminjaman')

@section('content')

{{-- Success Message --}}
@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 flex justify-between items-center">
        <span>{{ session('success') }}</span>
        <button onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
    </div>
@endif

{{-- Error Message --}}
@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 flex justify-between items-center">
        <span>{{ session('error') }}</span>
        <button onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
    </div>
@endif

{{-- Validation Errors --}}
@if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif


{{-- ================================================ --}}
{{-- LAYOUT PEMINJAM: 2 KOLOM (Form Kiri + Riwayat Kanan) --}}
{{-- ================================================ --}}
@if(auth()->user()->level == 'peminjam')

<h2 class="text-2xl font-bold text-gray-800 mb-6">Peminjaman Alat</h2>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    
    {{-- KOLOM KIRI: FORM AJUKAN PEMINJAMAN --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center space-x-2 mb-4">
            <div class="bg-blue-100 p-2 rounded-lg">
                <i class="fas fa-plus-circle text-blue-500"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800">Ajukan Peminjaman</h3>
        </div>

        <form action="{{ route('peminjaman.store') }}" method="POST" class="space-y-4">
            @csrf

            {{-- Pilih Unit Alat --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Pilih Alat <span class="text-red-500">*</span>
                </label>
                <select name="alat_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    <option value="">-- Pilih Alat --</option>
                    @foreach($alatList as $alat)
                        <option value="{{ $alat->alat_id }}" 
                            {{ isset($selectedAlatId) && $selectedAlatId == $alat->alat_id ? 'selected' : '' }}>
                            {{ $alat->nama_alat }} - {{ $alat->kode_alat }}
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Total {{ $alatList->count() }} unit tersedia</p>
            </div>

            {{-- Tanggal Peminjaman (readonly, auto hari ini) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tanggal Peminjaman
                </label>
                <input type="text" value="{{ date('d/m/Y') }}" readonly
                    class="w-full px-3 py-2 border border-gray-200 bg-gray-50 rounded-lg text-sm text-gray-500">
            </div>

            {{-- Tanggal Kembali Rencana --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tanggal Kembali Rencana <span class="text-red-500">*</span>
                </label>
                <input type="date" name="tanggal_kembali_rencana" required
                    min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
            </div>

            {{-- Tujuan Peminjaman --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tujuan Peminjaman</label>
                <textarea name="catatan" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm resize-none"
                    placeholder="Untuk keperluan..."></textarea>
            </div>

            {{-- Tombol Submit --}}
            <button type="submit"
                class="w-full bg-blue-500 hover:bg-blue-600 text-white py-3 rounded-lg transition font-medium text-sm">
                <i class="fas fa-paper-plane mr-2"></i> Ajukan Peminjaman
            </button>
        </form>
    </div>

    {{-- KOLOM KANAN: RIWAYAT PEMINJAMAN --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center space-x-2 mb-4">
            <div class="bg-purple-100 p-2 rounded-lg">
                <i class="fas fa-history text-purple-500"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800">Riwayat Peminjaman</h3>
        </div>

        {{-- Cards Riwayat --}}
        <div class="space-y-3 max-h-[600px] overflow-y-auto pr-2">
            @forelse($peminjaman as $item)
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                    {{-- Header: Nama Alat + Kode --}}
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex-1">
                            <h4 class="font-bold text-gray-800">{{ $item->alat->nama_alat }}</h4>
                            <p class="text-xs text-gray-500 font-mono">{{ $item->alat->kode_alat }}</p>
                            <p class="text-xs text-gray-500">{{ $item->tanggal_peminjaman->format('d/m/Y') }} - {{ $item->tanggal_kembali_rencana->format('d/m/Y') }}</p>
                        </div>
                        
                        {{-- Status Badge --}}
                        @if($item->status == 'pending')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock"></i> Pending
                            </span>
                        @elseif($item->status == 'approved')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                <i class="fas fa-check"></i> Dipinjam
                            </span>
                        @elseif($item->status == 'rejected')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                <i class="fas fa-times"></i> Ditolak
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check-double"></i> Dikembalikan
                            </span>
                        @endif
                    </div>

                    {{-- Detail --}}
                    <div class="text-sm space-y-1 mb-3">
                        @if($item->penyetuju)
                        <div class="flex justify-between text-gray-600">
                            <span>Disetujui oleh:</span>
                            <span class="font-medium">{{ $item->penyetuju->username }}</span>
                        </div>
                        @endif
                        @if($item->catatan)
                        <div class="text-gray-500 text-xs mt-2">
                            <span class="font-medium">Tujuan:</span> {{ $item->catatan }}
                        </div>
                        @endif
                    </div>

                    {{-- Actions --}}
                    @if($item->status == 'pending')
                        <form action="{{ route('peminjaman.cancel', $item->peminjaman_id) }}" method="POST"
                            onsubmit="return confirm('Yakin batalkan peminjaman ini?')">
                            @csrf
                            <button type="submit"
                                class="w-full text-xs text-red-600 hover:text-red-800 font-medium py-1">
                                <i class="fas fa-times-circle mr-1"></i> Batalkan
                            </button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="text-center py-12 text-gray-400">
                    <i class="fas fa-inbox text-4xl mb-3"></i>
                    <p class="text-sm">Belum ada riwayat peminjaman</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination Peminjam --}}
        @if($peminjaman->hasPages())
            <div class="mt-4 pt-4 border-t">
                {{ $peminjaman->links() }}
            </div>
        @endif
    </div>

</div>

@else

{{-- ================================================ --}}
{{-- LAYOUT ADMIN & PETUGAS: TABLE --}}
{{-- ================================================ --}}
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Data Peminjaman</h2>
    <button onclick="openModal('tambah')" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition">
        <i class="fas fa-plus"></i>
        <span>Ajukan Peminjaman</span>
    </button>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peminjam</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alat (Kode)</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($peminjaman as $item)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $item->user->username }}
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <div class="font-medium text-gray-900">{{ $item->alat->nama_alat }}</div>
                        <div class="text-xs text-gray-500 font-mono">{{ $item->alat->kode_alat }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        <div>{{ $item->tanggal_peminjaman->format('d/m/Y') }}</div>
                        <div class="text-xs text-gray-500">s/d {{ $item->tanggal_kembali_rencana->format('d/m/Y') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($item->status == 'pending')
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Pending
                            </span>
                        @elseif($item->status == 'approved')
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                Dipinjam
                            </span>
                        @elseif($item->status == 'rejected')
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Ditolak
                            </span>
                        @else
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Dikembalikan
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-2">
                            {{-- Admin & Petugas: Approve/Reject --}}
                            @if($item->status == 'pending')
                                <form action="{{ route('peminjaman.approve', $item->peminjaman_id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-900" title="Setujui">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                </form>
                                <form action="{{ route('peminjaman.reject', $item->peminjaman_id) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Yakin tolak peminjaman ini?')">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Tolak">
                                        <i class="fas fa-times-circle"></i>
                                    </button>
                                </form>
                            @endif

                             {{-- TOMBOL PRINT (Admin only) --}}
                            @if(auth()->user()->level == 'admin')
                                <a href="{{ route('laporan.peminjaman', $item->peminjaman_id) }}" 
                                target="_blank"
                                class="text-purple-600 hover:text-purple-900" 
                                title="Cetak Laporan">
                                    <i class="fas fa-print"></i>
                                </a>
                            @endif

                            {{-- Admin: Delete --}}
                            @if(auth()->user()->level == 'admin')
                                <form action="{{ route('peminjaman.destroy', $item->peminjaman_id) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Yakin hapus peminjaman ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl text-gray-300 mb-3 block"></i>
                        Belum ada data peminjaman.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    @if($peminjaman->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $peminjaman->links() }}
        </div>
    @endif
</div>


{{-- MODAL AJUKAN PEMINJAMAN (Admin/Petugas) --}}
<div id="modalTambah" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900">Ajukan Peminjaman</h3>
            <button onclick="closeModal('tambah')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="{{ route('peminjaman.store') }}" method="POST" class="space-y-4">
            @csrf

            {{-- Peminjam --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Peminjam <span class="text-red-500">*</span>
                </label>
                <select name="user_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    <option value="">-- Pilih Peminjam --</option>
                    @foreach($userList as $u)
                        <option value="{{ $u->user_id }}">{{ $u->username }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Alat --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Alat <span class="text-red-500">*</span>
                </label>
                <select name="alat_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    <option value="">-- Pilih Alat --</option>
                    @foreach($alatList as $alat)
                        <option value="{{ $alat->alat_id }}" 
                            {{ isset($selectedAlatId) && $selectedAlatId == $alat->alat_id ? 'selected' : '' }}>
                            {{ $alat->nama_alat }} - {{ $alat->kode_alat }}
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">{{ $alatList->count() }} unit tersedia</p>
            </div>

            {{-- Target Kembali --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Target Pengembalian <span class="text-red-500">*</span>
                </label>
                <input type="date" name="tanggal_kembali_rencana" required
                    min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
            </div>

            {{-- Catatan --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                <textarea name="catatan" rows="2"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm resize-none"
                    placeholder="Keperluan peminjaman (opsional)"></textarea>
            </div>

            {{-- Tombol --}}
            <div class="flex space-x-2 pt-2">
                <button type="submit"
                    class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-lg transition text-sm">
                    <i class="fas fa-paper-plane mr-1"></i> Ajukan
                </button>
                <button type="button" onclick="closeModal('tambah')"
                    class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 rounded-lg transition text-sm">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

@endif


<script>
    // Modal functions
    function openModal(type) {
        document.getElementById('modal' + (type === 'tambah' ? 'Tambah' : 'Edit')).classList.remove('hidden');
    }
    function closeModal(type) {
        document.getElementById('modal' + (type === 'tambah' ? 'Tambah' : 'Edit')).classList.add('hidden');
    }

    // Tutup modal klik di luar
    window.onclick = function(event) {
        if (event.target == document.getElementById('modalTambah')) closeModal('tambah');
    }

    // Buka modal tambah kalau ada validation error
    @if($errors->any())
        @if(auth()->user()->level != 'peminjam')
            openModal('tambah');
        @endif
    @endif

    // ========================================
    // AUTO-HIGHLIGHT DROPDOWN ALAT YANG DIPILIH
    // ========================================
    document.addEventListener('DOMContentLoaded', function() {
        const alatDropdown = document.querySelector('select[name="alat_id"]');
        const selectedValue = alatDropdown?.value;
        
        // Kalau ada alat yang udah ke-select (dari URL parameter)
        if (selectedValue && selectedValue !== '') {
            // Flash highlight hijau biar user tau udah dipilih
            alatDropdown.classList.add('ring-4', 'ring-green-400', 'bg-green-50');
            
            // Scroll dropdown ke view
            alatDropdown.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Hapus highlight setelah 3 detik
            setTimeout(() => {
                alatDropdown.classList.remove('ring-4', 'ring-green-400', 'bg-green-50');
            }, 3000);
            
            // Auto-focus ke field tanggal kembali (biar user langsung isi tanggal)
            const tanggalKembaliInput = document.querySelector('input[name="tanggal_kembali_rencana"]');
            if (tanggalKembaliInput) {
                setTimeout(() => {
                    tanggalKembaliInput.focus();
                }, 800);
            }
        }
    });
</script>

@endsection