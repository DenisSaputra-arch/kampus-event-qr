<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ScanQrRequest;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\Registration;
use App\Services\ActivityLogger;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ScanController extends Controller
{
    /**
     * Menampilkan halaman scanner QR dengan kamera browser (html5-qrcode) dan form manual.
     */
    public function index(Request $request): View
    {
        $events = Event::query()
            ->where('status', 'published')
            ->orderBy('start_at', 'desc')
            ->get();

        $selectedEventId = $request->query('event_id');
        $selectedEvent = $selectedEventId ? $events->firstWhere('id', (int) $selectedEventId) : null;

        $isNotToday = $selectedEvent ? ! $selectedEvent->start_at->isToday() : false;

        $recentAttendances = collect();
        if ($selectedEvent) {
            $recentAttendances = Attendance::whereHas('registration', function ($q) use ($selectedEvent) {
                $q->where('event_id', $selectedEvent->id);
            })
            ->with(['registration.user', 'scanner'])
            ->orderBy('scanned_at', 'desc')
            ->limit(10)
            ->get();
        }

        return view('admin.scan.index', compact('events', 'selectedEvent', 'isNotToday', 'recentAttendances'));
    }

    /**
     * Memverifikasi token QR kehadiran secara berurutan.
     *
     * Keamanan & Logika Verifikasi Berurutan:
     * 1. Token ada: Memastikan token QR terdaftar di tabel registrations.
     * 2. Pendaftaran aktif: Memastikan status tiket bukan 'cancelled'.
     * 3. Event sesuai: Memastikan tiket cocok dengan event yang dipilih petugas.
     * 4. Belum pernah absen: Memeriksa apakah attendance sudah tercatat sebelumnya.
     * 5. Catat kehadiran: Menuliskan data ke tabel attendances dalam DB transaction terkunci.
     */
    public function verify(ScanQrRequest $request): JsonResponse|RedirectResponse
    {
        $selectedEventId = (int) $request->input('event_id');
        $qrToken = trim((string) $request->input('qr_token'));
        $admin = $request->user();

        try {
            $result = DB::transaction(function () use ($selectedEventId, $qrToken, $admin) {
                // 1. Verifikasi: Apakah token ada di database?
                $registration = Registration::with(['event', 'user'])
                    ->where('qr_token', $qrToken)
                    ->lockForUpdate()
                    ->first();

                if (! $registration) {
                    return [
                        'status' => 404,
                        'success' => false,
                        'reason' => 'token tidak ada',
                        'message' => 'Penolakan: Token QR tidak valid atau tidak terdaftar dalam sistem.',
                        'data' => null,
                    ];
                }

                // 2. Verifikasi: Apakah pendaftaran masih berstatus aktif?
                if ($registration->status !== 'registered') {
                    return [
                        'status' => 422,
                        'success' => false,
                        'reason' => 'tiket dibatalkan',
                        'message' => 'Penolakan: Pendaftaran tiket ini telah dibatalkan oleh peserta.',
                        'data' => ['participant_name' => $registration->user->name],
                    ];
                }

                // 3. Verifikasi: Apakah event sesuai dengan yang dipilih di halaman scan?
                if ($registration->event_id !== $selectedEventId) {
                    return [
                        'status' => 422,
                        'success' => false,
                        'reason' => 'event salah',
                        'message' => 'Penolakan: Tiket ini terdaftar untuk event "' . $registration->event->title . '", bukan event yang sedang Anda scan.',
                        'data' => ['participant_name' => $registration->user->name],
                    ];
                }

                // 4. Verifikasi: Apakah peserta belum pernah absen sebelumnya?
                $existing = Attendance::where('registration_id', $registration->id)
                    ->lockForUpdate()
                    ->first();

                if ($existing) {
                    return [
                        'status' => 422,
                        'success' => false,
                        'reason' => 'sudah absen',
                        'message' => 'Penolakan: Peserta atas nama "' . $registration->user->name . '" sudah melakukan presensi sebelumnya pada ' . $existing->scanned_at->format('d/m/Y H:i:s') . ' WIB.',
                        'data' => [
                            'participant_name' => $registration->user->name,
                            'scanned_at' => $existing->scanned_at->format('d/m/Y H:i:s'),
                        ],
                    ];
                }

                // 5. Verifikasi Sukses: Catat ke tabel attendances
                $attendance = Attendance::create([
                    'registration_id' => $registration->id,
                    'scanned_by' => $admin->id,
                    'scanned_at' => now(),
                ]);

                return [
                    'status' => 200,
                    'success' => true,
                    'message' => 'Presensi Berhasil! Kehadiran atas nama ' . $registration->user->name . ' telah tercatat.',
                    'registration_id' => $registration->id,
                    'data' => [
                        'participant_name' => $registration->user->name,
                        'participant_email' => $registration->user->email,
                        'event_title' => $registration->event->title,
                        'scanned_at' => $attendance->scanned_at->format('d/m/Y H:i:s'),
                    ],
                ];
            });

            // Catat log scan (berhasil / gagal) — tanpa token QR
            if ($result['success']) {
                $logAction = 'scan_qr_success: event_id=' . $selectedEventId;
            } else {
                $reason = $result['reason'] ?? 'alasan tidak diketahui';
                $logAction = 'scan_qr_rejected: alasan=' . $reason . ' (event_id=' . $selectedEventId . ')';
            }
            ActivityLogger::log($logAction, $admin);

            // Respon sesuai jenis request (JSON untuk scanning kamera via AJAX, Redirect untuk input form)
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => $result['success'],
                    'message' => $result['message'],
                    'data' => $result['data'],
                ], $result['status']);
            }

            if ($result['success']) {
                return redirect()
                    ->route('admin.scan.index', ['event_id' => $selectedEventId])
                    ->with('success', $result['message']);
            }

            return redirect()
                ->route('admin.scan.index', ['event_id' => $selectedEventId])
                ->with('error', $result['message']);

        } catch (Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan sistem saat memproses presensi.',
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan sistem saat memproses presensi.');
        }
    }
}
