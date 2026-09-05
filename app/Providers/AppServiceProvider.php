<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Sentinel;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        /**
         * -------------------------------------------------
         * Global Role Variables (Sentinel)
         * -------------------------------------------------
         * Available in ALL Blade views
         * -------------------------------------------------
         */
        View::composer('*', function ($view) {

            $user = Sentinel::getUser();
            $role = $user?->roles()->first();

            $view->with([
                'currentUser'      => $user,
                'currentRole'      => $role,
                'currentRoleSlug'  => $role?->slug,
                'currentRoleName'  => $role?->name,
            ]);
        });
    }
}
