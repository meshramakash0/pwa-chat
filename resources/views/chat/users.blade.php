<x-app-layout>
    <style>
        :root {
            --wa-teal-dark: #075e54;
            --wa-teal-light: #128c7e;
            --wa-green: #25d366;
        }

        /* Dropdown menu styles */
        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            min-width: 180px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            z-index: 50;
            overflow: hidden;
            margin-top: 8px;
        }

        .dropdown-menu.show {
            display: block;
            animation: dropdownFadeIn 0.15s ease-out;
        }

        @keyframes dropdownFadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            color: #333;
            text-decoration: none;
            transition: background-color 0.15s;
        }

        .dropdown-item:hover {
            background-color: #f5f5f5;
        }

        .dropdown-item svg {
            margin-right: 12px;
        }

        .dropdown-divider {
            height: 1px;
            background: #e5e5e5;
            margin: 4px 0;
        }
    </style>

    <div class="max-w-4xl mx-auto bg-white min-h-screen sm:min-h-0 sm:h-[calc(100vh-32px)] sm:my-4 sm:rounded-lg sm:shadow-xl overflow-hidden flex flex-col">
        
        <!-- Header -->
        <div class="px-4 py-3 sm:py-4 text-white" style="background-color: var(--wa-teal-dark);">
            <div class="flex items-center justify-end">
                <!-- 3 Dots Menu with Dropdown -->
                <div class="relative">
                    <button id="menuBtn" class="p-2 hover:bg-white/10 rounded-full transition">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                        </svg>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div id="dropdownMenu" class="dropdown-menu">
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="dropdown-item w-full text-left text-red-600">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users List -->
        <div class="divide-y divide-gray-100 flex-1 overflow-y-auto">
            @forelse($users as $user)
                <a href="{{ route('chat.show', $user->id) }}" 
                   class="flex items-center px-4 py-3 hover:bg-gray-50 transition-colors cursor-pointer">
                    
                    <!-- Avatar -->
                    <div class="relative flex-shrink-0">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                            <svg class="w-7 h-7 sm:w-8 sm:h-8 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                        </div>
                        <!-- Online indicator -->
                        <span class="absolute bottom-0 right-0 w-3 h-3 sm:w-3.5 sm:h-3.5 bg-green-500 border-2 border-white rounded-full"></span>
                    </div>
                    
                    <!-- User Info -->
                    <div class="ml-3 sm:ml-4 flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-gray-900 text-base sm:text-lg truncate">
                                {{ $user->name }}
                            </h3>
                            <span class="text-xs text-gray-500 ml-2 flex-shrink-0">
                                {{ now()->format('h:i A') }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between mt-0.5">
                            <p class="text-sm text-gray-500 truncate">
                                <svg class="inline w-4 h-4 text-blue-500 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M18 7l-1.41-1.41-6.34 6.34 1.41 1.41L18 7zm4.24-1.41L11.66 16.17 7.48 12l-1.41 1.41L11.66 19l12-12-1.42-1.41zM.41 13.41L6 19l1.41-1.41L1.83 12 .41 13.41z"/>
                                </svg>
                                Tap to start chatting
                            </p>
                        </div>
                    </div>
                    
                    <!-- Arrow indicator -->
                    <svg class="w-5 h-5 text-gray-400 ml-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            @empty
                <div class="px-4 py-12 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">No conversations yet</h3>
                    <p class="text-gray-500">Start a new chat to begin messaging</p>
                </div>
            @endforelse
        </div>
    </div>

    <script>
        // Dropdown toggle
        const menuBtn = document.getElementById('menuBtn');
        const dropdownMenu = document.getElementById('dropdownMenu');

        menuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!dropdownMenu.contains(e.target) && !menuBtn.contains(e.target)) {
                dropdownMenu.classList.remove('show');
            }
        });

        // Close dropdown on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                dropdownMenu.classList.remove('show');
            }
        });
    </script>
</x-app-layout>
