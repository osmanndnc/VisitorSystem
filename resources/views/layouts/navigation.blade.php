<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-24">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                  <img src="{{ asset('images/ata_icon.png') }}" alt="Ata Logo" style="width: 200px; height: 60px;" class="mx-auto my-4" />
                    </a>
                </div>

                <!-- Navigation Links -->
                @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'admin')
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link href="{{ route('admin.users.index') }}" :active="request()->routeIs('admin.users.*')">
                            Adminler
                        </x-nav-link>
                        <x-nav-link href="{{ route('security.users.index') }}" :active="request()->routeIs('security.users.*')">
                            Güvenlikler
                        </x-nav-link>
                    </div>
                @endif
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <img class="h-9 w-9 rounded-full shadow hover:scale-105 transition duration-200"
                                src="{{ asset('images/profile.gif') }}" alt="Kullanıcı Profili" />
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <!-- Profil -->
                        <x-dropdown-link href="{{ route('profile.edit') }}">
                            Profil
                        </x-dropdown-link>

                        <!-- Çıkış -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                Çıkış Yap
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'admin')
                <x-responsive-nav-link href="{{ route('admin.users.index') }}" :active="request()->routeIs('admin.users.*')">
                    Adminler
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('security.users.index') }}" :active="request()->routeIs('security.users.*')">
                    Güvenlikler
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
            @auth
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            @endauth
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
