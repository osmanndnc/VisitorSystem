<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-md">
    <!-- Navigation Container -->
    <div class="w-full px-6 sm:px-8 lg:px-16 max-w-full">
        <div class="flex justify-between items-center h-20">
            <!-- Logo -->
            <div class="flex items-center gap-14">
                <!-- Logo -->
                <a href="{{ route('dashboard') }}">
                    <img src="{{ asset('images/ata_icon.png') }}" alt="Ata Logo" class="h-16 w-auto hover:scale-105 transition duration-300">
                </a>

                <!-- Menü Bağlantıları -->
                @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'admin')
                    <div class="hidden md:flex gap-8 items-center">
                        <!-- Ziyaretçi Listesi Butonu -->
                        <x-nav-link href="{{ route('admin.index') }}" :active="request()->routeIs('admin.visitor.*')" class="text-[1.1rem] font-semibold text-blue-900 backdrop-blur-md bg-white/60 px-4 py-2 rounded-xl hover:text-indigo-700 transition duration-200">
                            Ziyaretçi Listesi
                        </x-nav-link>
                        
                        <!-- Kullanıcılar Dropdown -->
                        <div class="relative group">
                            <button class="text-[1.1rem] font-semibold text-gray-700 backdrop-blur-md bg-white/60 px-4 py-2 rounded-xl hover:text-indigo-700 transition duration-200 flex items-center gap-2">
                                Kullanıcılar
                                <svg class="w-4 h-4 transition-transform duration-200 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div class="absolute left-0 mt-2 w-48 bg-white/90 backdrop-blur-md border border-gray-200 rounded-xl shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                <div class="py-2">
                                    <a href="{{ route('admin.users.index') }}" 
                                       class="block px-4 py-3 text-base text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors duration-150 rounded-lg mx-2 flex items-center gap-3">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        Adminler
                                    </a>
                                    <a href="{{ route('security.users.index') }}" 
                                       class="block px-4 py-3 text-base text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors duration-150 rounded-lg mx-2 flex items-center gap-3">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                        Güvenlikler
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Özel Profil Dropdown -->
            <!-- Modern ve Tıklanabilir Profil Dropdown -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center justify-center w-11 h-11 rounded-full border-2 border-indigo-500 shadow-lg hover:scale-105 transition duration-300 focus:outline-none">
                    <img class="w-full h-full object-cover rounded-full" src="{{ asset('images/profile.gif') }}" alt="Profil">
                </button>

                <!-- Menü Kutusu -->
                <div
                    x-show="open"
                    @click.away="open = false"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-56 rounded-xl shadow-2xl bg-white/80 backdrop-blur-md border border-gray-200 z-50 p-2"
                    style="display: none;"
                >
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-3 text-sm font-medium text-gray-800 hover:bg-indigo-100 hover:text-indigo-700 rounded-xl transition">
                        <i class="bi bi-person-circle text-lg"></i> Profil
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left flex items-center gap-2 px-4 py-3 text-sm font-medium text-red-600 hover:bg-red-100 rounded-xl transition">
                            <i class="bi bi-box-arrow-right text-lg"></i> Çıkış Yap
                        </button>
                    </form>
                </div>
            </div>

            <!-- Hamburger Menü -->
            <div class="md:hidden flex items-center">
                <button @click="open = ! open" class="text-gray-500 dark:text-gray-300 hover:text-indigo-600 focus:outline-none">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Menü -->
    <div :class="{ 'block': open, 'hidden': !open }" class="md:hidden px-4 pt-3 pb-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
        @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'admin')
            <x-responsive-nav-link href="{{ route('admin.index') }}" :active="request()->routeIs('admin.visitor.*')">
                Ziyaretçi Listesi
            </x-responsive-nav-link>
            
            <!-- Responsive Adminler ve Güvenlikler -->
            <x-responsive-nav-link href="{{ route('admin.users.index') }}" :active="request()->routeIs('admin.users.*')">
                Adminler
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('security.users.index') }}" :active="request()->routeIs('security.users.*')">
                Güvenlikler
            </x-responsive-nav-link>
        @endif

        <div class="mt-3 border-t border-gray-200 dark:border-gray-700 pt-4">
            <div class="px-4">
                <div class="text-gray-800 dark:text-gray-200 font-medium">{{ Auth::user()->name }}</div>
                <div class="text-gray-500 text-sm">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link href="{{ route('profile.edit') }}">
                    Profil
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                        Çıkış Yap
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>