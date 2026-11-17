<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-extrabold text-xl text-gray-900 leading-tight flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                {{ __('E-Tiket Resmi Event') }}
            </h2>
            <a href="{{ route('registrations.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Tiket Saya
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Flash Notifikasi -->
            @if (session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-2xl flex items-center gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <!-- PASS / BOARDING TICKET DESAIN PREMIUM -->
            <div class="bg-white rounded-3xl shadow-xl border border-gray-200/80 overflow-hidden relative" id="ticket-card">
                <!-- Header Card Tiket -->
                <div class="relative bg-gradient-to-r from-indigo-900 via-indigo-800 to-purple-900 p-8 text-white text-center overflow-hidden">
                    <div class="absolute -top-12 -right-12 w-40 h-40 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
                    <div class="absolute -bottom-12 -left-12 w-40 h-40 bg-purple-500/20 rounded-full blur-2xl pointer-events-none"></div>

                    <div class="relative z-10 space-y-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/15 backdrop-blur-md rounded-full text-[11px] font-extrabold tracking-widest uppercase border border-white/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                            Official Event Pass
                        </span>

                        <h1 class="text-xl sm:text-2xl font-black tracking-tight leading-snug">
                            {{ $registration->event->title }}
                        </h1>
                        <p class="text-xs text-indigo-200 font-medium">
                            Tiket ID: #{{ str_pad($registration->id, 6, '0', STR_PAD_LEFT) }}
                        </p>
                    </div>
                </div>

                <!-- Bagian Frame QR Code Presensi -->
                <div class="p-8 text-center bg-slate-50/70 flex flex-col items-center justify-center border-b border-dashed border-gray-300 relative">
                    <!-- Perforated side notches (Guntingan Tiket) -->
                    <div class="absolute -left-4 -bottom-4 w-8 h-8 bg-slate-50 rounded-full border border-gray-200/80"></div>
                    <div class="absolute -right-4 -bottom-4 w-8 h-8 bg-slate-50 rounded-full border border-gray-200/80"></div>

                    <div class="p-5 bg-white rounded-2xl shadow-md border border-gray-200/80 inline-block mb-3">
                        <div class="w-56 h-56 flex items-center justify-center">
                            {!! $qrCode !!}
                        </div>
                    </div>

                    <div class="flex items-center gap-2 bg-indigo-50 border border-indigo-100 px-3 py-1.5 rounded-xl max-w-full">
                        <span class="text-[10px] font-bold text-indigo-500 uppercase tracking-wider shrink-0">TOKEN:</span>
                        <span class="text-xs font-mono font-bold text-indigo-900 truncate">{{ $registration->qr_token }}</span>
                    </div>

                    <p class="text-xs text-gray-500 mt-3 max-w-sm">
                        Tunjukkan QR Code ini kepada panitia acara di meja registrasi masuk untuk diverifikasi.
                    </p>
                </div>

                <!-- Informasi Peserta & Jadwal -->
                <div class="p-8 space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Nama Peserta</span>
                            <div class="font-extrabold text-gray-900 text-base leading-snug">{{ $registration->user->name }}</div>
                            <div class="text-xs text-gray-500 truncate">{{ $registration->user->email }}</div>
                        </div>
                        <div class="space-y-1 text-right sm:text-left">
                            <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Status Validasi</span>
                            <div>
                                @if ($registration->isAttended())
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-blue-100 text-blue-800 border border-blue-200">
                                        Hadir
                                    </span>
                                @elseif ($registration->status === 'registered')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        Terdaftar Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-rose-100 text-rose-800 border border-rose-200">
                                        Dibatalkan
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-gray-100 text-xs">
                        <div>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Waktu Pelaksanaan</span>
                            <div class="font-bold text-gray-900 text-sm mt-0.5">{{ $registration->event->start_at->format('l, d F Y') }}</div>
                            <div class="text-indigo-600 font-semibold">Pukul {{ $registration->event->start_at->format('H:i') }} WIB</div>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Lokasi / Ruangan</span>
                            <div class="font-bold text-gray-900 text-sm mt-0.5">{{ $registration->event->location }}</div>
                            <div class="text-gray-500">Pintu Masuk Utama</div>
                        </div>
                    </div>
                </div>

                <!-- Footer Aksi: Cetak / Batalkan -->
                <div class="p-6 bg-slate-50 border-t border-gray-200/80 flex flex-wrap items-center justify-between gap-4">
                    <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 hover:bg-black text-white text-xs font-bold uppercase tracking-wider rounded-xl transition shadow-sm transform active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Cetak / Simpan PDF
                    </button>

                    @if ($registration->status === 'registered' && ! $registration->isAttended() && ! $registration->event->start_at->isPast())
                        <form method="POST" action="{{ route('registrations.destroy', $registration) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pendaftaran tiket ini? Kuota akan dilepaskan untuk peserta lain.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-bold text-rose-600 hover:text-rose-800 transition">
                                Batalkan Tiket Saya
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
