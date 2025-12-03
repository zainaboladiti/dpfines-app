<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ScrapedFine;

class ScrapedFinePolicy
{
    /**
     * Only admins can view listings
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Only admins can view a specific fine
     */
    public function view(User $user, ScrapedFine $fine): bool
    {
        return $user->isAdmin();
    }

    /**
     * Only admins can create (submit) fines
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Only admins can review (update status) fines
     */
    public function review(User $user, ScrapedFine $fine): bool
    {
        return $user->isAdmin() && $fine->status === 'pending';
    }

    /**
     * Only admins can delete fines
     */
    public function delete(User $user, ScrapedFine $fine): bool
    {
        return $user->isAdmin();
    }

    /**
     * Only admins who submitted the fine can view it for editing
     */
    public function restore(User $user, ScrapedFine $fine): bool
    {
        return $user->isAdmin() && $user->id === $fine->submitted_by;
    }
}
