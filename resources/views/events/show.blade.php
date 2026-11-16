<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Event') }}
            </h2>
            <a href="{{ route('events.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                &larr; Kembali ke Daftar Event
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Flash Alert -->
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 sm:p-8">
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                            @if ($event->status === 'published') bg-green-100 text-green-800
                            @elseif ($event->status === 'draft') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($event->status) }}
                        </span>

                        <div class="text-sm">
                            <span class="text-gray-500">Sisa Kuota:</span>
                            <span class="font-bold {{ $event->remainingQuota() > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $event->remainingQuota() }}
                            </span>
                            <span class="text-gray-400">/ {{ $event->quota }} Peserta</span>
                        </div>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mb-4">
                        {{ $event->title }}
                    </h1>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-gray-50 rounded-lg mb-6 text-sm text-gray-700 border border-gray-100">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <div>
                                <div class="font-semibold text-gray-900">Jadwal Pelaksanaan</div>
                                <div>{{ $event->start_at->format('d F Y, H:i') }} WIB</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <div>
                                <div class="font-semibold text-gray-900">Lokasi Acara</div>
                                <div>{{ $event->location }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="prose max-w-none text-gray-700 leading-relaxed mb-8 whitespace-pre-line">
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Deskripsi Acara</h3>
                        {{ $event->description }}
                    </div>

                    <!-- Bagian Pendaftaran Tiket QR -->
                    <div class="pt-6 border-t border-gray-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        @auth
                            @php
                                $userRegistration = Auth::user()->registrations()
                                    ->where('event_id', $event->id)
                                    ->where('status', 'registered')
                                    ->first();
                            @endphp

                            @if ($userRegistration)
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                        ✓ Anda Sudah Terdaftar
                                    </span>
                                    <a href="{{ route('registrations.show', $userRegistration) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs uppercase tracking-widest rounded-md shadow transition">
                                        Lihat Tiket QR Saya &rarr;
                                    </a>
                                </div>
                            @elseif ($event->isFull())
                                <div class="text-sm font-semibold text-red-600">
                                    Pendaftaran ditutup: Kuota peserta telah penuh.
                                </div>
                            @elseif ($event->start_at->isPast())
                                <div class="text-sm font-semibold text-gray-500">
                                    Pendaftaran ditutup: Waktu acara telah berlalu.
                                </div>
                            @else
                                <form method="POST" action="{{ route('events.register', $event) }}">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-bold text-sm uppercase tracking-wider rounded-md shadow-md transition">
                                        Daftar Sekarang (Gratis Tiket QR)
                                    </button>
                                </form>
                            @endif
                        @else
                            <div class="text-sm text-gray-600">
                                Ingin mendaftar? Silakan
                                <a href="{{ route('login') }}" class="text-indigo-600 font-semibold underline">Login</a>
                                atau
                                <a href="{{ route('register') }}" class="text-indigo-600 font-semibold underline">Daftar Akun</a>
                                untuk mendapatkan tiket QR.
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
