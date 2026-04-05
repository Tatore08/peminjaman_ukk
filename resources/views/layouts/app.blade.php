<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Sistem Peminjaman Alat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-50">
    
    <!-- Header Component -->
    <x-header :username="session('username')" />

    <div class="flex flex-col lg:flex-row">
        <!-- Sidebar Container - Hidden on Mobile -->
        <div id="sidebar-container" class="hidden lg:block lg:w-64 fixed lg:static left-0 top-16 h-screen lg:h-auto z-40 bg-white lg:bg-transparent overflow-y-auto">
            <x-sidebar />
        </div>

        <!-- Mobile Sidebar Overlay -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-30 lg:hidden"></div>

        <!-- Main Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 w-full">
            @yield('content')
        </main>
    </div>

    <!-- Mobile Menu Button (Floating) -->
    <button id="menu-toggle" class="lg:hidden fixed bottom-6 right-6 bg-blue-500 hover:bg-blue-600 active:bg-blue-700 text-white p-4 rounded-full shadow-lg z-40 transition-all duration-200">
        <i class="fas fa-bars text-xl"></i>
    </button>

    <script>
        const menuToggle = document.getElementById('menu-toggle');
        const sidebarContainer = document.getElementById('sidebar-container');
        const sidebarOverlay = document.getElementById('sidebar-overlay');
        let isOpen = false;

        menuToggle.addEventListener('click', () => {
            isOpen = !isOpen;
            sidebarContainer.classList.toggle('hidden', !isOpen);
            sidebarOverlay.classList.toggle('hidden', !isOpen);
            
            // Toggle icon animation
            const icon = menuToggle.querySelector('i');
            if (isOpen) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });

        sidebarOverlay.addEventListener('click', () => {
            isOpen = false;
            sidebarContainer.classList.add('hidden');
            sidebarOverlay.classList.add('hidden');
            const icon = menuToggle.querySelector('i');
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        });

        // Close sidebar when clicking a link
        const sidebarLinks = sidebarContainer.querySelectorAll('a');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', () => {
                isOpen = false;
                sidebarContainer.classList.add('hidden');
                sidebarOverlay.classList.add('hidden');
                const icon = menuToggle.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            });
        });
    </script>
</body>
</html>