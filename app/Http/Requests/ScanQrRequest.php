<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScanQrRequest extends FormRequest
{
    /**
     * Otorisasi Request:
     * Memastikan hanya user dengan role 'admin' yang dapat melakukan pemindaian presensi.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    /**
     * Validasi Input:
     * Menolak input kosong atau input dengan format yang tidak wajar.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'event_id' => ['required', 'integer', 'exists:events,id'],
            'qr_token' => ['required', 'string', 'max:128'],
        ];
    }

    /**
     * Kustomisasi pesan error validasi.
     */
    public function messages(): array
    {
        return [
            'event_id.required' => 'Pilih event terlebih dahulu.',
            'event_id.exists' => 'Event yang dipilih tidak ditemukan dalam sistem.',
            'qr_token.required' => 'Token QR tidak boleh kosong.',
        ];
    }
}
