<aside
    class="fixed lg:static inset-y-0 left-0 z-40 w-64 bg-white border-r border-gray-200 transform transition-transform duration-200 ease-in-out
           -translate-x-full lg:translate-x-0"
    :class="{ 'translate-x-0': sidebarOpen }" x-cloak>

    <!-- Tombol close di mobile -->
    <div class="flex justify-end lg:hidden p-2">
        <button @click="sidebarOpen = false">
            <span class="material-icons text-gray-600">close</span>
        </button>
    </div>

    <!-- Logo -->
    <div class="flex justify-center items-center pt-2 pb-1 border-b">
        <a href="{{ route('welcome') }}">
            <img src="{{ asset('images/assets/logo.svg') }}" alt="Logo" class="h-12 w-auto sm:h-12 lg:h-14 xl:h-18">
        </a>
    </div>

    <!-- Menu Navigasi -->
    <nav class="mt-4">
        <a href="{{ route('dashboard.index') }}"
            class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('dashboard.index') ? 'bg-gray-200 font-semibold' : '' }}">
            <span class="material-icons">dashboard</span>
            <span class="ml-3">Dashboard</span>
        </a>

        @can('booking-view')
            <a href="{{ route('dashboard.booking.index') }}"
                class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('dashboard.booking.*') ? 'bg-gray-200 font-semibold' : '' }}">
                <span class="material-icons">event</span>
                <span class="ml-3">Booking</span>
            </a>
        @endcan

        @if (auth()->user()->can('barberman-view') || auth()->user()->can('barbermanSchedule-view'))
            <div x-data="{ open: {{ request()->routeIs('dashboard.barberman.*') || request()->routeIs('dashboard.barberman.schedule.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="flex items-center justify-between w-full px-4 py-3 text-gray-700 hover:bg-gray-100 focus:outline-none">
                    <div class="flex items-center">
                        <span class="material-icons">content_cut</span>
                        <span class="ml-3">Barberman</span>
                    </div>
                    <span class="material-icons text-sm" x-show="!open">expand_more</span>
                    <span class="material-icons text-sm" x-show="open">expand_less</span>
                </button>
                <div x-show="open" class="pl-10 space-y-1 bg-gray-50">
                    @can('barberman-view')
                        <a href="{{ route('dashboard.barberman.data.index') }}"
                            class="block py-2 text-sm {{ request()->routeIs('dashboard.barberman.data.index') ? 'text-black font-semibold' : 'text-gray-700' }} hover:text-gray-900">
                            Data Barberman
                        </a>
                    @endcan

                    @can('barbermanSchedule-view')
                        <a href="{{ route('dashboard.barberman.schedule.index') }}"
                            class="block py-2 text-sm {{ request()->routeIs('dashboard.barberman.schedule.index') ? 'text-black font-semibold' : 'text-gray-700' }} hover:text-gray-900">
                            Jadwal Barberman
                        </a>
                    @endcan
                </div>
            </div>
        @endif

        @can('hairstyle-view')
            <a href="{{ route('dashboard.hairstyle.index') }}"
                class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('dashboard.hairstyle.*') ? 'bg-gray-200 font-semibold' : '' }}">
                <span class="material-icons">book</span>
                <span class="ml-3">Hairstyle</span>
            </a>
        @endcan

        @if (auth()->user()->can('gsllery-view') || auth()->user()->can('service-view') || auth()->user()->can('operationalHour-view') || auth()->user()->can('holiday-view'))
            <div x-data="{ open: {{ request()->routeIs('dashboard.gallery.*') || request()->routeIs('dashboard.service.*') || request()->routeIs('dashboard.operationalHour.*') || request()->routeIs('dashboard.holiday.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="flex items-center justify-between w-full px-4 py-3 text-gray-700 hover:bg-gray-100 focus:outline-none">
                    <div class="flex items-center">
                        <span class="material-icons">settings</span>
                        <span class="ml-3">Manajemen Sistem</span>
                    </div>
                    <span class="material-icons text-sm" x-show="!open">expand_more</span>
                    <span class="material-icons text-sm" x-show="open">expand_less</span>
                </button>
                <div x-show="open" class="pl-10 space-y-1 bg-gray-50">
                    @can('gallery-view')
                    <a href="{{ route('dashboard.gallery.index') }}"
                        class="block py-2 text-sm {{ request()->routeIs('dashboard.gallery.index') ? 'text-black font-semibold' : 'text-gray-700' }} hover:text-gray-900">
                            Gallery
                    </a>
                    @endcan

                    @can('service-view')
                    <a href="{{ route('dashboard.service.index') }}"
                        class="block py-2 text-sm {{ request()->routeIs('dashboard.service.index') ? 'text-black font-semibold' : 'text-gray-700' }} hover:text-gray-900">
                            Layanan
                    </a>
                    @endcan

                    @can('operationalHour-view')
                    <a href="{{ route('dashboard.operationalHour.index') }}"
                        class="block py-2 text-sm {{ request()->routeIs('dashboard.operationalHour.index') ? 'text-black font-semibold' : 'text-gray-700' }} hover:text-gray-900">
                            Jam Operasional
                    </a>
                    @endcan

                    @can('holiday-view')
                    <a href="{{ route('dashboard.holiday.index') }}"
                        class="block py-2 text-sm {{ request()->routeIs('dashboard.holiday.index') ? 'text-black font-semibold' : 'text-gray-700' }} hover:text-gray-900">
                            Hari Libur
                    </a>
                    @endcan
                </div>
            </div>
        @endif

        @can('user-view')
            <a href="{{ route('dashboard.user.index') }}"
                class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('dashboard.user.*') ? 'bg-gray-200 font-semibold' : '' }}">
                <span class="material-icons">person</span>
                <span class="ml-3">User</span>
            </a>
        @endcan
    </nav>

    <!-- Profil User di Bawah (Hanya Mobile) -->
    <div class="lg:hidden border-t border-gray-200 p-4 absolute bottom-0 w-full bg-white">
        <div class="flex items-center space-x-4">
            <img class="h-10 w-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}"
                alt="{{ Auth::user()->name }}">
            <div>
                <div class="text-gray-800 font-semibold">{{ Auth::user()->name }}</div>
                <a href="{{ route('profile.show') }}" class="text-blue-500 text-sm hover:underline">Lihat Profil</a>

                <!-- Tombol Logout -->
                <form method="POST" action="{{ route('logout') }}" class="mt-1" x-data>
                    @csrf
                    <button type="submit" class="text-red-500 text-sm hover:underline">Logout</button>
                </form>
            </div>
        </div>
    </div>
</aside>

<!-- Backdrop -->
<div class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden" x-show="sidebarOpen" @click="sidebarOpen = false"
    x-cloak></div>
