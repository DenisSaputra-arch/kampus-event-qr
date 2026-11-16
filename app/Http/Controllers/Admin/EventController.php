<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class EventController extends Controller
{
    /**
     * Menampilkan daftar semua event untuk admin.
     *
     * Keamanan Query:
     * Filter dan pencarian aman dengan Eloquent parameter binding.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Event::class);

        $search = $request->input('q');
        $status = $request->input('status');

        $events = Event::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('title', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderBy('start_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.events.index', compact('events', 'search', 'status'));
    }

    /**
     * Menampilkan form pembuatan event baru.
     */
    public function create(): View
    {
        Gate::authorize('create', Event::class);

        return view('admin.events.create');
    }

    /**
     * Menyimpan event baru ke database.
     *
     * Keamanan:
     * - Validasi ketat menggunakan StoreEventRequest (Form Request).
     * - Hanya data tervalidasi ($request->validated()) yang disimpan untuk mencegah Mass Assignment.
     */
    public function store(StoreEventRequest $request): RedirectResponse
    {
        $event = Event::create($request->validated());

        // Catat pembuatan event (audit trail)
        ActivityLogger::log('event_created: event_id=' . $event->id, $request->user());

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event "' . $event->title . '" berhasil dibuat.');
    }

    /**
     * Menampilkan detail event khusus admin.
     */
    public function show(Event $event): View
    {
        Gate::authorize('view', $event);

        return view('admin.events.show', compact('event'));
    }

    /**
     * Menampilkan form edit event.
     *
     * Keamanan IDOR:
     * Diproteksi oleh Policy untuk memastikan hanya user berhak yang dapat membuka form.
     */
    public function edit(Event $event): View
    {
        Gate::authorize('update', $event);

        return view('admin.events.edit', compact('event'));
    }

    /**
     * Memperbarui data event di database.
     *
     * Keamanan:
     * - Validasi ketat via UpdateEventRequest.
     * - Otorisasi via Policy (mencegah IDOR).
     */
    public function update(UpdateEventRequest $request, Event $event): RedirectResponse
    {
        Gate::authorize('update', $event);

        $event->update($request->validated());

        // Catat pembaruan event (audit trail)
        ActivityLogger::log('event_updated: event_id=' . $event->id, $request->user());

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event "' . $event->title . '" berhasil diperbarui.');
    }

    /**
     * Menghapus event dari database.
     *
     * Keamanan IDOR:
     * Diproteksi oleh Gate/Policy authorize('delete', $event).
     */
    public function destroy(Event $event): RedirectResponse
    {
        Gate::authorize('delete', $event);

        $title = $event->title;
        $eventId = $event->id;

        $event->delete();

        // Catat penghapusan event (audit trail)
        ActivityLogger::log('event_deleted: event_id=' . $eventId, request()->user());

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event "' . $title . '" berhasil dihapus.');
    }
}
