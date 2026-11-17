<x-app-layout>
    <!-- HERO SECTION -->
    <div class="relative overflow-hidden bg-gradient-to-br from-indigo-950 via-slate-900 to-purple-950 text-white py-14 sm:py-20 border-b border-indigo-900/50">
        <!-- Background decorative ambient circles -->
        <div class="absolute top-0 right-1/4 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-1/4 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto space-y-4">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-xs font-semibold tracking-wide text-indigo-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span>Tiket & Presensi QR untuk Event Kampus</span>
                </div>

                <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-white leading-tight">
                    Temukan & Ikuti Event Kampus Impianmu
                </h1>

                <p class="text-base sm:text-lg text-indigo-200/90 leading-relaxed">
                    Daftar seminar, workshop, dan kompetisi secara online, lalu tunjukkan tiket QR saat hari acara.
                </p>

                <!-- Search Bar Floating -->
                <div class="pt-4 max-w-2xl mx-auto">
                    <form method="GET" action="{{ route('events.index') }}" class="relative flex flex-col sm:flex-row gap-2 bg-white/10 p-2 rounded-2xl backdrop-blur-md border border-white/20 shadow-2xl">
                        <div class="relative flex-1 flex items-center">
                            <svg class="w-5 h-5 text-indigo-300 absolute left-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input
                                type="text"
                                name="q"
                                value="{{ $search ?? '' }}"
                                placeholder="Cari topik seminar, workshop, atau lokasi..."
                                class="w-full pl-11 pr-4 py-3 bg-white/95 text-gray-900 rounded-xl border-0 text-sm focus:ring-2 focus:ring-indigo-500 placeholder-gray-400 font-medium shadow-inner"
                            />
                        </div>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-indigo-600/30 transition transform active:scale-95">
                            Cari Event
                        </button>
                    </form>

                    @if (!empty($search))
                        <div class="mt-3 flex items-center justify-center gap-2 text-sm text-indigo-200">
                            <span>Menampilkan hasil untuk: <strong>"{{ $search }}"</strong></span>
                            <a href="{{ route('events.index') }}" class="text-xs bg-white/20 hover:bg-white/30 text-white px-2.5 py-1 rounded-full transition">
                                ✕ Reset Filter
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Tiga Poin Fitur -->
                <div class="pt-6 grid grid-cols-3 gap-3 sm:gap-6 text-center text-xs text-indigo-200/80 border-t border-white/10 mt-8">
                    <div class="flex flex-col items-center gap-1">
                        <span class="text-lg sm:text-xl font-bold text-white">Daftar Online</span>
                        <span>Pilih event dan daftar tanpa antre</span>
                    </div>
                    <div class="flex flex-col items-center gap-1">
                        <span class="text-lg sm:text-xl font-bold text-white">Tiket QR</span>
                        <span>Satu tiket digital untuk tiap event</span>
                    </div>
                    <div class="flex flex-col items-center gap-1">
                        <span class="text-lg sm:text-xl font-bold text-white">Presensi Cepat</span>
                        <span>Kehadiran tercatat lewat scan QR</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN EVENT LIST SECTION -->
    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                <div>
                    <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">
                        Agenda Event Kampus
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Pilih event dan amankan kursi pendaftaranmu sebelum kuota habis.
                    </p>
                </div>
                <div class="text-xs text-gray-500 bg-white px-3 py-1.5 rounded-full border border-gray-200 shadow-sm font-medium">
                    Total: <span class="font-bold text-indigo-600">{{ $events->total() }}</span> event tersedia
                </div>
            </div>

            @if ($events->isEmpty())
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center max-w-lg mx-auto">
                    <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Tidak Ada Event yang Sesuai</h3>
                    <p class="text-sm text-gray-500 mb-6">
                        Belum ada event yang cocok dengan kata kunci pencarian Anda. Coba kata kunci lain atau reset filter.
                    </p>
                    <a href="{{ route('events.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm font-bold shadow hover:bg-indigo-700 transition">
                        Tampilkan Semua Event
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($events as $event)
                        @php
                            $remaining = $event->remainingQuota();
                            $quotaPercentage = $event->quota > 0 ? round((($event->quota - $remaining) / $event->quota) * 100) : 0;
                            $isFull = $remaining <= 0;
                            $isPast = $event->start_at->isPast();
                        @endphp
                        <div class="group bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between overflow-hidden">
                            <!-- Header Card dengan Banner Aksen -->
                            <div class="h-2 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>

                            <div class="p-6 flex-1 flex flex-col">
                                <!-- Status Badge & Date Box -->
                                <div class="flex items-start justify-between gap-3 mb-4">
                                    <div class="flex flex-col items-center justify-center bg-indigo-50 border border-indigo-100 rounded-xl px-3 py-1.5 min-w-[54px] text-center shadow-xs">
                                        <span class="text-[10px] font-black uppercase text-indigo-500 tracking-wider">
                                            {{ $event->start_at->format('M') }}
                                        </span>
                                        <span class="text-xl font-extrabold text-indigo-900 leading-none">
                                            {{ $event->start_at->format('d') }}
                                        </span>
                                    </div>

                                    <div class="flex flex-col items-end gap-1">
                                        @if ($isPast)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-gray-100 text-gray-600">
                                                Acara Selesai
                                            </span>
                                        @elseif ($isFull)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-red-100 text-red-700">
                                                Kuota Penuh
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                                                Pendaftaran Buka
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Judul Event -->
                                <h3 class="text-lg font-bold text-gray-900 group-hover:text-indigo-600 transition-colors line-clamp-2 mb-2 leading-snug">
                                    {{ $event->title }}
                                </h3>

                                <!-- Deskripsi Singkat -->
                                <p class="text-xs text-gray-500 line-clamp-3 mb-4 flex-1">
                                    {{ $event->description }}
                                </p>

                                <!-- Meta Info: Waktu & Lokasi -->
                                <div class="space-y-2 py-3 border-t border-gray-100 text-xs text-gray-600">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="truncate font-medium">{{ $event->start_at->format('l, d F Y • H:i') }} WIB</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span class="truncate font-medium">{{ $event->location }}</span>
                                    </div>
                                </div>

                                <!-- Progress Kuota -->
                                <div class="pt-3 border-t border-gray-100">
                                    <div class="flex justify-between items-center text-xs mb-1.5">
                                        <span class="text-gray-500 font-medium">Sisa Kuota:</span>
                                        <span class="font-bold {{ $isFull ? 'text-red-600' : 'text-indigo-600' }}">
                                            {{ $remaining }} / {{ $event->quota }} Kursi
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                        <div class="h-2 rounded-full transition-all duration-500 {{ $isFull ? 'bg-red-500' : ($quotaPercentage > 80 ? 'bg-amber-500' : 'bg-gradient-to-r from-indigo-500 to-purple-600') }}"
                                             style="width: {{ min(100, $quotaPercentage) }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer Card Action -->
                            <div class="p-4 bg-gray-50/80 border-t border-gray-100">
                                <a href="{{ route('events.show', $event) }}" class="inline-flex items-center justify-center w-full gap-2 px-4 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 shadow-md shadow-indigo-500/20 transition-all transform active:scale-98">
                                    <span>Lihat Detail & Registrasi</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $events->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
