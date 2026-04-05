<header class="bg-white shadow-sm border-b-4 border-red-500">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 sm:py-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-0">
        
        <div class="flex items-center space-x-2">
            <i class="fas fa-wrench text-gray-700 text-lg sm:text-xl"></i>
            <h1 class="text-base sm:text-xl font-bold text-gray-800">Sistem Peminjaman Alat</h1>
        </div>

        @auth
        <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4 w-full sm:w-auto">
            
            {{-- ROLE BADGE --}}
            <span class="bg-red-100 text-red-600 px-3 py-1 rounded-lg text-xs sm:text-sm font-medium capitalize">
                {{ auth()->user()->level }}
            </span>

            {{-- USERNAME / NAMA --}}
            <span class="text-gray-600 text-xs sm:text-sm truncate">
                {{ auth()->user()->name ?? auth()->user()->username }}
            </span>

            {{-- LOGOUT --}}
            <form method="POST" action="{{ route('logout') }}" class="w-full sm:w-auto">
                @csrf
                <button type="submit"
                    class="w-full sm:w-auto bg-red-500 hover:bg-red-600 active:bg-red-700 text-white px-4 py-2 rounded-lg text-xs sm:text-sm font-medium transition">
                    Logout
                </button>
            </form>
        </div>
        @endauth

    </div>
</header>