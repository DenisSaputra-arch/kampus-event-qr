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
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 sm:p-8">
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                            {{ ucfirst($event->status) }}
                        </span>
                        <div class="text-sm text-gray-500">
                            Kuota Maksimal: <span class="font-bold text-gray-700">{{ $event->quota }} Peserta</span>
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

                    <div class="pt-6 border-t border-gray-100 flex items-center justify-between">
                        @auth
                            <div class="text-sm text-gray-500">
                                Fitur pendaftaran tiket QR akan tersedia di Tahap 3.
                            </div>
                        @else
                            <div class="text-sm text-gray-500">
                                Ingin mendaftar? Silakan <a href="{{ route('login') }}" class="text-indigo-600 font-semibold underline">Login</a> atau <a href="{{ route('register') }}" class="text-indigo-600 font-semibold underline">Daftar Akun</a> terlebih dahulu.
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
