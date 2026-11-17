<div>
    <!-- Top Utility Bar ala ASU -->
    <div class="bg-charcoal-900 text-gray-300 text-xs py-1.5 px-4 sm:px-8 border-b border-neutral-800 hidden sm:flex justify-between items-center">
        <div class="flex items-center gap-4">
            <span class="font-medium tracking-wide">Portal Resmi Event & Presensi QR Mahasiswa</span>
        </div>
        <div class="flex items-center gap-4 text-[11px] font-medium">
            <a href="{{ route('events.index') }}" class="hover:text-gold-400 transition">Agenda Kampus</a>
            <span class="text-neutral-600">|</span>
            @auth
                <a href="{{ route('registrations.index') }}" class="hover:text-gold-400 transition">Tiket Saya</a>
                <span class="text-neutral-600">|</span>
                <span class="text-gray-400">Login: <strong class="text-white">{{ Auth::user()->name }}</strong> ({{ strtoupper(Auth::user()->role) }})</span>
            @else
                <a href="{{ route('login') }}" class="hover:text-gold-400 transition">Login Peserta / Admin</a>
            @endauth
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <nav x-data="{ open: false }" class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center gap-8">
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                        <a href="{{ route('events.index') }}" class="hover:opacity-95 transition">
                            <x-application-logo />
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden space-x-1 sm:-my-px sm:flex h-full items-center">
                        <x-nav-link :href="route('events.index')" :active="request()->routeIs('events.index')">
                            {{ __('Daftar Event') }}
                        </x-nav-link>

                        @auth
                            <x-nav-link :href="route('registrations.index')" :active="request()->routeIs('registrations.*')">
                                {{ __('Tiket Saya') }}
                            </x-nav-link>

                            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                                {{ __('Dashboard') }}
                            </x-nav-link>

                            @if (Auth::user()->isAdmin())
                                <div class="h-6 w-px bg-gray-200 self-center mx-2"></div>
                                
                                <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                                    <span class="text-maroon-700 font-bold">{{ __('Admin') }}</span>
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

                <!-- Settings Dropdown / Guest Auth Buttons -->
                <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                    @auth
                        <x-dropdown align="right" width="56">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center gap-2.5 px-3 py-1.5 border border-gray-200 rounded-lg text-sm font-medium text-charcoal-900 bg-white hover:bg-gray-50 focus:outline-none transition">
                                    <div class="w-7 h-7 rounded-md bg-maroon-700 text-gold-500 flex items-center justify-center text-xs font-black uppercase">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                    <div class="text-left">
                                        <div class="text-xs font-bold text-charcoal-900 leading-tight max-w-[120px] truncate">{{ Auth::user()->name }}</div>
                                        <div class="text-[10px] uppercase font-bold tracking-wider text-maroon-700 leading-none">
                                            {{ Auth::user()->role }}
                                        </div>
                                    </div>
                                    <svg class="fill-current h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                                    <p class="text-xs text-gray-500">Akun Terdaftar:</p>
                                    <p class="text-sm font-bold text-charcoal-900 truncate">{{ Auth::user()->email }}</p>
                                    <span class="mt-1 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold tracking-wider uppercase {{ Auth::user()->isAdmin() ? 'bg-maroon-100 text-maroon-800' : 'bg-gray-200 text-charcoal-900' }}">
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
                                            class="text-red-700 hover:bg-red-50"
                                            onclick="event.preventDefault(); this.closest('form').submit();">
                                        {{ __('Keluar (Log Out)') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>

                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" title="Keluar dari akun" class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-gray-200 rounded-lg text-xs font-bold text-gray-700 hover:text-maroon-700 hover:border-maroon-700 transition">
                                Keluar
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-bold text-charcoal-900 hover:text-maroon-700 transition px-3 py-2">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-white bg-maroon-700 hover:bg-maroon-800 px-4 py-2 rounded-lg shadow-xs transition">
                            Daftar Sekarang
                        </a>
                    @endauth
                </div>

                <!-- Hamburger Button (Mobile) -->
                <div class="-me-2 flex items-center sm:hidden">
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-lg text-gray-600 hover:text-maroon-700 hover:bg-gray-100 focus:outline-none transition">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Responsive Navigation Menu (Mobile) -->
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
                    <div class="pt-2 pb-1 border-t border-gray-100 text-xs font-bold text-maroon-700 uppercase tracking-wider">
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
                    <div class="font-bold text-base text-charcoal-900">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-xs text-gray-500">{{ Auth::user()->email }} ({{ strtoupper(Auth::user()->role) }})</div>
                    <div class="mt-3 space-y-1">
                        <x-responsive-nav-link :href="route('profile.edit')">
                            {{ __('Pengaturan Akun') }}
                        </x-responsive-nav-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-responsive-nav-link :href="route('logout')"
                                    class="text-red-700"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Keluar (Log Out)') }}
                            </x-responsive-nav-link>
                        </form>
                    </div>
                </div>
            @else
                <div class="pt-4 space-y-2">
                    <a href="{{ route('login') }}" class="block w-full text-center py-2 px-4 rounded-lg border border-gray-300 font-bold text-charcoal-900">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="block w-full text-center py-2 px-4 rounded-lg bg-maroon-700 text-white font-bold shadow-xs">
                        Daftar Akun Baru
                    </a>
                </div>
            @endauth
        </div>
    </nav>
</div>