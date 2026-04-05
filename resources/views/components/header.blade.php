<header class="bg-white shadow-sm border-b-4 border-red-500">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
        
        <div class="flex items-center space-x-2">
            <i class="fas fa-wrench text-gray-700 text-xl"></i>
            <h1 class="text-xl font-bold text-gray-800">Sistem Peminjaman Alat</h1>
        </div>

        @auth
        <div class="flex items-center space-x-4">
            
            {{-- ROLE BADGE --}}
            <span class="bg-red-100 text-red-600 px-3 py-1 rounded-lg text-sm font-medium capitalize">
                {{ auth()->user()->level }}
            </span>

            {{-- USERNAME / NAMA --}}
            <span class="text-gray-600 text-sm">
                {{ auth()->user()->name ?? auth()->user()->username }}
            </span>

            {{-- LOGOUT --}}
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit"
                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    Logout
                </button>
            </form>
        </div>
        @endauth

    </div>
</header>
