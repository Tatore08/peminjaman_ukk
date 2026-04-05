@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Manajemen User</h2>
        <button onclick="openModal('tambah')" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition">
            <i class="fas fa-plus"></i>
            <span>Tambah User</span>
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

    {{-- Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Terdaftar</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $index => $user)
                    <tr class="hover:bg-gray-50 transition {{ $user->user_id == auth()->id() ? 'bg-blue-50' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $users->firstItem() + $index }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-gray-500"></i>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $user->username }}
                                        @if($user->user_id == auth()->id())
                                            <span class="ml-2 text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">Anda</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->level == 'admin')
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    <i class="fas fa-crown mr-1"></i> Admin
                                </span>
                            @elseif($user->level == 'petugas')
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <i class="fas fa-user-tie mr-1"></i> Petugas
                                </span>
                            @else
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-user mr-1"></i> Peminjam
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $user->created_at->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-3">
                            @if($user->user_id != auth()->id())
                                {{-- Tombol Edit --}}
                                <button onclick="openEditModal(
                                    {{ $user->user_id }},
                                    '{{ addslashes($user->username) }}',
                                    '{{ $user->level }}'
                                )" class="text-blue-600 hover:text-blue-900" title="Edit">
                                    Edit
                                </button>

                                {{-- Tombol Hapus --}}
                                <form action="{{ route('users.destroy', $user->user_id) }}" method="POST"
                                    class="inline"
                                    onsubmit="return confirm('Yakin ingin menghapus user \'{{ $user->username }}\'?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                        Hapus
                                    </button>
                                </form>
                            @else
                                <span class="text-gray-400 text-xs italic">Tidak bisa edit/hapus akun sendiri</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-users text-4xl text-gray-300 mb-2"></i>
                            <p>Belum ada data user lain.</p>
                            <p class="text-sm">Klik tombol "Tambah User" untuk menambahkan.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>


    {{-- ================================================ --}}
    {{-- MODAL TAMBAH USER --}}
    {{-- ================================================ --}}
    <div id="modalTambah" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Tambah User</h3>
                <button onclick="closeModal('tambah')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
                @csrf

                {{-- Username --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Username <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="username" value="{{ old('username') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                        placeholder="Contoh: johndoe">
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password" required minlength="6"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                        placeholder="Minimal 6 karakter">
                </div>

                {{-- Level --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Level <span class="text-red-500">*</span>
                    </label>
                    <select name="level" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        <option value="">-- Pilih Level --</option>
                        <option value="admin">Admin</option>
                        <option value="petugas">Petugas</option>
                        <option value="peminjam">Peminjam</option>
                    </select>
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
    {{-- MODAL EDIT USER --}}
    {{-- ================================================ --}}
    <div id="modalEdit" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Edit User</h3>
                <button onclick="closeModal('edit')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="formEdit" action="" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                {{-- Username --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Username <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="editUsername" name="username" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Password <span class="text-gray-500 text-xs">(Kosongkan jika tidak diubah)</span>
                    </label>
                    <input type="password" id="editPassword" name="password" minlength="6"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                        placeholder="Minimal 6 karakter">
                </div>

                {{-- Level --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Level <span class="text-red-500">*</span>
                    </label>
                    <select id="editLevel" name="level" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        <option value="admin">Admin</option>
                        <option value="petugas">Petugas</option>
                        <option value="peminjam">Peminjam</option>
                    </select>
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
        // Modal Tambah & Edit
        function openModal(type) {
            document.getElementById('modal' + (type === 'tambah' ? 'Tambah' : 'Edit')).classList.remove('hidden');
        }
        function closeModal(type) {
            document.getElementById('modal' + (type === 'tambah' ? 'Tambah' : 'Edit')).classList.add('hidden');
        }

        // Open Edit Modal
        function openEditModal(id, username, level) {
            document.getElementById('formEdit').action = '/users/' + id;
            document.getElementById('editUsername').value = username;
            document.getElementById('editPassword').value = '';
            document.getElementById('editLevel').value = level;
            openModal('edit');
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