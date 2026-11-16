<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Event Kampus') }}
            </h2>

            <!-- Form Pencarian Aman (GET dengan parameter binding di controller) -->
            <form method="GET" action="{{ route('events.index') }}" class="flex items-center gap-2 w-full sm:w-auto">
                <input
                    type="text"
                    name="q"
                    value="{{ $search ?? '' }}"
                    placeholder="Cari judul, lokasi, topik..."
                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm w-full sm:w-64"
                />
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Cari
                </button>
                @if (!empty($search))
                    <a href="{{ route('events.index') }}" class="text-xs text-gray-500 hover:text-gray-700 underline">Reset</a>
                @endif
            </form>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (!empty($search))
                <div class="mb-4 text-sm text-gray-600">
                    Hasil pencarian untuk: <span class="font-bold text-gray-800">"{{ $search }}"</span>
                </div>
            @endif

            @if ($events->isEmpty())
                <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Belum ada event yang tersedia atau sesuai dengan kata kunci pencarian.
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($events as $event)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden flex flex-col justify-between hover:shadow-md transition">
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ ucfirst($event->status) }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        Kuota: {{ $event->quota }}
                                    </span>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 mb-2">
                                    {{ $event->title }}
                                </h3>
                                <p class="text-sm text-gray-600 line-clamp-3 mb-4">
                                    {{ Str::limit($event->description, 120) }}
                                </p>
                                <div class="space-y-1 text-xs text-gray-500">
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        {{ $event->start_at->format('d M Y, H:i') }} WIB
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        {{ $event->location }}
                                    </div>
                                </div>
                            </div>
                            <div class="p-6 pt-0 border-t border-gray-100 bg-gray-50">
                                <a href="{{ route('events.show', $event) }}" class="mt-4 block text-center w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md text-sm transition">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $events->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
