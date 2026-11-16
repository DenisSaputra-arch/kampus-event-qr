<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Kelola Event (Admin Panel)') }}
            </h2>
            <a href="{{ route('admin.events.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                + Tambah Event Baru
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Flash Notifikasi Sukses -->
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Filter dan Pencarian -->
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6">
                <form method="GET" action="{{ route('admin.events.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="q" class="block text-xs font-medium text-gray-700 mb-1">Cari Judul / Lokasi</label>
                        <input type="text" name="q" id="q" value="{{ $search ?? '' }}" placeholder="Ketik kata kunci..." class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    </div>
                    <div>
                        <label for="status" class="block text-xs font-medium text-gray-700 mb-1">Status Event</label>
                        <select name="status" id="status" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua Status</option>
                            <option value="published" {{ ($status ?? '') === 'published' ? 'selected' : '' }}>Published</option>
                            <option value="draft" {{ ($status ?? '') === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="cancelled" {{ ($status ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-gray-700 transition">
                            Filter
                        </button>
                        @if (!empty($search) || !empty($status))
                            <a href="{{ route('admin.events.index') }}" class="text-xs text-gray-500 hover:text-gray-700 underline py-2">Reset</a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Tabel Data Event -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">ID</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Judul Event</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Lokasi</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Waktu Mulai</th>
                                <th class="px-6 py-3 text-center font-semibold text-gray-600">Kuota</th>
                                <th class="px-6 py-3 text-center font-semibold text-gray-600">Status</th>
                                <th class="px-6 py-3 text-right font-semibold text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($events as $event)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-gray-500 font-mono text-xs">#{{ $event->id }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        {{ $event->title }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ $event->location }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 text-xs">
                                        {{ $event->start_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="px-6 py-4 text-center font-semibold text-gray-700">
                                        {{ $event->quota }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if ($event->status === 'published')
                                            <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">Published</span>
                                        @elseif ($event->status === 'draft')
                                            <span class="px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded-full">Draft</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded-full">Cancelled</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                        <a href="{{ route('admin.events.show', $event) }}" class="text-indigo-600 hover:text-indigo-900 text-xs font-semibold">Lihat</a>
                                        <a href="{{ route('admin.events.edit', $event) }}" class="text-blue-600 hover:text-blue-900 text-xs font-semibold">Ubah</a>

                                        <!-- Form Delete Aman (CSRF & Method Spoofing DELETE) -->
                                        <form method="POST" action="{{ route('admin.events.destroy', $event) }}" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus event ini? Data yang terhapus tidak dapat dikembalikan.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-xs font-semibold">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                        Tidak ada data event yang ditemukan.
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
