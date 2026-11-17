<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-maroon-800 leading-tight">
                {{ __('Laporan Kehadiran Event') }}
            </h2>

            <!-- Pencarian Event -->
            <form method="GET" action="{{ route('admin.reports.index') }}" class="flex items-center gap-2">
                <input
                    type="text"
                    name="q"
                    value="{{ $search ?? '' }}"
                    placeholder="Cari event..."
                    class="text-xs rounded-md border-gray-300 shadow-sm focus:border-maroon-600 focus:ring-maroon-600 w-48 sm:w-64"
                />
                <button type="submit" class="px-3 py-1.5 bg-maroon-700 hover:bg-maroon-800 text-white rounded text-xs font-semibold uppercase transition">
                    Cari
                </button>
                @if (!empty($search))
                    <a href="{{ route('admin.reports.index') }}" class="text-xs text-gray-500 hover:text-gray-700 underline">Reset</a>
                @endif
            </form>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">Rekapitulasi Terdaftar vs Hadir</h3>
                        <p class="text-xs text-gray-500 mt-1">Data statistik kehadiran peserta dari pemindaian tiket QR per event.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider font-semibold">
                            <tr>
                                <th class="px-6 py-3 text-left">Nama Event</th>
                                <th class="px-6 py-3 text-center">Waktu Acara</th>
                                <th class="px-6 py-3 text-center">Kuota</th>
                                <th class="px-6 py-3 text-center">Terdaftar</th>
                                <th class="px-6 py-3 text-center">Hadir</th>
                                <th class="px-6 py-3 text-center">Tingkat Kehadiran</th>
                                <th class="px-6 py-3 text-right">Aksi & Ekspor</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($events as $event)
                                @php
                                    $regCount = $event->registered_count ?? 0;
                                    $attCount = $event->attended_count ?? 0;
                                    $percentage = $regCount > 0 ? round(($attCount / $regCount) * 100, 1) : 0;
                                @endphp
                                <tr class="hover:bg-gray-50/80 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900">{{ $event->title }}</div>
                                        <div class="text-xs text-gray-500">📍 {{ $event->location }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center text-xs text-gray-600">
                                        {{ $event->start_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 text-center font-mono font-semibold text-gray-700">
                                        {{ $event->quota }}
                                    </td>
                                    <td class="px-6 py-4 text-center font-mono font-semibold text-blue-600">
                                        {{ $regCount }}
                                    </td>
                                    <td class="px-6 py-4 text-center font-mono font-bold text-green-600">
                                        {{ $attCount }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <div class="w-16 bg-gray-200 rounded-full h-2 overflow-hidden">
                                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                            </div>
                                            <span class="text-xs font-bold font-mono text-gray-700">{{ $percentage }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                        <a href="{{ route('admin.reports.show', $event) }}" class="inline-flex items-center px-3 py-1.5 bg-maroon-50 text-maroon-700 hover:bg-maroon-100 border border-maroon-200 rounded text-xs font-bold transition">
                                            Rincian
                                        </a>

                                        <!-- Ekspor CSV Aman (Mitigasi Formula Injection) -->
                                        <a href="{{ route('admin.reports.export', $event) }}" class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded text-xs font-semibold transition shadow-sm">
                                            📥 Ekspor CSV
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-400 italic">
                                        Belum ada data event yang tersedia.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-gray-200">
                    {{ $events->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
