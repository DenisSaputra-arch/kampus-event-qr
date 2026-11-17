<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('events.index') }}" class="hover:opacity-90 transition">
                        <x-application-logo />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-1 lg:space-x-2 sm:-my-px sm:ms-8 sm:flex">
                    <x-nav-link :href="route('events.index')" :active="request()->routeIs('events.index')">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                            {{ __('Daftar Event') }}
                        </span>
                    </x-nav-link>

                    @auth
                        <x-nav-link :href="route('registrations.index')" :active="request()->routeIs('registrations.*')">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                                {{ __('Tiket Saya') }}
                            </span>
                        </x-nav-link>

                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                {{ __('Dashboard') }}
                            </span>
                        </x-nav-link>

                        @if (Auth::user()->isAdmin())
                            <div class="h-6 w-px bg-gray-200 self-center mx-2"></div>
                            
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                                <span class="flex items-center gap-1.5 text-indigo-700 font-semibold">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                    {{ __('Admin') }}
                                </span>
                            </x-nav-link>

                            <x-nav-link :href="route('admin.events.index')" :active="request()->routeIs('admin.events.*')">
                                {{ __('Kelola Event') }}
                            </x-nav-link>

                            <x-nav-link :href="route('admin.scan.index')" :active="request()->routeIs('admin.scan.*')">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                    {{ __('Scan QR') }}
                                </span>
                            </x-nav-link>

                            <x-nav-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.*')">
                                {{ __('Laporan') }}
                            </x-nav-link>

                            <x-nav-link :href="route('admin.activity-logs.index')" :active="request()->routeIs('admin.activity-logs.*')">
                                {{ __('Log') }}
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown (Auth vs Guest) -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-2">
                @auth
                    <x-dropdown align="right" width="56">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-2.5 px-3 py-1.5 border border-gray-200/80 rounded-full text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition shadow-sm">
                                <div class="w-7 h-7 rounded-full bg-indigo-600 flex items-center justify-center text-white text-xs font-bold uppercase">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <div class="text-left">
                                    <div class="text-xs font-bold text-gray-900 leading-tight max-w-[120px] truncate">{{ Auth::user()->name }}</div>
                                    <div class="text-[10px] uppercase font-semibold tracking-wider text-indigo-600 leading-none">
                                        {{ Auth::user()->role }}
                                    </div>
                                </div>
                                <svg class="fill-current h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="px-4 py-3 border-b border-gray-100 bg-gray-50/50">
                                <p class="text-xs text-gray-500">Masuk sebagai</p>
                                <p class="text-sm font-bold text-gray-900 truncate">{{ Auth::user()->email }}</p>
                                <span class="mt-1 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold tracking-wider uppercase {{ Auth::user()->isAdmin() ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                    Role: {{ Auth::user()->role }}
                                </span>
                            </div>

                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Pengaturan Akun') }}
                            </x-dropdown-link>

                            @if (Auth::user()->isAdmin())
                                <x-dropdown-link :href="route('admin.dashboard')">
                                    {{ __('Panel Kontrol Admin') }}
                                </x-dropdown-link>
                            @endif

                            <div class="border-t border-gray-100"></div>

                            <!-- Logout Form -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        class="text-red-600 hover:text-red-700 hover:bg-red-50"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Keluar (Log Out)') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>

                    <!-- Tombol Cepat Logout Langsung -->
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" title="Keluar dari akun" class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-rose-200/80 rounded-full text-xs font-bold text-rose-600 bg-rose-50/50 hover:bg-rose-100 hover:text-rose-700 transition shadow-2xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Keluar
                        </button>
                    </form>
                @else
                    <div class="flex items-center gap-3">
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-700 hover:text-indigo-600 transition px-3 py-2">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded-xl shadow-sm transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                            Daftar Sekarang
                        </a>
                    </div>
                @endauth
            </div>

            <!-- Hamburger Button -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-xl text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-gray-200 bg-white px-4 pt-2 pb-6 space-y-1">
        <x-responsive-nav-link :href="route('events.index')" :active="request()->routeIs('events.index')">
            {{ __('Daftar Event') }}
        </x-responsive-nav-link>

        @auth
            <x-responsive-nav-link :href="route('registrations.index')" :active="request()->routeIs('registrations.*')">
                {{ __('Tiket Saya') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @if (Auth::user()->isAdmin())
                <div class="pt-2 pb-1 border-t border-gray-100 text-xs font-bold text-gray-400 uppercase tracking-wider">
                    Menu Administrator
                </div>
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    {{ __('Admin Panel') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.events.index')" :active="request()->routeIs('admin.events.*')">
                    {{ __('Kelola Event') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.scan.index')" :active="request()->routeIs('admin.scan.*')">
                    {{ __('Scan QR Kehadiran') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.*')">
                    {{ __('Laporan Presensi') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.activity-logs.index')" :active="request()->routeIs('admin.activity-logs.*')">
                    {{ __('Log Aktivitas') }}
                </x-responsive-nav-link>
            @endif

            <div class="pt-4 pb-2 border-t border-gray-200">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-xs text-gray-500">{{ Auth::user()->email }} ({{ Auth::user()->role }})</div>
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Pengaturan Akun') }}
                    </x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                class="text-red-600"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Keluar (Log Out)') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @else
            <div class="pt-4 space-y-2">
                <a href="{{ route('login') }}" class="block w-full text-center py-2 px-4 rounded-xl border border-gray-300 font-semibold text-gray-700">
                    Masuk
                </a>
                <a href="{{ route('register') }}" class="block w-full text-center py-2 px-4 rounded-xl bg-indigo-600 text-white font-semibold shadow">
                    Daftar Akun Baru
                </a>
            </div>
        @endauth
    </div>
</nav>
