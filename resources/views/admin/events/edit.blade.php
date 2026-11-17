<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-maroon-800 leading-tight">
                {{ __('Ubah Data Event') }}
            </h2>
            <a href="{{ route('admin.events.index') }}" class="text-sm text-maroon-600 hover:text-maroon-800 font-medium">
                &larr; Kembali ke Kelola Event
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sm:p-8">
                <!-- Form Update: CSRF Token + Method Spoofing PUT + Validasi UpdateEventRequest -->
                <form method="POST" action="{{ route('admin.events.update', $event) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="title" value="Judul Event" />
                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $event->title)" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('title')" />
                    </div>

                    <div>
                        <x-input-label for="description" value="Deskripsi Lengkap" />
                        <textarea id="description" name="description" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-maroon-500 focus:ring-maroon-500 text-sm" required>{{ old('description', $event->description) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="location" value="Lokasi Acara" />
                            <x-text-input id="location" name="location" type="text" class="mt-1 block w-full" :value="old('location', $event->location)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('location')" />
                        </div>

                        <div>
                            <x-input-label for="start_at" value="Waktu Pelaksanaan" />
                            <x-text-input id="start_at" name="start_at" type="datetime-local" class="mt-1 block w-full" :value="old('start_at', $event->start_at->format('Y-m-d\TH:i'))" required />
                            <x-input-error class="mt-2" :messages="$errors->get('start_at')" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="quota" value="Kuota Peserta" />
                            <x-text-input id="quota" name="quota" type="number" min="1" max="100000" class="mt-1 block w-full" :value="old('quota', $event->quota)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('quota')" />
                        </div>

                        <div>
                            <x-input-label for="status" value="Status Publikasi" />
                            <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-maroon-500 focus:ring-maroon-500 text-sm" required>
                                <option value="published" {{ old('status', $event->status) === 'published' ? 'selected' : '' }}>Published (Aktif)</option>
                                <option value="draft" {{ old('status', $event->status) === 'draft' ? 'selected' : '' }}>Draft (Disimpan Sementara)</option>
                                <option value="cancelled" {{ old('status', $event->status) === 'cancelled' ? 'selected' : '' }}>Cancelled (Dibatalkan)</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('status')" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100">
                        <a href="{{ route('admin.events.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
                        <x-primary-button>
                            {{ __('Perbarui Event') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
