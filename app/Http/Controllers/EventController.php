<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class EventController extends Controller
{
    /**
     * Menampilkan daftar event yang dipublikasikan dengan fitur pencarian.
     *
     * Keamanan SQL Injection:
     * Pencarian dilakukan menggunakan query builder Eloquent dengan parameter binding otomatis.
     * Input dari user tidak pernah digabungkan langsung secara mentah (raw string concatenation).
     */
    public function index(Request $request): View
    {
        $search = $request->input('q');

        $events = Event::query()
            ->where('status', 'published')
            ->when($search, function ($query, $search) {
                // Parameter binding Eloquent otomatis mengamankan input dari SQL Injection
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('title', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy('start_at', 'asc')
            ->paginate(9)
            ->withQueryString();

        return view('events.index', compact('events', 'search'));
    }

    /**
     * Menampilkan detail satu event.
     *
     * Keamanan Akses:
     * Menggunakan Gate / Policy untuk mencegah akses tidak sah ke event bertatus draft/cancelled.
     */
    public function show(Event $event): View
    {
        Gate::authorize('view', $event);

        return view('events.show', compact('event'));
    }
}
