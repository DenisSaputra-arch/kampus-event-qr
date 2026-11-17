<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-maroon-800 leading-tight">
                    {{ __('Rincian Kehadiran Event') }}
                </h2>
                <div class="text-xs text-gray-500 mt-1">
                    Event: <span class="font-bold text-gray-800">{{ $event->title }}</span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.reports.export', $event) }}" class="inline-flex items-center px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded text-xs font-bold uppercase tracking-wider transition shadow-sm">
                    📥 Unduh File CSV
                </a>
                <a href="{{ route('admin.reports.index') }}" class="text-xs text-maroon-700 hover:text-maroon-900 font-semibold">
                    &larr; Kembali ke Laporan
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Ringkasan Statistik Kartu -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-5 rounded-lg border-l-4 border-charcoal-700 shadow-sm border-t border-r border-b border-gray-200">
                    <div class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Total Kuota</div>
                    <div class="text-2xl font-black text-gray-900 mt-1">{{ $event->quota }}</div>
                    <div class="text-[11px] text-gray-500 mt-1">Kapasitas maksimal</div>
                </div>

                <div class="bg-white p-5 rounded-lg border-l-4 border-maroon-700 shadow-sm border-t border-r border-b border-gray-200">
                    <div class="text-xs text-maroon-700 font-semibold uppercase tracking-wider">Total Terdaftar</div>
                    <div class="text-2xl font-black text-maroon-800 mt-1">{{ $totalRegistered }}</div>
                    <div class="text-[11px] text-gray-500 mt-1">Pendaftaran aktif</div>
                </div>

                <div class="bg-white p-5 rounded-lg border-l-4 border-emerald-600 shadow-sm border-t border-r border-b border-gray-200">
                    <div class="text-xs text-emerald-700 font-semibold uppercase tracking-wider">Total Hadir</div>
                    <div class="text-2xl font-black text-emerald-700 mt-1">{{ $totalAttended }}</div>
                    <div class="text-[11px] text-gray-500 mt-1">QR Berhasil discan</div>
                </div>

                <div class="bg-white p-5 rounded-lg border-l-4 border-gold-500 shadow-sm border-t border-r border-b border-gray-200">
                    <div class="text-xs text-yellow-700 font-semibold uppercase tracking-wider">Belum Hadir</div>
                    <div class="text-2xl font-black text-yellow-600 mt-1">{{ max(0, $totalRegistered - $totalAttended) }}</div>
                    <div class="text-[11px] text-gray-500 mt-1">Menunggu presensi</div>
                </div>
            </div>

            <!-- Tabel Detail Peserta -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-gray-900 text-sm">Daftar Presensi Peserta</h3>
                    <span class="text-xs text-gray-400">Total baris: {{ $registrations->total() }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider font-semibold">
                            <tr>
                                <th class="px-6 py-3 text-left">Nama Peserta</th>
                                <th class="px-6 py-3 text-left">Email</th>
                                <th class="px-6 py-3 text-center">Status Pendaftaran</th>
                                <th class="px-6 py-3 text-center">Status Kehadiran</th>
                                <th class="px-6 py-3 text-center">Waktu Presensi</th>
                                <th class="px-6 py-3 text-right">Petugas Scan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($registrations as $reg)
                                <tr class="hover:bg-gray-50/80 transition">
                                    <td class="px-6 py-4 font-bold text-gray-900">
                                        {{ $reg->user->name }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 text-xs">
                                        {{ $reg->user->email }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if ($reg->status === 'registered')
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                                Terdaftar
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                                Dibatalkan
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if ($reg->status === 'cancelled')
                                            <span class="text-xs text-gray-400 italic">Batal</span>
                                        @elseif ($reg->attendance)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                                ✓ Hadir
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                                Belum Hadir
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center text-xs font-mono text-gray-600">
                                        @if ($reg->attendance)
                                            {{ $reg->attendance->scanned_at->format('d/m/Y H:i:s') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right text-xs text-gray-600">
                                        {{ $reg->attendance?->scanner?->name ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-400 italic">
                                        Belum ada peserta yang mendaftar pada event ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-gray-200">
                    {{ $registrations->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
