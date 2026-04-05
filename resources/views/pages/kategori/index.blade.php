@extends('layouts.app')

@section('title', 'Kategori Alat')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Kategori Alat</h2>
        <button onclick="openModal('tambah')" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition">
            <i class="fas fa-plus"></i>
            <span>Tambah Kategori</span>
        </button>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 flex justify-between items-center">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    {{-- Error Message --}}
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 flex justify-between items-center">
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900">
                <i class="fas fa-times"></i>
            </button>
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

    {{-- Card Grid --}}
    @if($kategori->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($kategori as $item)
                <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                    
                    {{-- Icon & Nama --}}
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="bg-blue-100 p-2 rounded-lg">
                            <i class="fas fa-folder text-blue-500"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">{{ $item->nama_kategori }}</h3>
                    </div>

                    {{-- Deskripsi --}}
                    <p class="text-gray-500 text-sm mb-4">
                        {{ $item->deskripsi ?? '-' }}
                    </p>

                    {{-- Jumlah Alat --}}
                    <div class="mb-4">
                        <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-lg">
                            <i class="fas fa-wrench mr-1"></i>
                            {{ $item->alat()->count() }} alat
                        </span>
                    </div>

                    {{-- Aksi --}}
                    <div class="flex space-x-3 text-sm border-t border-gray-100 pt-3">
                        {{-- Tombol Edit - buka modal edit --}}
                        <button onclick="openEditModal({{ $item->kategori_id }}, '{{ addslashes($item->nama_kategori) }}', '{{ addslashes($item->deskripsi) }}')"
                            class="text-blue-600 hover:text-blue-900 font-medium flex items-center space-x-1">
                            <i class="fas fa-edit"></i>
                            <span>Edit</span>
                        </button>

                        {{-- Tombol Hapus --}}
                        <form action="{{ route('kategori.destroy', $item->kategori_id) }}" method="POST"
                            onsubmit="return confirm('Yakin ingin menghapus kategori \'{{ $item->nama_kategori }}\'?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 font-medium flex items-center space-x-1">
                                <i class="fas fa-trash"></i>
                                <span>Hapus</span>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($kategori->hasPages())
            <div class="mt-6">
                {{ $kategori->links() }}
            </div>
        @endif

    @else
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <i class="fas fa-folder-open text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg mb-2">Belum ada kategori</p>
            <p class="text-gray-400 text-sm">Klik tombol "Tambah Kategori" untuk menambahkan kategori baru.</p>
        </div>
    @endif


    {{-- ================================================ --}}
    {{-- MODAL TAMBAH KATEGORI --}}
    {{-- ================================================ --}}
    <div id="modalTambah" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Tambah Kategori</h3>
                <button onclick="closeModal('tambah')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form action="{{ route('kategori.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Kategori <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_kategori"
                        value="{{ old('nama_kategori') }}"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Contoh: Elektronik">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="deskripsi" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Deskripsi kategori (opsional)">{{ old('deskripsi') }}</textarea>
                </div>

                <div class="flex space-x-2">
                    <button type="submit"
                        class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-lg transition">
                        Simpan
                    </button>
                    <button type="button" onclick="closeModal('tambah')"
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 rounded-lg transition">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- ================================================ --}}
    {{-- MODAL EDIT KATEGORI --}}
    {{-- ================================================ --}}
    <div id="modalEdit" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Edit Kategori</h3>
                <button onclick="closeModal('edit')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="formEdit" action="" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Kategori <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="editNamaKategori" name="nama_kategori"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Contoh: Elektronik">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea id="editDeskripsi" name="deskripsi" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Deskripsi kategori (opsional)"></textarea>
                </div>

                <div class="flex space-x-2">
                    <button type="submit"
                        class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-lg transition">
                        Update
                    </button>
                    <button type="button" onclick="closeModal('edit')"
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 rounded-lg transition">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>


    <script>
        // Buka modal tambah atau edit
        function openModal(type) {
            document.getElementById('modal' + (type === 'tambah' ? 'Tambah' : 'Edit')).classList.remove('hidden');
        }

        // Tutup modal
        function closeModal(type) {
            document.getElementById('modal' + (type === 'tambah' ? 'Tambah' : 'Edit')).classList.add('hidden');
        }

        // Buka modal edit dan isi data
        function openEditModal(id, nama, deskripsi) {
            // Set action form ke route update yang benar
            document.getElementById('formEdit').action = '/kategori/' + id;

            // Isi field dengan data yang ada
            document.getElementById('editNamaKategori').value = nama;
            document.getElementById('editDeskripsi').value = deskripsi;

            // Buka modal
            document.getElementById('modalEdit').classList.remove('hidden');
        }

        // Tutup modal kalau klik di luar
        window.onclick = function(event) {
            const modalTambah = document.getElementById('modalTambah');
            const modalEdit = document.getElementById('modalEdit');

            if (event.target == modalTambah) closeModal('tambah');
            if (event.target == modalEdit) closeModal('edit');
        }

        // Buka modal tambah otomatis kalau ada validation error
        @if($errors->any())
            openModal('tambah');
        @endif
    </script>

@endsection