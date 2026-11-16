<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Menampilkan rekapitulasi data pendaftar vs kehadiran untuk setiap event.
     */
    public function index(Request $request): View
    {
        $search = $request->query('q');

        $events = Event::query()
            ->when($search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            })
            ->withCount([
                // Hitung peserta terdaftar aktif
                'registrations as registered_count' => function ($q) {
                    $q->where('status', 'registered');
                },
                // Hitung peserta yang hadir (presensi sudah tercatat di attendances)
                'registrations as attended_count' => function ($q) {
                    $q->where('status', 'registered')
                        ->whereHas('attendance');
                },
                // Hitung peserta yang membatalkan tiket
                'registrations as cancelled_count' => function ($q) {
                    $q->where('status', 'cancelled');
                },
            ])
            ->orderBy('start_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.reports.index', compact('events', 'search'));
    }

    /**
     * Menampilkan detail kehadiran peserta untuk satu event spesifik.
     */
    public function show(Event $event): View
    {
        $registrations = Registration::where('event_id', $event->id)
            ->with(['user', 'attendance.scanner'])
            ->orderByRaw("status = 'registered' DESC")
            ->orderBy('created_at', 'asc')
            ->paginate(25);

        $totalRegistered = Registration::where('event_id', $event->id)->where('status', 'registered')->count();
        $totalAttended = Registration::where('event_id', $event->id)->where('status', 'registered')->whereHas('attendance')->count();
        $totalCancelled = Registration::where('event_id', $event->id)->where('status', 'cancelled')->count();

        return view('admin.reports.show', compact('event', 'registrations', 'totalRegistered', 'totalAttended', 'totalCancelled'));
    }

    /**
     * Mengekspor laporan presensi peserta per event ke format CSV.
     *
     * Keamanan CSV (CWE-1236: CSV / Formula Injection Mitigation):
     * Karakter awalan berbahaya (=, +, -, @, tab, newline) yang berpotensi dieksekusi
     * oleh aplikasi spreadsheet seperti Microsoft Excel atau LibreOffice Calc dinetralkan
     * dengan menyisipkan karakter petik tunggal (') di awal data.
     */
    public function exportCsv(Event $event): StreamedResponse
    {
        $filename = 'laporan-kehadiran-' . str()->slug($event->title) . '-' . now()->format('Ymd-His') . '.csv';

        $registrations = Registration::where('event_id', $event->id)
            ->with(['user', 'attendance.scanner'])
            ->orderBy('created_at', 'asc')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($event, $registrations) {
            $output = fopen('php://output', 'w');

            // Tambahkan BOM (Byte Order Mark) UTF-8 agar karakter aksen/Unicode terbaca rapi di Excel
            fputs($output, "\xEF\xBB\xBF");

            // Header Informasi Event
            fputcsv($output, [$this->sanitizeField('LAPORAN KEHADIRAN EVENT')]);
            fputcsv($output, [$this->sanitizeField('Judul Event'), $this->sanitizeField($event->title)]);
            fputcsv($output, [$this->sanitizeField('Waktu Pelaksanaan'), $this->sanitizeField($event->start_at->format('d/m/Y H:i') . ' WIB')]);
            fputcsv($output, [$this->sanitizeField('Lokasi'), $this->sanitizeField($event->location)]);
            fputcsv($output, [$this->sanitizeField('Kuota Maksimal'), $this->sanitizeField($event->quota)]);
            fputcsv($output, []); // Baris kosong

            // Header Tabel Kolom Data
            fputcsv($output, [
                $this->sanitizeField('No'),
                $this->sanitizeField('Nama Peserta'),
                $this->sanitizeField('Email'),
                $this->sanitizeField('Status Pendaftaran'),
                $this->sanitizeField('Status Kehadiran'),
                $this->sanitizeField('Waktu Presensi'),
                $this->sanitizeField('Dipindai Oleh'),
            ]);

            // Isi Data Baris Peserta
            $no = 1;
            foreach ($registrations as $reg) {
                $statusHadir = 'Belum Hadir';
                $waktuPresensi = '-';
                $petugasScan = '-';

                if ($reg->status === 'cancelled') {
                    $statusHadir = 'Batal';
                } elseif ($reg->attendance) {
                    $statusHadir = 'Hadir';
                    $waktuPresensi = $reg->attendance->scanned_at->format('d/m/Y H:i:s') . ' WIB';
                    $petugasScan = $reg->attendance->scanner?->name ?? 'Admin';
                }

                fputcsv($output, [
                    $this->sanitizeField($no++),
                    $this->sanitizeField($reg->user->name),
                    $this->sanitizeField($reg->user->email),
                    $this->sanitizeField($reg->status === 'registered' ? 'Terdaftar' : 'Dibatalkan'),
                    $this->sanitizeField($statusHadir),
                    $this->sanitizeField($waktuPresensi),
                    $this->sanitizeField($petugasScan),
                ]);
            }

            fclose($output);
        }, 200, $headers);
    }

    /**
     * Sanitasi Field untuk Mencegah Serangan CSV Formula Injection (OWASP Compliance).
     *
     * Aturan:
     * Jika string diawali dengan karakter formula: '=', '+', '-', '@', '\t', '\r'
     * tambahkan prefix tanda kutip tunggal (') agar spreadsheet memprosesnya sebagai teks murni (literal string).
     */
    protected function sanitizeField(mixed $value): string
    {
        $str = (string) $value;

        // Cek awalan formula berbahaya
        if (preg_match('/^[\=\+\-\@\t\r]/', $str)) {
            // Netralkan dengan menambahkan petik tunggal di depan formula
            return "'" . $str;
        }

        return $str;
    }
}
