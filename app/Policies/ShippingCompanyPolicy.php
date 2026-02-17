<?php

namespace App\Policies;

use App\Models\ShippingCompany;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ShippingCompanyPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin') || $user->can('shipping_companies.view_any');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ShippingCompany $shippingCompany): bool
    {
        return $user->hasRole('admin') || $user->can('shipping_companies.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->can('shipping_companies.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ShippingCompany $shippingCompany): bool
    {
        return $user->hasRole('admin') || $user->can('shipping_companies.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ShippingCompany $shippingCompany): bool
    {
        return $user->hasRole('admin') || $user->can('shipping_companies.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ShippingCompany $shippingCompany): bool
    {
        return $user->hasRole('admin') || $user->can('shipping_companies.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ShippingCompany $shippingCompany): bool
    {
        return $user->hasRole('admin') || $user->can('shipping_companies.force_delete');
    }
}
