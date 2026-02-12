<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\Shipment::class => \App\Policies\ShipmentPolicy::class,
        \App\Models\Product::class => \App\Policies\ProductPolicy::class,
        \App\Models\User::class => \App\Policies\UserPolicy::class,
        \App\Models\ShippingCompany::class => \App\Policies\ShippingCompanyPolicy::class,
        \App\Models\DeliveryAgent::class => \App\Policies\DeliveryAgentPolicy::class,
        \App\Models\Collection::class => \App\Policies\CollectionPolicy::class,
        \App\Models\Expense::class => \App\Policies\ExpensePolicy::class,
        \App\Models\Inventory::class => \App\Policies\InventoryPolicy::class,
        \App\Models\ShipmentStatus::class => \App\Policies\ShipmentStatusPolicy::class,
        \Spatie\Permission\Models\Role::class => \App\Policies\RolePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Implicitly grant "Super Admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });
    }
}
