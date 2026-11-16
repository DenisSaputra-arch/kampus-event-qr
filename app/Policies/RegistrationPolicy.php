<?php

namespace App\Policies;

use App\Models\Registration;
use App\Models\User;

class RegistrationPolicy
{
    /**
     * Melihat daftar tiket:
     * Siapa pun yang login boleh melihat daftar tiket miliknya sendiri.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Keamanan Anti-IDOR:
     * Tiket QR dan detail pendaftaran HANYA boleh dilihat oleh pemilik tiket itu sendiri
     * atau oleh administrator.
     */
    public function view(User $user, Registration $registration): bool
    {
        return $user->isAdmin() || $registration->user_id === $user->id;
    }

    /**
     * Keamanan Anti-IDOR:
     * Tiket pendaftaran HANYA boleh dibatalkan oleh pemilik tiket itu sendiri
     * atau oleh administrator.
     */
    public function delete(User $user, Registration $registration): bool
    {
        return $user->isAdmin() || $registration->user_id === $user->id;
    }
}
