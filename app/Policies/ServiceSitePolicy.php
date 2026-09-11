<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ServiceSite;
class ServiceSitePolicy
{
    /**
     * Create a new policy instance.
     */

    public function isAdmin(User $user): bool
    {
        return in_array($user->email, config('admin.emails', []) );
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ServiceSite $ServiceSite): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ServiceSite $ServiceSite): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ServiceSite $ServiceSite): bool
    {
        return false; //$this->isAdmin($user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ServiceSite $ServiceSite): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ServiceSite $ServiceSite): bool
    {
        return false;
    }

}
