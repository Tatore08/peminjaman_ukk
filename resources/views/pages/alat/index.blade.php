@extends('layouts.app')

@section('title', 'Daftar Alat')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Daftar Alat</h2>
        @if(auth()->user()->level == 'admin' || auth()->user()->level == 'petugas')
        <button onclick="openModal('tambah')" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition">
            <i class="fas fa-plus"></i>
            <span>Tambah Alat</span>
        </button>
        @endif
    </div>

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

    {{-- Table Grouped --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Alat</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Unit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tersedia</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dipinjam</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rusak</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pending</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($alatGrouped as $group)
                    {{-- Baris Utama (Summary) --}}
                    <tr class="hover:bg-gray-50 transition cursor-pointer" onclick="toggleUnits('{{ md5($group['nama_alat']) }}')">
                        <td class="px-6 py-4 text-sm">
                            <div class="flex items-center">
                                <i class="fas fa-chevron-right mr-2 text-gray-400 transition-transform" id="icon-{{ md5($group['nama_alat']) }}"></i>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $group['nama_alat'] }}</div>
                                    @if($group['deskripsi'])
                                        <div class="text-xs text-gray-500">{{ Str::limit($group['deskripsi'], 50) }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $group['kategori'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">{{ $group['total_unit'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                {{ $group['tersedia'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $group['dipinjam'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                {{ $group['rusak'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                {{ $group['pending'] ?? 0 }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="text-gray-400 text-xs">Klik untuk lihat unit</span>
                        </td>
                    </tr>

                    {{-- Baris Detail Units (Hidden by default) --}}
                    <tr id="units-{{ md5($group['nama_alat']) }}" class="hidden bg-gray-50">
                        <td colspan="7" class="px-6 py-4">
                            <div class="ml-8">
                                <h4 class="text-sm font-semibold text-gray-700 mb-3">Detail Unit:</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($group['units'] as $unit)
                                        <div class="border border-gray-200 rounded-lg p-3 bg-white">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="font-mono text-sm font-semibold text-gray-800">{{ $unit->kode_alat }}</span>
                                                @if($unit->status == 'tersedia')
                                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Tersedia</span>
                                                @elseif($unit->status == 'dipinjam')
                                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">Dipinjam</span>
                                                @elseif($unit->status == 'pending')
                                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">Pending</span>
                                                @else
                                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">Rusak</span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-600 space-y-1">
                                                <div><strong>Kondisi:</strong> {{ ucfirst($unit->kondisi) }}</div>
                                                @if($unit->lokasi)
                                                    <div><strong>Lokasi:</strong> {{ $unit->lokasi }}</div>
                                                @endif
                                            </div>
                                            
                                            {{-- Action Buttons --}}
                                                <div class="mt-3 flex items-center space-x-2">   
                                                    {{-- TOMBOL PINJAM (Peminjam only, kalau tersedia) --}}
                                                    @if($unit->status == 'tersedia' && auth()->user()->level == 'peminjam')
                                                        <a href="{{ route('peminjaman.index', ['alat_id' => $unit->alat_id]) }}" 
                                                        class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs transition text-center">
                                                            <i class="fas fa-hand-holding mr-1"></i> Pinjam
                                                        </a>
                                                    @endif
                                                    {{-- TOMBOL EDIT & HAPUS (Admin/Petugas only) --}}
                                                    @if(auth()->user()->level == 'admin' || auth()->user()->level == 'petugas')
                                                        <button onclick="editUnit({{ $unit->alat_id }}, '{{ $unit->kategori_id }}', '{{ $unit->nama_alat }}', '{{ $unit->kode_alat }}', '{{ $unit->kondisi }}', '{{ $unit->lokasi }}', '{{ $unit->status }}', {{ $unit->harga_beli ?? 0 }})" 
                                                            class="text-blue-600 hover:text-blue-900 text-xs" title="Edit">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </button>
                                                        
                                                        @if(auth()->user()->level == 'admin')
                                                            <form action="{{ route('alat.destroy', $unit->alat_id) }}" method="POST" class="inline"
                                                                onsubmit="return confirm('Yakin hapus unit {{ $unit->kode_alat }}?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="text-red-600 hover:text-red-900 text-xs" title="Hapus">
                                                                    <i class="fas fa-trash"></i> Hapus
                                                                </button>
                                                            </form>
                                                        @endif
                                                    @endif
                                                </div>
                                           
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-box-open text-4xl text-gray-300 mb-3 block"></i>
                            Belum ada data alat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>


    {{-- ================================================ --}}
    {{-- MODAL TAMBAH ALAT --}}
    {{-- ================================================ --}}
    <div id="modalTambah" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Tambah Alat</h3>
                <button onclick="closeModal('tambah')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form action="{{ route('alat.store') }}" method="POST" class="space-y-4">
                @csrf

                {{-- Nama Alat --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Alat <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_alat" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                        placeholder="Contoh: Laptop ASUS">
                </div>

                {{-- Kategori --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select name="kategori_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategoriList as $kat)
                            <option value="{{ $kat->kategori_id }}">{{ $kat->nama_kategori }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Kode otomatis: 3 huruf pertama kategori (contoh: ELE-001)</p>
                </div>

                {{-- Jumlah Unit --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Jumlah Unit <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="jumlah" min="1" max="100" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                        placeholder="Berapa unit yang ditambahkan?">
                    <p class="text-xs text-gray-500 mt-1">Setiap unit akan diberi kode unik otomatis</p>
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="deskripsi" rows="2"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm resize-none"
                        placeholder="Deskripsi alat (opsional)"></textarea>
                </div>

                {{-- Kondisi --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Kondisi <span class="text-red-500">*</span>
                    </label>
                    <select name="kondisi" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        <option value="baik">Baik</option>
                        <option value="rusak">Rusak</option>
                    </select>
                </div>

                {{-- Lokasi --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi</label>
                    <input type="text" name="lokasi"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                        placeholder="Contoh: Lab 1">
                </div>

                {{-- Harga Beli --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Harga Beli <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500 text-sm">Rp</span>
                        <input type="number" name="harga_beli" min="0" step="1000" required
                            class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                            placeholder="0">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Harga beli per unit (untuk kalkulasi denda kerusakan)</p>
                </div>

                {{-- Tombol --}}
                <div class="flex space-x-2 pt-2">
                    <button type="submit"
                        class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-lg transition text-sm">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                    <button type="button" onclick="closeModal('tambah')"
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 rounded-lg transition text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- ================================================ --}}
    {{-- MODAL EDIT UNIT --}}
    {{-- ================================================ --}}
    <div id="modalEdit" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Edit Unit</h3>
                <button onclick="closeModal('edit')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="formEdit" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                {{-- Kode Alat (readonly) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kode Alat</label>
                    <input type="text" id="edit_kode_alat" readonly
                        class="w-full px-3 py-2 border border-gray-200 bg-gray-50 rounded-lg text-sm text-gray-600">
                </div>

                {{-- Nama Alat --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Alat</label>
                    <input type="text" name="nama_alat" id="edit_nama_alat" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>

                {{-- Kategori --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <select name="kategori_id" id="edit_kategori_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        @foreach($kategoriList as $kat)
                            <option value="{{ $kat->kategori_id }}">{{ $kat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Kondisi --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kondisi</label>
                    <select name="kondisi" id="edit_kondisi" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        <option value="baik">Baik</option>
                        <option value="rusak">Rusak</option>
                    </select>
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" id="edit_status" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        <option value="tersedia">Tersedia</option>
                        <option value="dipinjam">Dipinjam</option>
                        <option value="rusak">Rusak</option>
                        <option value="pending">Pending</option> {{-- ← TAMBAH INI --}}
                    </select>
                </div>

                {{-- Lokasi --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi</label>
                    <input type="text" name="lokasi" id="edit_lokasi"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>

                {{-- Harga Beli --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Harga Beli</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500 text-sm">Rp</span>
                        <input type="number" name="harga_beli" id="edit_harga_beli" min="0" step="1000" required
                            class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                </div>

                {{-- Tombol --}}
                <div class="flex space-x-2 pt-2">
                    <button type="submit"
                        class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-lg transition text-sm">
                        <i class="fas fa-save mr-1"></i> Update
                    </button>
                    <button type="button" onclick="closeModal('edit')"
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 rounded-lg transition text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>


    <script>
        // Toggle units visibility
        function toggleUnits(id) {
            const unitsRow = document.getElementById('units-' + id);
            const icon = document.getElementById('icon-' + id);
            
            if (unitsRow.classList.contains('hidden')) {
                unitsRow.classList.remove('hidden');
                icon.style.transform = 'rotate(90deg)';
            } else {
                unitsRow.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }

        // Edit unit
        function editUnit(id, kategori_id, nama_alat, kode_alat, kondisi, lokasi, status,  harga_beli) {
            document.getElementById('formEdit').action = '/alat/' + id;
            document.getElementById('edit_kode_alat').value = kode_alat;
            document.getElementById('edit_nama_alat').value = nama_alat;
            document.getElementById('edit_kategori_id').value = kategori_id;
            document.getElementById('edit_kondisi').value = kondisi;
            document.getElementById('edit_lokasi').value = lokasi || '';
            document.getElementById('edit_status').value = status;
            document.getElementById('edit_harga_beli').value = harga_beli || 0;
            
            openModal('edit');
        }

        function openModal(type) {
            document.getElementById('modal' + (type === 'tambah' ? 'Tambah' : 'Edit')).classList.remove('hidden');
        }
        function closeModal(type) {
            document.getElementById('modal' + (type === 'tambah' ? 'Tambah' : 'Edit')).classList.add('hidden');
        }

        // Tutup modal klik di luar
        window.onclick = function(event) {
            if (event.target == document.getElementById('modalTambah')) closeModal('tambah');
            if (event.target == document.getElementById('modalEdit')) closeModal('edit');
        }

        // Buka modal tambah kalau ada validation error
        @if($errors->any())
            openModal('tambah');
        @endif
    </script>

@endsection