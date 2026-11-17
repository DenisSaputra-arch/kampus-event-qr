<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <nav class="flex items-center gap-2 text-xs sm:text-sm text-gray-500 font-medium">
                <a href="{{ route('events.index') }}" class="hover:text-maroon-700 transition">Daftar Event</a>
                <span>/</span>
                <span class="text-charcoal-900 font-bold truncate max-w-xs sm:max-w-md">{{ $event->title }}</span>
            </nav>
            <a href="{{ route('events.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-maroon-700 hover:text-maroon-900 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Agenda
            </a>
        </div>
    </x-slot>

    <div class="py-10 bg-[#F5F5F5] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Flash Notifikasi -->
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-600 text-emerald-900 text-sm rounded-r flex items-center gap-3 shadow-2xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="font-bold">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-red-50 border-l-4 border-red-600 text-red-900 text-sm rounded-r flex items-center gap-3 shadow-2xs">
                    <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="font-bold">{{ session('error') }}</span>
                </div>
            @endif

            @php
                $remaining = $event->remainingQuota();
                $isFull = $remaining <= 0;
                $isPast = $event->start_at->isPast();
                $userRegistration = Auth::check() ? Auth::user()->registrations()->where('event_id', $event->id)->where('status', 'registered')->first() : null;
            @endphp

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                <!-- Kolom Kiri: Banner Foto & Informasi Acara -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-md border border-gray-200 overflow-hidden shadow-2xs">
                        <!-- Banner Foto Event -->
                        <div class="relative h-64 sm:h-80 w-full bg-charcoal-900 overflow-hidden">
                            <img
                                src="{{ $event->getImageUrl() }}"
                                alt="{{ $event->title }}"
                                class="w-full h-full object-cover object-center"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-charcoal-900/80 via-transparent to-transparent"></div>
                            <div class="absolute bottom-4 left-6 right-6">
                                <span class="inline-block px-3 py-1 bg-gold-500 text-charcoal-900 text-xs font-black uppercase tracking-wider mb-2">
                                    {{ $event->status === 'published' ? 'Agenda Kampus Resmi' : ucfirst($event->status) }}
                                </span>
                            </div>
                        </div>

                        <!-- Garis Aksen Emas -->
                        <div class="h-1.5 bg-gold-500 w-full"></div>

                        <div class="p-6 sm:p-8 space-y-6">
                            <!-- Judul Event -->
                            <h1 class="text-2xl sm:text-4xl font-black text-charcoal-900 tracking-tight leading-snug">
                                {{ $event->title }}
                            </h1>

                            <!-- Detail Ringkas (Tanggal & Lokasi) -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 pb-4 border-y border-gray-200">
                                <div class="flex items-start gap-3">
                                    <div class="w-9 h-9 rounded bg-maroon-50 text-maroon-700 flex items-center justify-center shrink-0 border border-maroon-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                    <div>
                                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Waktu Pelaksanaan</span>
                                        <div class="text-sm font-bold text-charcoal-900 mt-0.5">{{ $event->start_at->translatedFormat('l, d F Y') }}</div>
                                        <div class="text-xs text-maroon-700 font-bold">Pukul {{ $event->start_at->format('H:i') }} WIB</div>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3">
                                    <div class="w-9 h-9 rounded bg-maroon-50 text-maroon-700 flex items-center justify-center shrink-0 border border-maroon-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </div>
                                    <div>
                                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Tempat / Ruangan</span>
                                        <div class="text-sm font-bold text-charcoal-900 mt-0.5">{{ $event->location }}</div>
                                        <div class="text-xs text-gray-500">Kampus Terpadu</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Deskripsi Lengkap -->
                            <div class="space-y-3">
                                <h2 class="text-lg font-black text-charcoal-900 uppercase tracking-wide flex items-center gap-2">
                                    <span class="w-1.5 h-4 bg-maroon-700 inline-block"></span>
                                    Deskripsi Acara
                                </h2>
                                <div class="text-charcoal-900 leading-relaxed text-sm sm:text-base whitespace-pre-line font-normal">
                                    {{ $event->description }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Card Registrasi & Status Kuota -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white rounded-md border-t-4 border-maroon-700 border-x border-b border-gray-200 p-6 sm:p-7 shadow-2xs space-y-6 sticky top-24">
                        <div class="text-center pb-5 border-b border-gray-200">
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-500 block">Ketersediaan Kuota</span>
                            <div class="mt-2 flex items-baseline justify-center gap-1.5">
                                <span class="text-4xl font-black {{ $isFull ? 'text-red-700' : 'text-maroon-700' }}">
                                    {{ $remaining }}
                                </span>
                                <span class="text-xs font-bold text-gray-500">/ {{ $event->quota }} Kursi</span>
                            </div>

                            <div class="w-full bg-gray-200 rounded h-2 mt-3 overflow-hidden">
                                @php
                                    $pct = $event->quota > 0 ? round((($event->quota - $remaining) / $event->quota) * 100) : 0;
                                @endphp
                                <div class="h-2 rounded transition-all duration-300 {{ $isFull ? 'bg-red-700' : 'bg-maroon-700' }}" style="width: {{ min(100, $pct) }}%"></div>
                            </div>
                        </div>

                        <!-- CTA BUTTON BERDASARKAN STATUS -->
                        @if ($userRegistration)
                            <!-- Sudah Terdaftar -->
                            <div class="space-y-3">
                                <div class="p-3 bg-emerald-50 border border-emerald-300 rounded text-center text-xs text-emerald-900 font-bold">
                                    ✓ Anda telah terdaftar pada event ini
                                </div>
                                <a href="{{ route('registrations.show', $userRegistration) }}" class="flex items-center justify-center gap-2 w-full py-3.5 px-4 rounded bg-emerald-700 hover:bg-emerald-800 text-white font-black text-xs uppercase tracking-wider shadow-xs transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                    Buka E-Tiket QR Anda
                                </a>
                            </div>
                        @elseif ($isPast)
                            <div class="p-4 bg-gray-100 rounded text-center text-xs text-gray-600 font-bold">
                                Pendaftaran ditutup (Acara telah selesai).
                            </div>
                        @elseif ($isFull)
                            <div class="p-4 bg-red-50 border border-red-200 rounded text-center text-xs text-red-800 font-bold">
                                Kuota pendaftaran telah terpenuhi sepenuhnya.
                            </div>
                        @else
                            @auth
                                <form method="POST" action="{{ route('events.register', $event) }}">
                                    @csrf
                                    <button type="submit" class="w-full py-3.5 px-4 rounded bg-maroon-700 hover:bg-maroon-800 text-white font-black text-xs uppercase tracking-wider shadow-xs transition active:scale-98">
                                        Daftar Event Sekarang
                                    </button>
                                </form>
                            @else
                                <div class="space-y-3">
                                    <a href="{{ route('login') }}" class="block text-center w-full py-3 px-4 rounded bg-maroon-700 hover:bg-maroon-800 text-white font-bold text-xs uppercase tracking-wider shadow-xs transition">
                                        Masuk untuk Mendaftar
                                    </a>
                                    <p class="text-[11px] text-center text-gray-500">
                                        Belum punya akun? <a href="{{ route('register') }}" class="text-maroon-700 font-bold hover:underline">Daftar Akun Baru</a>
                                    </p>
                                </div>
                            @endauth
                        @endif

                        <!-- Informasi Pendaftaran -->
                        <div class="pt-4 border-t border-gray-200 space-y-2 text-xs text-gray-600 font-medium">
                            <div class="flex items-center gap-2">
                                <span class="text-emerald-700 font-bold">✓</span>
                                <span>Pendaftaran instan tanpa biaya</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-emerald-700 font-bold">✓</span>
                                <span>E-tiket QR langsung dapat diunduh/dicetak</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-emerald-700 font-bold">✓</span>
                                <span>Presensi cepat di lokasi dengan scan QR</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>