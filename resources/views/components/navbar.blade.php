<div class="bg-white shadow px-6 py-4 flex justify-between items-center">
    <!-- Kiri: Logo -->
    <div class="hidden lg:block"></div>
    <div class="flex items-center space-x-4 block lg:hidden">
        <a href="{{ route('welcome') }}">
            <img src="{{ asset('images/assets/logo.svg') }}" alt="Logo" class="h-8 lg:h-10 w-auto">
        </a>
    </div>

    <!-- Kanan: Notifikasi + Hamburger (mobile) & User Dropdown (desktop) -->
    <div class="flex items-center space-x-4">
        <!-- Notifikasi -->
        <div class="relative">
            <div id="notification-bell" class="relative cursor-pointer">
                <span class="material-icons text-gray-600">notifications</span>
                <span id="notif-indicator" class="absolute top-0 right-0 w-3 h-3 bg-red-500 rounded-full hidden"></span>
            </div>

            <!-- Panel Notifikasi -->
            <div id="notification-panel"
                class="absolute right-0 mt-2 w-64 bg-white border border-gray-200 rounded-lg shadow-lg z-50 hidden">
                <div class="p-4 text-sm text-gray-700">Notifikasi</div>
                <div class="max-h-48 overflow-y-auto divide-y divide-gray-200">
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Kamu punya jadwal booking baru
                    </a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Pembayaran kamu telah dikonfirmasi
                    </a>
                </div>
            </div>
        </div>

        <!-- Hamburger Menu (mobile only) -->
        <div class="lg:hidden">
            <button @click="sidebarOpen = true" class="focus:outline-none">
                <span class="material-icons text-gray-700">menu</span>
            </button>
        </div>

        <!-- User Dropdown (desktop only) -->
        <div class="hidden lg:block relative">
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                        <div class="flex items-center gap-2">
                            <div class="truncate w-[69px] h-[29px] leading-[29px] text-sm">
                                {{ Auth::user()->name }}
                            </div>
                            <button
                                class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                <img class="h-8 w-8 rounded-full object-cover"
                                    src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                            </button>
                        </div>
                    @else
                        <span class="inline-flex rounded-md">
                            <button type="button"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                {{ Auth::user()->name }}
                                <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                        </span>
                    @endif
                </x-slot>

                <x-slot name="content">
                    <div class="block px-4 py-2 text-xs text-gray-400">
                        {{ __('Manage Account') }}
                    </div>

                    <x-dropdown-link href="{{ route('profile.show') }}">
                        {{ __('Profile') }}
                    </x-dropdown-link>

                    @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                        <x-dropdown-link href="{{ route('api-tokens.index') }}">
                            {{ __('API Tokens') }}
                        </x-dropdown-link>
                    @endif

                    <div class="border-t border-gray-200"></div>

                    <form method="POST" action="{{ route('logout') }}" x-data>
                        @csrf
                        <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>
    </div>
</div>