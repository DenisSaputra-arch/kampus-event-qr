<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    /**
     * Siapa saja (termasuk tamu) boleh melihat daftar event.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Keamanan Akses:
     * - Event published dapat dilihat oleh siapa saja.
     * - Event dengan status draft atau cancelled hanya dapat dilihat oleh admin.
     */
    public function view(?User $user, Event $event): bool
    {
        if ($event->status === 'published') {
            return true;
        }

        return $user !== null && $user->isAdmin();
    }

    /**
     * Hanya admin yang berhak membuat event baru.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Keamanan IDOR:
     * Hanya admin yang berhak mengubah event apapun.
     */
    public function update(User $user, Event $event): bool
    {
        return $user->isAdmin();
    }

    /**
     * Keamanan IDOR:
     * Hanya admin yang berhak menghapus event.
     */
    public function delete(User $user, Event $event): bool
    {
        return $user->isAdmin();
    }
}
