<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        Gate::define('role-admin', fn ($user): bool => $user->role->isAdmin());
        Gate::define('role-member', fn ($user): bool => $user->role->isMember());

        if($this->app->environment('production')) {
            \URL::forceScheme('https');
        }
    }
}
