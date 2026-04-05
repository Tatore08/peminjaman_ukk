@extends('layouts.app')

@section('title', 'Edit Kategori')

@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex items-center space-x-3">
        <a href="{{ route('kategori.index') }}" class="text-gray-400 hover:text-gray-600 transition">
            <i class="fas fa-arrow-left text-lg"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Edit Kategori</h2>
            <p class="text-sm text-gray-500 mt-1">Edit kategori: <strong>{{ $kategori->nama_kategori }}</strong></p>
        </div>
    </div>

    {{-- FORM --}}
    <div class="bg-white rounded-xl shadow-sm p-6 max-w-lg">
        <form action="{{ route('kategori.update', $kategori->kategori_id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Nama Kategori --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Kategori <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nama_kategori"
                    value="{{ old('nama_kategori', $kategori->nama_kategori) }}"
                    placeholder="Contoh: Elektronik"
                    class="w-full px-4 py-2 border {{ $errors->has('nama_kategori') ? 'border-red-400' : 'border-gray-300' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                @error('nama_kategori')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Deskripsi
                </label>
                <textarea name="deskripsi" rows="3"
                    placeholder="Deskripsi kategori (opsional)"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm resize-none">{{ old('deskripsi', $kategori->deskripsi) }}</textarea>
            </div>

            {{-- Tombol --}}
            <div class="flex items-center space-x-3 pt-2">
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-medium transition">
                    <i class="fas fa-save mr-2"></i> Update
                </button>
                <a href="{{ route('kategori.index') }}"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-6 py-2 rounded-lg text-sm font-medium transition">
                    Batal
                </a>
            </div>
        </form>
    </div>

</div>
@endsection