<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Event (Admin)') }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('admin.events.edit', $event) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded text-xs font-semibold hover:bg-blue-700">
                    Ubah Event
                </a>
                <a href="{{ route('admin.events.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                    &larr; Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sm:p-8">
                <div class="flex items-center justify-between mb-4">
                    <span class="font-mono text-xs text-gray-400">ID Event: #{{ $event->id }}</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold
                        @if ($event->status === 'published') bg-green-100 text-green-800
                        @elseif ($event->status === 'draft') bg-yellow-100 text-yellow-800
                        @else bg-red-100 text-red-800 @endif">
                        {{ strtoupper($event->status) }}
                    </span>
                </div>

                <h1 class="text-2xl font-bold text-gray-900 mb-4">{{ $event->title }}</h1>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-gray-50 rounded-lg mb-6 text-sm">
                    <div>
                        <div class="text-xs text-gray-500 uppercase font-semibold">Waktu Mulai</div>
                        <div class="text-gray-800 font-medium">{{ $event->start_at->format('d M Y, H:i') }} WIB</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 uppercase font-semibold">Lokasi</div>
                        <div class="text-gray-800 font-medium">{{ $event->location }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 uppercase font-semibold">Kuota Peserta</div>
                        <div class="text-gray-800 font-medium">{{ $event->quota }} Orang</div>
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-sm font-bold text-gray-700 uppercase mb-2">Deskripsi</h3>
                    <p class="text-gray-600 text-sm whitespace-pre-line leading-relaxed">{{ $event->description }}</p>
                </div>

                <div class="pt-6 border-t border-gray-100 text-xs text-gray-400 flex justify-between">
                    <span>Dibuat: {{ $event->created_at->format('d M Y H:i') }}</span>
                    <span>Terakhir diubah: {{ $event->updated_at->format('d M Y H:i') }}</span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
