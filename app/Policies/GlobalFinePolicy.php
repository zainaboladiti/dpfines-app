<?php

namespace App\Policies;

use App\Models\User;
use App\Models\GlobalFine;

class GlobalFinePolicy
{
    /**
     * Only admins can view fine listings
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Only admins can view a specific fine
     */
    public function view(User $user, GlobalFine $fine): bool
    {
        return $user->isAdmin();
    }

    /**
     * Only admins can create fines
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Only admins can update fines
     */
    public function update(User $user, GlobalFine $fine): bool
    {
        return $user->isAdmin();
    }

    /**
     * Only admins can delete fines
     */
    public function delete(User $user, GlobalFine $fine): bool
    {
        return $user->isAdmin();
    }
}
