<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tiket Saya') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Flash Notifikasi -->
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
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">Daftar Tiket & Pendaftaran Event Anda</h3>
                    <a href="{{ route('events.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                        + Jelajahi Event Lainnya
                    </a>
                </div>

                @if ($registrations->isEmpty())
                    <div class="p-8 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                        </svg>
                        Anda belum mendaftar di event manapun.
                        <div class="mt-4">
                            <a href="{{ route('events.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-semibold hover:bg-indigo-700">
                                Cari Event Sekarang
                            </a>
                        </div>
                    </div>
                @else
                    <div class="divide-y divide-gray-200">
                        @foreach ($registrations as $reg)
                            <div class="p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 hover:bg-gray-50 transition">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                            @if ($reg->status === 'registered') bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ $reg->status === 'registered' ? 'Aktif (Terdaftar)' : 'Dibatalkan' }}
                                        </span>
                                        <span class="text-xs text-gray-400">
                                            Daftar: {{ $reg->created_at->format('d M Y, H:i') }}
                                        </span>
                                    </div>
                                    <h4 class="text-lg font-bold text-gray-900">
                                        {{ $reg->event->title }}
                                    </h4>
                                    <div class="text-xs text-gray-500 flex flex-wrap gap-4">
                                        <span>📅 {{ $reg->event->start_at->format('d M Y, H:i') }} WIB</span>
                                        <span>📍 {{ $reg->event->location }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 self-end md:self-center">
                                    @if ($reg->status === 'registered')
                                        <a href="{{ route('registrations.show', $reg) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs uppercase tracking-wider rounded-md shadow-sm transition">
                                            Buka Tiket QR
                                        </a>

                                        <!-- Form Batalkan Tiket (Aman: CSRF + DELETE) -->
                                        <form method="POST" action="{{ route('registrations.destroy', $reg) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan tiket pendaftaran ini? Kuota akan dikembalikan.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-3 py-2 border border-red-300 text-red-700 hover:bg-red-50 font-semibold text-xs uppercase tracking-wider rounded-md transition">
                                                Batalkan
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Tiket tidak berlaku</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="p-4 border-t border-gray-200">
                        {{ $registrations->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
