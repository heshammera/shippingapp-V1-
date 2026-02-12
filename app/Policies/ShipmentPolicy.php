<?php

namespace App\Policies;

use App\Models\Shipment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ShipmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('shipments.view_any');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Shipment $shipment): bool
    {
        return $user->can('shipments.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('shipments.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Shipment $shipment): bool
    {
        return $user->can('shipments.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Shipment $shipment): bool
    {
        return $user->can('shipments.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Shipment $shipment): bool
    {
        return $user->can('shipments.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Shipment $shipment): bool
    {
        return $user->can('shipments.force_delete');
    }

    /**
     * Additional permissions for advanced operations
     */
    public function export(User $user): bool
    {
        return $user->can('shipments.export_excel') || $user->can('shipments.export_pdf');
    }

    public function print(User $user): bool
    {
        return $user->can('shipments.print_invoices') 
            || $user->can('shipments.print_table') 
            || $user->can('shipments.print_thermal');
    }

    public function import(User $user): bool
    {
        return $user->can('shipments.import');
    }

    public function bulkDelete(User $user): bool
    {
        return $user->can('shipments.bulk_delete');
    }

    public function assignAgent(User $user): bool
    {
        return $user->can('shipments.assign_agent');
    }

    public function updateStatus(User $user): bool
    {
        return $user->can('shipments.update_status');
    }
}
