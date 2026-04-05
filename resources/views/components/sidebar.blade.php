<aside class="w-full lg:w-64 bg-white min-h-screen shadow-sm p-4">
    <nav class="space-y-2">
        
        <!-- Dashboard - SEMUA ROLE -->
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-4 py-2 sm:py-3 text-sm sm:text-base rounded-lg transition {{ request()->routeIs('dashboard') ? 'bg-gray-100 text-gray-800 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
            <i class="fas fa-chart-bar {{ request()->routeIs('dashboard') ? 'text-blue-500' : 'text-gray-400' }}"></i>
            <span>Dashboard</span>
        </a>

        @if(auth()->user()->level == 'admin')
            <!-- Users - ADMIN ONLY -->
            <a href="{{ route('users.index') }}" class="flex items-center space-x-3 px-4 py-2 sm:py-3 text-sm sm:text-base rounded-lg transition {{ request()->routeIs('users.*') ? 'bg-gray-100 text-gray-800 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fas fa-users {{ request()->routeIs('users.*') ? 'text-blue-500' : 'text-gray-400' }}"></i>
                <span>Users</span>
            </a>

            <!-- Kategori - ADMIN ONLY -->
            <a href="{{ route('kategori.index') }}" class="flex items-center space-x-3 px-4 py-2 sm:py-3 text-sm sm:text-base rounded-lg transition {{ request()->routeIs('kategori.*') ? 'bg-gray-100 text-gray-800 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fas fa-folder {{ request()->routeIs('kategori.*') ? 'text-blue-500' : 'text-gray-400' }}"></i>
                <span>Kategori</span>
            </a>
        @endif

        @if(auth()->user()->level == 'admin' || auth()->user()->level == 'petugas')
            <!-- Alat - ADMIN & PETUGAS -->
            <a href="{{ route('alat.index') }}" class="flex items-center space-x-3 px-4 py-2 sm:py-3 text-sm sm:text-base rounded-lg transition {{ request()->routeIs('alat.*') ? 'bg-gray-100 text-gray-800 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fas fa-wrench {{ request()->routeIs('alat.*') ? 'text-blue-500' : 'text-gray-400' }}"></i>
                <span>Alat</span>
            </a>

            <!-- Peminjaman - ADMIN & PETUGAS -->
            <a href="{{ route('peminjaman.index') }}" class="flex items-center space-x-3 px-4 py-2 sm:py-3 text-sm sm:text-base rounded-lg transition {{ request()->routeIs('peminjaman.*') ? 'bg-gray-100 text-gray-800 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fas fa-clipboard-list {{ request()->routeIs('peminjaman.*') ? 'text-blue-500' : 'text-gray-400' }}"></i>
                <span>Peminjaman</span>
            </a>

            <!-- Pengembalian - ADMIN & PETUGAS -->
            <a href="{{ route('pengembalian.index') }}" class="flex items-center space-x-3 px-4 py-2 sm:py-3 text-sm sm:text-base rounded-lg transition {{ request()->routeIs('pengembalian.*') ? 'bg-gray-100 text-gray-800 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fas fa-undo {{ request()->routeIs('pengembalian.*') ? 'text-blue-500' : 'text-gray-400' }}"></i>
                <span>Pengembalian</span>
            </a>
        @endif

        @if(auth()->user()->level == 'admin')
            <!-- Log Aktivitas - ADMIN ONLY -->
            <a href="{{ route('log.index') }}" class="flex items-center space-x-3 px-4 py-2 sm:py-3 text-sm sm:text-base rounded-lg transition {{ request()->routeIs('log.*') ? 'bg-gray-100 text-gray-800 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fas fa-book {{ request()->routeIs('log.*') ? 'text-blue-500' : 'text-gray-400' }}"></i>
                <span>Log Aktivitas</span>
            </a>

            <!-- Laporan - ADMIN ONLY -->
            <a href="{{ route('laporan.index') }}" class="flex items-center space-x-3 px-4 py-2 sm:py-3 text-sm sm:text-base rounded-lg transition {{ request()->routeIs('laporan.*') ? 'bg-gray-100 text-gray-800 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fas fa-chart-line {{ request()->routeIs('laporan.*') ? 'text-blue-500' : 'text-gray-400' }}"></i>
                <span>Laporan</span>
            </a>
        @endif

        @if(auth()->user()->level == 'peminjam')
            <!-- Alat - PEMINJAM (view only) -->
            <a href="{{ route('alat.index') }}" class="flex items-center space-x-3 px-4 py-2 sm:py-3 text-sm sm:text-base rounded-lg transition {{ request()->routeIs('alat.*') ? 'bg-gray-100 text-gray-800 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fas fa-wrench {{ request()->routeIs('alat.*') ? 'text-blue-500' : 'text-gray-400' }}"></i>
                <span>Daftar Alat</span>
            </a>

            <!-- Peminjaman - PEMINJAM (riwayat & ajukan) -->
            <a href="{{ route('peminjaman.index') }}" class="flex items-center space-x-3 px-4 py-2 sm:py-3 text-sm sm:text-base rounded-lg transition {{ request()->routeIs('peminjaman.*') ? 'bg-gray-100 text-gray-800 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fas fa-clipboard-list {{ request()->routeIs('peminjaman.*') ? 'text-blue-500' : 'text-gray-400' }}"></i>
                <span>Peminjaman Saya</span>
            </a>

            <!-- Pengembalian - PEMINJAM -->
            <a href="{{ route('pengembalian.index') }}" class="flex items-center space-x-3 px-4 py-2 sm:py-3 text-sm sm:text-base rounded-lg transition {{ request()->routeIs('pengembalian.*') ? 'bg-gray-100 text-gray-800 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                <i class="fas fa-undo {{ request()->routeIs('pengembalian.*') ? 'text-blue-500' : 'text-gray-400' }}"></i>
                <span>Kembalikan Alat</span>
            </a>
        @endif

    </nav>
</aside>