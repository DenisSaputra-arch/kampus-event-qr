<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <nav class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('events.index') }}" class="hover:text-indigo-600 font-medium transition">Daftar Event</a>
                <span>/</span>
                <span class="text-gray-900 font-semibold truncate max-w-xs sm:max-w-md">{{ $event->title }}</span>
            </nav>
            <a href="{{ route('events.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-10 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Flash Notifikasi -->
            @if (session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-2xl flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-2xl flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            @php
                $remaining = $event->remainingQuota();
                $isFull = $remaining <= 0;
                $isPast = $event->start_at->isPast();
                $userRegistration = Auth::check() ? Auth::user()->registrations()->where('event_id', $event->id)->where('status', 'registered')->first() : null;
            @endphp

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                <!-- Kolom Kiri: Informasi Utama & Deskripsi Event -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-gray-200/80 shadow-sm space-y-6">
                        <!-- Badge Status -->
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                                @if ($event->status === 'published') bg-emerald-100 text-emerald-800
                                @elseif ($event->status === 'draft') bg-amber-100 text-amber-800
                                @else bg-rose-100 text-rose-800 @endif">
                                {{ ucfirst($event->status) }}
                            </span>
                            @if ($isPast)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700">
                                    Waktu Acara Telah Lewat
                                </span>
                            @endif
                        </div>

                        <!-- Judul Event -->
                        <h1 class="text-2xl sm:text-4xl font-extrabold text-gray-900 tracking-tight leading-snug">
                            {{ $event->title }}
                        </h1>

                        <!-- Detail Grid Info Box -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="p-4 rounded-2xl bg-indigo-50/60 border border-indigo-100 flex items-start gap-3.5">
                                <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-indigo-600/30">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-indigo-900 uppercase tracking-wider">Tanggal & Waktu</div>
                                    <div class="text-sm font-semibold text-gray-900 mt-0.5">{{ $event->start_at->format('l, d F Y') }}</div>
                                    <div class="text-xs text-indigo-700 font-medium">Pukul {{ $event->start_at->format('H:i') }} WIB</div>
                                </div>
                            </div>

                            <div class="p-4 rounded-2xl bg-purple-50/60 border border-purple-100 flex items-start gap-3.5">
                                <div class="w-10 h-10 rounded-xl bg-purple-600 text-white flex items-center justify-center shrink-0 shadow-sm shadow-purple-600/30">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-purple-900 uppercase tracking-wider">Lokasi Acara</div>
                                    <div class="text-sm font-semibold text-gray-900 mt-0.5">{{ $event->location }}</div>
                                    <div class="text-xs text-purple-700 font-medium">Kampus Terpadu</div>
                                </div>
                            </div>
                        </div>

                        <!-- Deskripsi Lengkap -->
                        <div class="pt-6 border-t border-gray-100">
                            <h3 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                                Tentang Acara Ini
                            </h3>
                            <div class="text-gray-700 leading-relaxed space-y-4 text-sm sm:text-base whitespace-pre-line">
                                {{ $event->description }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Card Registrasi & Kuota -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200/80 shadow-sm space-y-6 sticky top-24">
                        <div class="text-center pb-4 border-b border-gray-100">
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Status Pendaftaran</span>
                            <div class="mt-2 flex items-baseline justify-center gap-2">
                                <span class="text-4xl font-black {{ $isFull ? 'text-rose-600' : 'text-indigo-600' }}">
                                    {{ $remaining }}
                                </span>
                                <span class="text-sm font-bold text-gray-400">/ {{ $event->quota }} Kursi Tersedia</span>
                            </div>

                            <div class="w-full bg-gray-100 rounded-full h-2.5 mt-3 overflow-hidden">
                                @php
                                    $pct = $event->quota > 0 ? round((($event->quota - $remaining) / $event->quota) * 100) : 0;
                                @endphp
                                <div class="h-2.5 rounded-full {{ $isFull ? 'bg-rose-500' : 'bg-gradient-to-r from-indigo-500 to-purple-600' }}" style="width: {{ min(100, $pct) }}%"></div>
                            </div>
                        </div>

                        <!-- CTA BUTTON BERDASARKAN STATUS -->
                        @if ($userRegistration)
                            <!-- Sudah Terdaftar -->
                            <div class="space-y-3">
                                <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-2xl text-center text-xs text-emerald-800 font-semibold">
                                    ✓ Anda sudah terdaftar aktif pada event ini!
                                </div>
                                <a href="{{ route('registrations.show', $userRegistration) }}" class="flex items-center justify-center gap-2 w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-sm shadow-lg shadow-emerald-600/30 transition transform hover:-translate-y-0.5">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                    Buka E-Tiket QR Anda
                                </a>
                            </div>
                        @elseif ($isPast)
                            <div class="p-4 bg-gray-100 rounded-2xl text-center text-xs text-gray-500 font-semibold">
                                Pendaftaran telah ditutup karena acara telah selesai.
                            </div>
                        @elseif ($isFull)
                            <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-center text-xs text-rose-700 font-semibold">
                                Mohon maaf, kuota peserta telah terpenuhi sepenuhnya.
                            </div>
                        @else
                            @auth
                                <form method="POST" action="{{ route('events.register', $event) }}">
                                    @csrf
                                    <button type="submit" class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-extrabold text-sm shadow-xl shadow-indigo-600/30 transition transform hover:-translate-y-0.5 active:scale-95">
                                        Daftar Event Sekarang
                                    </button>
                                </form>
                            @else
                                <div class="space-y-3">
                                    <a href="{{ route('login') }}" class="block text-center w-full py-3 px-4 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow transition">
                                        Masuk untuk Mendaftar
                                    </a>
                                    <p class="text-[11px] text-center text-gray-400">
                                        Belum punya akun? <a href="{{ route('register') }}" class="text-indigo-600 font-bold hover:underline">Daftar Akun Baru</a>
                                    </p>
                                </div>
                            @endauth
                        @endif

                        <!-- Informasi Pendaftaran -->
                        <div class="pt-4 border-t border-gray-100 space-y-2.5 text-xs text-gray-500">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Kuota diperiksa saat mendaftar</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>E-tiket QR langsung tersedia setelah mendaftar</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Tiket hanya bisa dilihat oleh pemiliknya</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
