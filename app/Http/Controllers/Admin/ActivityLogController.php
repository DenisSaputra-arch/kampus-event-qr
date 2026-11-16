<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller untuk menampilkan log aktivitas pengguna kepada admin.
 * Hanya dapat diakses oleh admin (dilindungi route middleware).
 */
class ActivityLogController extends Controller
{
    /**
     * Menampilkan daftar log aktivitas, diurutkan terbaru.
     */
    public function index(Request $request): View
    {
        $search = $request->input('q');

        $logs = ActivityLog::with('user')
            ->when($search, function ($query) use ($search) {
                // Pencarian aman menggunakan parameter binding Eloquent
                $query->where('action', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(50)
            ->withQueryString();

        return view('admin.activity-logs.index', compact('logs', 'search'));
    }
}
