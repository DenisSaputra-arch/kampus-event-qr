<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-gray-900 leading-tight">
                    {{ __('Dompet Tiket Saya') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    Kelola dan akses seluruh e-tiket event kampus yang telah Anda daftarkan.
                </p>
            </div>
            <a href="{{ route('events.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/20 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Daftar Event Lainnya
            </a>
        </div>
    </x-slot>

    <div class="py-10 bg-slate-50 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Flash Notifikasi -->
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-2xl flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-2xl flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white rounded-3xl border border-gray-200/80 shadow-xs overflow-hidden">
                <div class="p-6 sm:p-8 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Riwayat Pendaftaran</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Menampilkan {{ $registrations->total() }} tiket</p>
                    </div>
                </div>

                @if ($registrations->isEmpty())
                    <div class="p-12 text-center max-w-sm mx-auto">
                        <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                        </div>
                        <h4 class="text-base font-bold text-gray-900 mb-1">Dompet Tiket Masih Kosong</h4>
                        <p class="text-xs text-gray-500 mb-6">Anda belum memiliki tiket event apapun saat ini. Temukan event menarik dan daftarkan diri Anda sekarang.</p>
                        <a href="{{ route('events.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold shadow hover:bg-indigo-700 transition">
                            Cari Event Kampus
                        </a>
                    </div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach ($registrations as $reg)
                            <div class="p-6 sm:p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 hover:bg-slate-50/60 transition">
                                <div class="space-y-2 flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase
                                            @if ($reg->status === 'registered') bg-emerald-100 text-emerald-800
                                            @else bg-rose-100 text-rose-800 @endif">
                                            {{ $reg->status === 'registered' ? 'Tiket Aktif' : 'Dibatalkan' }}
                                        </span>
                                        <span class="text-[11px] text-gray-400">
                                            Didaftarkan: {{ $reg->created_at->format('d M Y, H:i') }} WIB
                                        </span>
                                    </div>

                                    <h4 class="text-lg font-bold text-gray-900 hover:text-indigo-600 transition">
                                        {{ $reg->event->title }}
                                    </h4>

                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-500">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            {{ $reg->event->start_at->format('d M Y, H:i') }} WIB
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                            {{ $reg->event->location }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 shrink-0 w-full sm:w-auto">
                                    <a href="{{ route('registrations.show', $reg) }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold text-xs shadow-md shadow-indigo-600/20 transition transform active:scale-95 w-full sm:w-auto">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                        Buka Tiket QR
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="p-6 border-t border-gray-100">
                        {{ $registrations->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
