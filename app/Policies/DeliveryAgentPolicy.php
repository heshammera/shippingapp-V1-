<?php

namespace App\Policies;

use App\Models\DeliveryAgent;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DeliveryAgentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin') || $user->can('delivery_agents.view_any');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DeliveryAgent $deliveryAgent): bool
    {
        return $user->can('delivery_agents.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('delivery_agents.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DeliveryAgent $deliveryAgent): bool
    {
        return $user->can('delivery_agents.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DeliveryAgent $deliveryAgent): bool
    {
        return $user->can('delivery_agents.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DeliveryAgent $deliveryAgent): bool
    {
        return $user->can('delivery_agents.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DeliveryAgent $deliveryAgent): bool
    {
        return $user->can('delivery_agents.force_delete');
    }
}
