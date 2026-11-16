<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="font-semibold text-green-700">Selamat datang di Panel Admin Kampus Event QR!</p>
                    <p class="mt-2 text-sm text-gray-600">Halaman ini hanya dapat diakses oleh user dengan role <code class="bg-gray-100 px-2 py-0.5 rounded text-red-600">admin</code> melalui proteksi middleware RBAC.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
