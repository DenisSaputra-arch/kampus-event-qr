<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use App\Services\ActivityLogger;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class RegistrationController extends Controller
{
    /**
     * Menampilkan daftar tiket yang dimiliki oleh peserta yang sedang login.
     */
    public function index(Request $request): View
    {
        $registrations = $request->user()
            ->registrations()
            ->with('event')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('registrations.index', compact('registrations'));
    }

    /**
     * Mendaftar ke event dengan pemeriksaan kuota yang aman dari Race Condition.
     *
     * Keamanan:
     * 1. DB Transaction + lockForUpdate(): Mengunci baris event dan baris pendaftaran terkait
     *    selama transaksi berlangsung untuk mencegah Over-subscription akibat concurrency.
     * 2. CSPRNG Token: Token QR di-generate menggunakan bin2hex(random_bytes(32))
     *    sehingga tidak dapat ditebak (unpredictable) dan unik.
     */
    public function store(Request $request, Event $event): RedirectResponse
    {
        $user = $request->user();

        // Validasi di sisi server: tolak pendaftaran jika event statusnya bukan published atau waktunya sudah lewat
        if ($event->status !== 'published') {
            return redirect()
                ->back()
                ->with('error', 'Pendaftaran ditolak: Event ini tidak sedang menerima pendaftaran (status: ' . $event->status . ').');
        }

        if ($event->start_at->isPast()) {
            return redirect()
                ->back()
                ->with('error', 'Pendaftaran ditolak: Waktu pelaksanaan event telah lewat.');
        }

        try {
            $registration = DB::transaction(function () use ($event, $user) {
                // 1. Kunci baris event untuk update (Pessimistic Locking)
                $lockedEvent = Event::where('id', $event->id)->lockForUpdate()->firstOrFail();

                // 2. Pastikan event masih berstatus published
                if ($lockedEvent->status !== 'published') {
                    throw new Exception('Pendaftaran ditolak: Event ini tidak sedang menerima pendaftaran (status: ' . $lockedEvent->status . ').');
                }

                // 3. Pastikan waktu pelaksanaan belum lewat
                if ($lockedEvent->start_at->isPast()) {
                    throw new Exception('Pendaftaran ditolak: Waktu pelaksanaan event telah lewat.');
                }

                // 4. Periksa apakah user sudah pernah mendaftar pada event ini
                $existing = Registration::where('event_id', $lockedEvent->id)
                    ->where('user_id', $user->id)
                    ->lockForUpdate()
                    ->first();

                if ($existing) {
                    if ($existing->status === 'registered') {
                        throw new Exception('Anda sudah terdaftar aktif pada event ini.');
                    }

                    // Jika sebelumnya pernah membatalkan, re-aktifkan pendaftaran dengan token baru
                    $currentActive = Registration::where('event_id', $lockedEvent->id)
                        ->where('status', 'registered')
                        ->lockForUpdate()
                        ->count();

                    if ($currentActive >= $lockedEvent->quota) {
                        throw new Exception('Mohon maaf, kuota peserta untuk event ini telah penuh.');
                    }

                    // Generate token acak kriptografis baru (256-bit entropy)
                    $newToken = bin2hex(random_bytes(32));
                    $existing->update([
                        'qr_token' => $newToken,
                        'status' => 'registered',
                    ]);

                    return $existing;
                }

                // 5. Cek kuota peserta saat ini di dalam transaksi terkunci
                $currentActive = Registration::where('event_id', $lockedEvent->id)
                    ->where('status', 'registered')
                    ->lockForUpdate()
                    ->count();

                if ($currentActive >= $lockedEvent->quota) {
                    throw new Exception('Mohon maaf, kuota peserta untuk event ini telah penuh.');
                }

                // 6. Buat token QR acak berbasis CSPRNG (bukan ID auto-increment)
                $qrToken = bin2hex(random_bytes(32));

                return Registration::create([
                    'event_id' => $lockedEvent->id,
                    'user_id' => $user->id,
                    'qr_token' => $qrToken,
                    'status' => 'registered',
                ]);
            });

            // Catat pendaftaran berhasil (audit trail — tanpa token QR!)
            ActivityLogger::log('register_event: event_id=' . $event->id, $user);

            return redirect()
                ->route('registrations.show', $registration)
                ->with('success', 'Selamat! Anda berhasil terdaftar. Berikut adalah e-tiket QR Anda.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Menampilkan detail tiket dan QR Code milik peserta.
     *
     * Keamanan IDOR:
     * Memanggil RegistrationPolicy untuk memverifikasi bahwa tiket HANYA bisa dibuka
     * oleh pemilik sah atau administrator.
     */
    public function show(Registration $registration): View
    {
        Gate::authorize('view', $registration);

        $registration->load('event', 'user');

        // Menghasilkan QR Code SVG dari token acak pendaftaran
        $qrCode = QrCode::size(240)
            ->style('round')
            ->margin(1)
            ->generate($registration->qr_token);

        return view('registrations.show', compact('registration', 'qrCode'));
    }

    /**
     * Membatalkan pendaftaran tiket.
     *
     * Keamanan IDOR:
     * Otorisasi ketat via Policy untuk memastikan hanya pemilik tiket atau admin yang berhak membatalkan.
     */
    public function destroy(Registration $registration): RedirectResponse
    {
        Gate::authorize('delete', $registration);

        if ($registration->status === 'cancelled') {
            return redirect()->back()->with('error', 'Tiket ini sudah berstatus dibatalkan.');
        }

        $registration->update([
            'status' => 'cancelled',
        ]);

        // Catat pembatalan tiket (audit trail)
        ActivityLogger::log('cancel_ticket: registration_id=' . $registration->id, request()->user());

        return redirect()
            ->route('registrations.index')
            ->with('success', 'Pendaftaran tiket untuk event "' . $registration->event->title . '" berhasil dibatalkan.');
    }
}
