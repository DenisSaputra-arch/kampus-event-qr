<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('E-Tiket Event Kampus') }}
            </h2>
            <a href="{{ route('registrations.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                &larr; Kembali ke Tiket Saya
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Flash Notifikasi -->
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Kartu Tiket Desain Modern -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden relative" id="ticket-card">
                <!-- Header Tiket -->
                <div class="bg-gradient-to-r from-indigo-700 to-purple-800 p-6 text-white text-center">
                    <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-xs font-semibold tracking-wider uppercase mb-2">
                        Official Event Pass
                    </span>
                    <h1 class="text-xl sm:text-2xl font-black tracking-tight">
                        {{ $registration->event->title }}
                    </h1>
                </div>

                <!-- Bagian QR Code -->
                <div class="p-8 text-center bg-gray-50/50 flex flex-col items-center justify-center border-b border-dashed border-gray-300">
                    <div class="p-4 bg-white rounded-xl shadow-md border border-gray-200 inline-block mb-3">
                        <!-- Render SVG QR Code dari token acak kriptografis -->
                        <div class="w-60 h-60 flex items-center justify-center">
                            {!! $qrCode !!}
                        </div>
                    </div>

                    <div class="text-xs text-gray-500 font-mono tracking-widest bg-gray-100 px-3 py-1 rounded-md border border-gray-200 max-w-full truncate">
                        TOKEN: {{ $registration->qr_token }}
                    </div>
                    <p class="text-xs text-gray-400 mt-2">
                        Tunjukkan QR Code ini kepada panitia / administrator di pintu masuk acara untuk dipindai.
                    </p>
                </div>

                <!-- Rincian Informasi Tiket -->
                <div class="p-6 sm:p-8 space-y-4 text-sm">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-gray-400 font-semibold uppercase">Nama Peserta</div>
                            <div class="font-bold text-gray-900 text-base">{{ $registration->user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $registration->user->email }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400 font-semibold uppercase">Status Tiket</div>
                            <div>
                                @if ($registration->status === 'registered')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                        TERDAFTAR AKTIF
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                        DIBATALKAN
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                        <div>
                            <div class="text-xs text-gray-400 font-semibold uppercase">Jadwal Acara</div>
                            <div class="font-semibold text-gray-800">{{ $registration->event->start_at->format('d M Y, H:i') }} WIB</div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400 font-semibold uppercase">Lokasi</div>
                            <div class="font-semibold text-gray-800">{{ $registration->event->location }}</div>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-gray-100 text-xs text-gray-400 flex justify-between">
                        <span>Nomor Tiket: #{{ $registration->id }}</span>
                        <span>Waktu Daftar: {{ $registration->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>

                <!-- Footer Aksi -->
                <div class="p-6 bg-gray-50 border-t border-gray-200 flex flex-wrap items-center justify-between gap-4">
                    <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-xs font-bold uppercase tracking-wider rounded-md transition shadow">
                        🖨️ Cetak / Simpan PDF
                    </button>

                    @if ($registration->status === 'registered')
                        <form method="POST" action="{{ route('registrations.destroy', $registration) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan tiket ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-800 underline">
                                Batalkan Tiket Saya
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
