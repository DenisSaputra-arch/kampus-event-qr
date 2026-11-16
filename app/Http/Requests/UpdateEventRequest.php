<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    /**
     * Otorisasi Request (Keamanan Akses):
     * Memastikan hanya user dengan role 'admin' yang dapat memperbarui data event.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    /**
     * Aturan Validasi Input saat memperbarui event.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'location' => ['required', 'string', 'max:255'],
            'start_at' => ['required', 'date'],
            'quota' => ['required', 'integer', 'min:1', 'max:100000'],
            'status' => ['required', 'string', 'in:draft,published,cancelled'],
        ];
    }

    /**
     * Kustomisasi pesan error validasi.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul event wajib diisi.',
            'description.required' => 'Deskripsi event wajib diisi.',
            'location.required' => 'Lokasi event wajib diisi.',
            'start_at.required' => 'Waktu pelaksanaan event wajib diisi.',
            'quota.required' => 'Kuota peserta wajib diisi.',
            'quota.min' => 'Kuota peserta minimal 1 orang.',
            'status.in' => 'Status event tidak valid.',
        ];
    }
}
