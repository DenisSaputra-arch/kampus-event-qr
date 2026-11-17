<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-black text-2xl text-charcoal-900 leading-tight">
                    Dashboard Mahasiswa
                </h2>
                <p class="text-xs text-gray-500 mt-1 font-medium">
                    Selamat datang, <strong class="text-charcoal-900">{{ Auth::user()->name }}</strong>. Kelola riwayat tiket dan kehadiran event kampus Anda.
                </p>
            </div>
            <a href="{{ route('events.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded bg-maroon-700 hover:bg-maroon-800 text-white font-bold text-xs uppercase tracking-wider shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Jelajahi Agenda Event
            </a>
        </div>
    </x-slot>

    <div class="py-10 bg-[#F5F5F5] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <!-- 2 KARTU STATISTIK PESERTA (EDITORIAL) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="bg-white rounded-md p-6 border-l-4 border-maroon-700 border-y border-r border-gray-200 shadow-2xs flex items-center gap-5">
                    <div class="w-12 h-12 rounded bg-maroon-50 text-maroon-700 flex items-center justify-center shrink-0 border border-maroon-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                    </div>
                    <div>
                        <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Tiket Aktif Saya</div>
                        <div class="text-2xl font-black text-charcoal-900 mt-0.5">{{ $activeTicketsCount }} Tiket</div>
                        <a href="{{ route('registrations.index') }}" class="text-xs font-bold text-maroon-700 hover:underline">Buka dompet tiket &rarr;</a>
                    </div>
                </div>

                <div class="bg-white rounded-md p-6 border-l-4 border-gold-500 border-y border-r border-gray-200 shadow-2xs flex items-center gap-5">
                    <div class="w-12 h-12 rounded bg-gold-400/20 text-charcoal-900 flex items-center justify-center shrink-0 border border-gold-500/40">
                        <svg class="w-6 h-6 text-maroon-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Event Mendatang Terbuka</div>
                        <div class="text-2xl font-black text-charcoal-900 mt-0.5">{{ $availableEventsCount }} Event</div>
                        <a href="{{ route('events.index') }}" class="text-xs font-bold text-maroon-700 hover:underline">Daftar sekarang &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- TIKET AKTIF SAYA -->
            <div class="bg-white rounded-md border border-gray-200 shadow-2xs overflow-hidden">
                <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-black text-charcoal-900 uppercase tracking-wide">E-Tiket Terdaftar</h3>
                        <p class="text-xs text-gray-500 mt-0.5 font-medium">Tunjukkan QR Code pada tiket untuk verifikasi kehadiran di lokasi.</p>
                    </div>
                    <a href="{{ route('registrations.index') }}" class="text-xs font-bold text-maroon-700 hover:underline">Lihat Semua Tiket &rarr;</a>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @forelse ($registrations as $reg)
                            <div class="p-5 rounded border border-gray-200 hover:border-maroon-700 transition bg-white flex flex-col justify-between space-y-4">
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider
                                            @if ($reg->isAttended()) bg-blue-100 text-blue-900 border border-blue-300
                                            @elseif ($reg->status === 'registered') bg-emerald-100 text-emerald-900 border border-emerald-300
                                            @else bg-red-100 text-red-900 border border-red-300 @endif">
                                            @if ($reg->isAttended())
                                                ✓ Hadir
                                            @elseif ($reg->status === 'registered')
                                                Tiket Aktif
                                            @else
                                                Dibatalkan
                                            @endif
                                        </span>
                                        <span class="text-[10px] text-gray-400 font-mono font-bold">#{{ str_pad($reg->id, 5, '0', STR_PAD_LEFT) }}</span>
                                    </div>
                                    <h4 class="font-black text-charcoal-900 text-base leading-snug">{{ $reg->event->title }}</h4>
                                    <div class="text-xs text-charcoal-900 font-semibold flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-maroon-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ $reg->event->start_at->translatedFormat('d M Y, H:i') }} WIB
                                    </div>
                                    <div class="text-xs text-gray-500 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                        {{ $reg->event->location }}
                                    </div>
                                </div>

                                <a href="{{ route('registrations.show', $reg) }}" class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 rounded bg-maroon-700 hover:bg-maroon-800 text-white font-bold text-xs uppercase tracking-wider shadow-xs transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                    Buka E-Tiket & QR Code
                                </a>
                            </div>
                        @empty
                            <div class="col-span-2 text-center py-10 bg-gray-50 rounded border border-dashed border-gray-300">
                                <p class="text-gray-500 text-sm">Anda belum mendaftar di event apapun.</p>
                                <a href="{{ route('events.index') }}" class="mt-3 inline-block font-bold text-xs text-maroon-700 hover:underline">
                                    Lihat Daftar Event yang Buka &rarr;
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>