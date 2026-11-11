<?php

namespace App\Policies;

use App\Models\Denda;
use App\Models\User;

class DendaPolicy
{
    /**
     * Pelunasan denda dicatat admin di kasir.
     */
    public function lunaskan(User $user, Denda $denda): bool
    {
        return $user->punyaRole('admin');
    }
}
