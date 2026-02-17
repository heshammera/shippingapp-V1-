<?php

namespace App\Policies;

use App\Models\ShipmentStatus;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ShipmentStatusPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin') || $user->can('shipment_statuses.view_any');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ShipmentStatus $shipmentStatus): bool
    {
        return $user->can('statuses.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('statuses.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ShipmentStatus $shipmentStatus): bool
    {
        return $user->can('statuses.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ShipmentStatus $shipmentStatus): bool
    {
        return $user->can('statuses.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ShipmentStatus $shipmentStatus): bool
    {
        return $user->can('statuses.restore'); // Assuming default or create explicit permission if needed
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ShipmentStatus $shipmentStatus): bool
    {
        return $user->can('statuses.force_delete'); // Assuming default or create explicit permission if needed
    }
}
