<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-black text-2xl text-charcoal-900 leading-tight">
                    {{ __('Panel Kontrol Administrator') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1 font-medium">
                    Manajemen event kampus, monitoring kehadiran presensi QR, dan jejak audit sistem.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.scan.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs uppercase tracking-wider shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    Buka Scanner QR
                </a>
                <a href="{{ route('admin.events.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded bg-maroon-700 hover:bg-maroon-800 text-white font-bold text-xs uppercase tracking-wider shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Buat Event Baru
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-[#F5F5F5] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <!-- 4 KARTU STATISTIK UTAMA (ADMIN EDITORIAL) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Event -->
                <div class="bg-white rounded-md p-6 border-l-4 border-maroon-700 border-y border-r border-gray-200 shadow-2xs flex items-center gap-4">
                    <div class="w-12 h-12 rounded bg-maroon-50 text-maroon-700 flex items-center justify-center shrink-0 border border-maroon-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total Event</div>
                        <div class="text-2xl font-black text-charcoal-900 mt-0.5">{{ $totalEvents }}</div>
                        <a href="{{ route('admin.events.index') }}" class="text-[11px] font-bold text-maroon-700 hover:underline">Kelola &rarr;</a>
                    </div>
                </div>

                <!-- Pendaftar Aktif -->
                <div class="bg-white rounded-md p-6 border-l-4 border-gold-500 border-y border-r border-gray-200 shadow-2xs flex items-center gap-4">
                    <div class="w-12 h-12 rounded bg-gold-400/20 text-charcoal-900 flex items-center justify-center shrink-0 border border-gold-500/40">
                        <svg class="w-6 h-6 text-maroon-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div>
                        <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Pendaftar Aktif</div>
                        <div class="text-2xl font-black text-charcoal-900 mt-0.5">{{ $totalRegistrations }}</div>
                        <span class="text-[11px] text-gray-500 font-medium">Tiket terbit</span>
                    </div>
                </div>

                <!-- Kehadiran Terverifikasi -->
                <div class="bg-white rounded-md p-6 border-l-4 border-emerald-600 border-y border-r border-gray-200 shadow-2xs flex items-center gap-4">
                    <div class="w-12 h-12 rounded bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0 border border-emerald-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Presensi Scan</div>
                        <div class="text-2xl font-black text-emerald-800 mt-0.5">{{ $totalAttendances }}</div>
                        <a href="{{ route('admin.reports.index') }}" class="text-[11px] font-bold text-emerald-700 hover:underline">Lihat Laporan &rarr;</a>
                    </div>
                </div>

                <!-- Jejak Audit Log -->
                <div class="bg-white rounded-md p-6 border-l-4 border-charcoal-700 border-y border-r border-gray-200 shadow-2xs flex items-center gap-4">
                    <div class="w-12 h-12 rounded bg-gray-100 text-charcoal-900 flex items-center justify-center shrink-0 border border-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Audit Security Log</div>
                        <div class="text-2xl font-black text-charcoal-900 mt-0.5">{{ $totalLogs }}</div>
                        <a href="{{ route('admin.activity-logs.index') }}" class="text-[11px] font-bold text-maroon-700 hover:underline">Periksa Log &rarr;</a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Daftar Event Terbaru -->
                <div class="lg:col-span-2 bg-white rounded-md p-6 sm:p-7 border border-gray-200 shadow-2xs space-y-6">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                        <h3 class="text-base font-black text-charcoal-900 uppercase tracking-wide">Agenda Event Terdekat</h3>
                        <a href="{{ route('admin.events.index') }}" class="text-xs font-bold text-maroon-700 hover:underline">Lihat Semua &rarr;</a>
                    </div>

                    <div class="space-y-3">
                        @forelse ($recentEvents as $event)
                            <div class="p-4 rounded border border-gray-200 hover:border-maroon-700 hover:bg-gray-50 transition flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                            @if ($event->status === 'published') bg-emerald-100 text-emerald-900 border border-emerald-300
                                            @elseif ($event->status === 'draft') bg-amber-100 text-amber-900 border border-amber-300
                                            @else bg-red-100 text-red-900 border border-red-300 @endif">
                                            {{ $event->status }}
                                        </span>
                                        <span class="text-xs text-gray-500 font-semibold">{{ $event->start_at->translatedFormat('d M Y, H:i') }} WIB</span>
                                    </div>
                                    <div class="font-black text-charcoal-900 text-sm">{{ $event->title }}</div>
                                    <div class="text-xs text-gray-500">{{ $event->location }}</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.reports.show', $event) }}" class="px-3 py-1.5 rounded bg-gray-100 hover:bg-gray-200 text-charcoal-900 font-bold text-xs transition">
                                        Laporan
                                    </a>
                                    <a href="{{ route('admin.events.edit', $event) }}" class="px-3 py-1.5 rounded bg-maroon-50 hover:bg-maroon-100 text-maroon-700 font-bold text-xs border border-maroon-200 transition">
                                        Edit
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-400 text-sm">Belum ada event yang dibuat.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Log Keamanan Terbaru -->
                <div class="lg:col-span-1 bg-white rounded-md p-6 sm:p-7 border border-gray-200 shadow-2xs space-y-6">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                        <h3 class="text-base font-black text-charcoal-900 uppercase tracking-wide">Audit Log Terkini</h3>
                        <a href="{{ route('admin.activity-logs.index') }}" class="text-xs font-bold text-maroon-700 hover:underline">Semua &rarr;</a>
                    </div>

                    <div class="space-y-2.5">
                        @forelse ($recentLogs as $log)
                            <div class="p-3 rounded bg-gray-50 border border-gray-200 text-xs space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-mono font-bold text-maroon-700 truncate max-w-[150px]">{{ $log->action }}</span>
                                    <span class="text-[10px] text-gray-400 font-medium">{{ $log->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="text-gray-700 font-medium text-[11px]">
                                    {{ $log->user ? $log->user->name : 'Anonim' }}
                                    <span class="text-gray-400 font-mono text-[10px]">({{ $log->ip_address }})</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-gray-400 text-xs">Belum ada log aktivitas.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>