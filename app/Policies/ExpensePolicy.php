<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ExpensePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin') || $user->can('expenses.view_any');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Expense $expense): bool
    {
        return $user->hasRole('admin') || $user->can('expenses.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->can('expenses.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Expense $expense): bool
    {
        return $user->hasRole('admin') || $user->can('expenses.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Expense $expense): bool
    {
        return $user->hasRole('admin') || $user->can('expenses.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Expense $expense): bool
    {
        return $user->hasRole('admin') || $user->can('expenses.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Expense $expense): bool
    {
        return $user->hasRole('admin') || $user->can('expenses.force_delete');
    }
}
