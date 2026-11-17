<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Log Aktivitas Pengguna
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Notifikasi --}}
            @if (session('success'))
                <div class="bg-green-50 border border-green-300 text-green-800 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                {{-- Header + Pencarian --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Jejak Audit Keamanan</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Menampilkan {{ $logs->total() }} entri log aktivitas
                        </p>
                    </div>
                    <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="flex gap-2">
                        <input
                            type="text"
                            name="q"
                            value="{{ $search }}"
                            placeholder="Cari aksi, IP, atau nama user..."
                            class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 w-72"
                        />
                        <button type="submit"
                            class="bg-maroon-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-maroon-800 transition font-bold">
                            Cari
                        </button>
                        @if ($search)
                            <a href="{{ route('admin.activity-logs.index') }}"
                               class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-200 transition">
                                Reset
                            </a>
                        @endif
                    </form>
                </div>

                {{-- Tabel Log --}}
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full text-sm divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">
                                    Waktu
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Pengguna
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-36">
                                    IP Address
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($logs as $log)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">
                                        {{ $log->created_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i:s') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($log->user)
                                            <div class="font-medium text-gray-900">{{ $log->user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $log->user->email }}</div>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium
                                                {{ $log->user->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                                {{ $log->user->role }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 italic text-xs">Anonim / User Dihapus</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @php
                                            // Tentukan warna badge berdasarkan jenis aksi
                                            $badgeClass = match(true) {
                                                str_starts_with($log->action, 'login_failed') => 'bg-red-100 text-red-700',
                                                str_starts_with($log->action, 'login') => 'bg-green-100 text-green-700',
                                                str_starts_with($log->action, 'logout') => 'bg-gray-100 text-gray-600',
                                                str_starts_with($log->action, 'access_denied') => 'bg-red-100 text-red-700',
                                                str_starts_with($log->action, 'scan_qr_success') => 'bg-green-100 text-green-700',
                                                str_starts_with($log->action, 'scan_qr_rejected') => 'bg-red-100 text-red-700',
                                                str_starts_with($log->action, 'event_deleted') => 'bg-red-100 text-red-700',
                                                str_starts_with($log->action, 'cancel_ticket') => 'bg-yellow-100 text-yellow-700',
                                                default => 'bg-blue-100 text-blue-700',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-mono font-medium {{ $badgeClass }}">
                                            {{-- Output di-escape Blade otomatis via {{ }} --}}
                                            {{ $log->action }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-gray-600 font-mono whitespace-nowrap">
                                        {{ $log->ip_address ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-10 text-center text-gray-400">
                                        <div class="text-4xl mb-2">📋</div>
                                        <p>Belum ada log aktivitas yang tercatat.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($logs->hasPages())
                    <div class="mt-4">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
