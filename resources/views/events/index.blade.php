<x-app-layout>
    <!-- HERO SECTION ALA ASU EVENTS -->
    <div class="relative bg-charcoal-900 text-white min-h-[420px] flex items-center overflow-hidden">
        <!-- Foto Suasana Acara Kampus -->
        <img
            src="https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&w=1600&q=80"
            alt="Suasana Acara Kampus"
            class="absolute inset-0 w-full h-full object-cover object-center opacity-45"
        />
        <!-- Gradient Overlay Tegas Editorial -->
        <div class="absolute inset-0 bg-gradient-to-t from-charcoal-900 via-charcoal-900/60 to-transparent"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 w-full">
            <div class="max-w-3xl space-y-4">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-gold-500 text-charcoal-900 text-xs font-black uppercase tracking-wider">
                    Agenda Resmi Universitas
                </div>

                <h1 class="text-4xl sm:text-6xl font-black tracking-tight text-white leading-tight drop-shadow-sm">
                    Kampus Events
                </h1>

                <p class="text-base sm:text-lg text-gray-200 leading-relaxed max-w-2xl">
                    Jelajahi seminar akademik, workshop teknologi, dan kegiatan mahasiswa. Daftarkan diri secara online dan dapatkan tiket QR resmi.
                </p>

                <!-- Search Bar Floating Box (ASU Style) -->
                <div class="pt-4">
                    <div class="bg-white p-5 sm:p-6 rounded-md shadow-xl max-w-2xl border-t-4 border-maroon-700">
                        <label for="event-search" class="block text-xs font-bold uppercase tracking-wider text-charcoal-900 mb-2">
                            Cari Event Berdasarkan Nama atau Topik
                        </label>
                        <form method="GET" action="{{ route('events.index') }}" class="flex flex-col sm:flex-row gap-3">
                            <div class="relative flex-1">
                                <input
                                    id="event-search"
                                    type="text"
                                    name="q"
                                    value="{{ $search ?? '' }}"
                                    placeholder="Contoh: Cyber Security, Workshop, Auditorium..."
                                    class="w-full px-4 py-3 bg-gray-50 text-charcoal-900 border border-gray-300 rounded-md text-sm focus:bg-white focus:ring-2 focus:ring-maroon-700 focus:border-maroon-700 focus:outline-none"
                                />
                            </div>
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center px-7 py-3 bg-maroon-700 hover:bg-maroon-800 text-white font-black text-sm uppercase tracking-wider rounded-md transition shadow-xs shrink-0 active:scale-98"
                            >
                                Cari Event
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN EVENT DISCOVERY SECTION (ASU 2-COLUMN LAYOUT) -->
    <div class="py-12 bg-[#F5F5F5] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header: Gold Highlight Title -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4 mb-8 pb-4 border-b border-gray-300">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-black text-charcoal-900 tracking-tight">
                        <span class="bg-gold-500 text-charcoal-900 px-3 py-1 inline-block">Semua Event</span>
                    </h2>
                    <p class="text-xs sm:text-sm text-gray-600 mt-2 font-medium">
                        Daftar agenda kegiatan terbuka untuk seluruh civitas akademika kampus.
                    </p>
                </div>
                <div class="text-xs text-gray-600 font-bold bg-white px-3 py-1.5 rounded border border-gray-200">
                    Total: <span class="text-maroon-700 font-black">{{ $events->total() }}</span> Event Tersedia
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 items-start">
                <!-- SIDEBAR FILTER (KIRI) -->
                <aside class="lg:col-span-1 space-y-6 bg-white p-6 rounded-md border border-gray-200 shadow-2xs">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                        <h3 class="text-sm font-black uppercase tracking-wider text-charcoal-900">
                            Saring Hasil
                        </h3>
                        @if (!empty($search))
                            <a href="{{ route('events.index') }}" class="text-[11px] font-bold text-maroon-700 hover:underline">
                                Hapus Filter
                            </a>
                        @endif
                    </div>

                    <!-- Filter Kategori Populer -->
                    <div class="space-y-3">
                        <span class="text-xs font-bold text-charcoal-900 uppercase tracking-wider block">
                            Kategori Acara
                        </span>
                        <div class="space-y-1.5 text-xs text-gray-700">
                            @php
                                $categories = [
                                    'Semua' => '',
                                    'Cyber Security' => 'Cyber Security',
                                    'Workshop' => 'Workshop',
                                    'Seminar' => 'Seminar',
                                    'Kuliah Tamu' => 'Kuliah Tamu',
                                ];
                            @endphp
                            @foreach ($categories as $label => $keyword)
                                <a
                                    href="{{ empty($keyword) ? route('events.index') : route('events.index', ['q' => $keyword]) }}"
                                    class="flex items-center justify-between px-2.5 py-1.5 rounded hover:bg-gray-100 transition {{ ($search === $keyword || (empty($keyword) && empty($search))) ? 'bg-maroon-50 text-maroon-700 font-bold' : '' }}"
                                >
                                    <span>{{ $label }}</span>
                                    @if ($search === $keyword || (empty($keyword) && empty($search)))
                                        <span class="text-maroon-700 font-black">✓</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Filter Lokasi Kampus -->
                    <div class="pt-4 border-t border-gray-200 space-y-3">
                        <span class="text-xs font-bold text-charcoal-900 uppercase tracking-wider block">
                            Lokasi Kampus
                        </span>
                        <div class="space-y-1.5 text-xs text-gray-700">
                            @php
                                $locations = ['Auditorium', 'Lab Komputer', 'Gedung Rektorat'];
                            @endphp
                            @foreach ($locations as $loc)
                                <a
                                    href="{{ route('events.index', ['q' => $loc]) }}"
                                    class="flex items-center justify-between px-2.5 py-1.5 rounded hover:bg-gray-100 transition {{ ($search === $loc) ? 'bg-maroon-50 text-maroon-700 font-bold' : '' }}"
                                >
                                    <span>{{ $loc }}</span>
                                    @if ($search === $loc)
                                        <span class="text-maroon-700 font-black">✓</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Informasi Panduan Singkat -->
                    <div class="pt-4 border-t border-gray-200 bg-gray-50 -mx-6 -mb-6 p-6 rounded-b-md text-xs space-y-2">
                        <span class="font-bold text-charcoal-900 block">Tiket Digital QR:</span>
                        <p class="text-gray-600 leading-relaxed text-[11px]">
                            Setelah registrasi berhasil, e-tiket QR Code resmi dapat langsung dilihat di menu <strong>Tiket Saya</strong> dan ditunjukkan ke panitia saat hari acara.
                        </p>
                    </div>
                </aside>

                <!-- GRID LISTING EVENT (KANAN) -->
                <main class="lg:col-span-3 space-y-6">
                    <!-- Status Filter Aktif -->
                    @if (!empty($search))
                        <div class="flex items-center justify-between p-3.5 bg-white border border-gray-200 rounded-md text-xs">
                            <div class="flex items-center gap-2">
                                <span class="text-gray-500">Filter Aktif:</span>
                                <span class="px-2.5 py-1 bg-maroon-50 border border-maroon-200 text-maroon-700 font-bold rounded">
                                    "{{ $search }}"
                                </span>
                            </div>
                            <a href="{{ route('events.index') }}" class="text-maroon-700 hover:underline font-bold">
                                ✕ Reset Pencarian
                            </a>
                        </div>
                    @endif

                    @if ($events->isEmpty())
                        <div class="bg-white rounded-md border border-gray-200 p-12 text-center">
                            <div class="w-14 h-14 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <h3 class="text-base font-bold text-charcoal-900 mb-1">Tidak Ada Event yang Sesuai</h3>
                            <p class="text-xs text-gray-500 max-w-sm mx-auto mb-5">
                                Belum ada event yang cocok dengan kriteria pencarian Anda. Coba kata kunci lain atau tampilkan seluruh agenda.
                            </p>
                            <a href="{{ route('events.index') }}" class="inline-flex items-center px-4 py-2 bg-maroon-700 hover:bg-maroon-800 text-white rounded font-bold text-xs uppercase tracking-wider transition">
                                Tampilkan Semua Event
                            </a>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($events as $event)
                                @php
                                    $remaining = $event->remainingQuota();
                                    $isFull = $remaining <= 0;
                                    $isPast = $event->start_at->isPast();

                                    // Mapping foto kampus berkualitas tinggi berdasarkan index/topik
                                    $eventImages = [
                                        'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?auto=format&fit=crop&w=700&q=80',
                                        'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=700&q=80',
                                        'https://images.unsplash.com/photo-1475721027785-f74eccf877e2?auto=format&fit=crop&w=700&q=80',
                                        'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=700&q=80',
                                        'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=700&q=80',
                                    ];
                                    $cardImage = $eventImages[$event->id % count($eventImages)];
                                @endphp

                                <div class="bg-white rounded-md border border-gray-200 overflow-hidden hover:shadow-md transition flex flex-col justify-between group">
                                    <div>
                                        <!-- Foto Event Banner (ASU Event Card Style) -->
                                        <div class="relative h-44 w-full bg-charcoal-900 overflow-hidden">
                                            <img
                                                src="{{ $cardImage }}"
                                                alt="{{ $event->title }}"
                                                class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                            />
                                            <!-- Status Badge di Pojok Kanan Atas -->
                                            <div class="absolute top-3 right-3">
                                                @if ($isPast)
                                                    <span class="inline-block px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider bg-charcoal-900/90 text-white rounded">
                                                        Selesai
                                                    </span>
                                                @elseif ($isFull)
                                                    <span class="inline-block px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider bg-red-700 text-white rounded">
                                                        Penuh
                                                    </span>
                                                @else
                                                    <span class="inline-block px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider bg-emerald-700 text-white rounded">
                                                        Buka
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Aksen Garis Emas Khas ASU -->
                                        <div class="h-1 bg-gold-500 w-full"></div>

                                        <!-- Isi Card Event -->
                                        <div class="p-5 space-y-3">
                                            <!-- Judul Event -->
                                            <h3 class="text-base font-black text-maroon-700 hover:text-maroon-900 transition leading-snug line-clamp-2">
                                                <a href="{{ route('events.show', $event) }}">
                                                    {{ $event->title }}
                                                </a>
                                            </h3>

                                            <!-- Jadwal & Waktu -->
                                            <div class="flex items-start gap-2 text-xs text-charcoal-900 font-semibold">
                                                <svg class="w-4 h-4 text-maroon-700 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <span>{{ $event->start_at->translatedFormat('l, d M Y • H:i') }} WIB</span>
                                            </div>

                                            <!-- Lokasi -->
                                            <div class="flex items-center gap-2 text-xs text-gray-600">
                                                <svg class="w-4 h-4 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                <span class="truncate">{{ $event->location }}</span>
                                            </div>

                                            <!-- Deskripsi Ringkas -->
                                            <p class="text-xs text-gray-500 line-clamp-2 pt-1 border-t border-gray-100">
                                                {{ $event->description }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Footer Card: Kuota & CTA -->
                                    <div class="p-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between text-xs">
                                        <div>
                                            <span class="text-gray-500 text-[11px] block">Sisa Kuota:</span>
                                            <span class="font-bold {{ $isFull ? 'text-red-700' : 'text-charcoal-900' }}">
                                                {{ $remaining }} / {{ $event->quota }} Kursi
                                            </span>
                                        </div>
                                        <a
                                            href="{{ route('events.show', $event) }}"
                                            class="inline-flex items-center gap-1 text-xs font-bold text-maroon-700 hover:text-maroon-900 uppercase tracking-wider"
                                        >
                                            <span>Detail</span>
                                            <span>&rarr;</span>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-8">
                            {{ $events->links() }}
                        </div>
                    @endif
                </main>
            </div>
        </div>
    </div>
</x-app-layout>