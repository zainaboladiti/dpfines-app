<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\GlobalFine;
use App\Models\ScrapedFine;
use App\Policies\GlobalFinePolicy;
use App\Policies\ScrapedFinePolicy;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     */
    protected $policies = [
        GlobalFine::class => GlobalFinePolicy::class,
        ScrapedFine::class => ScrapedFinePolicy::class,
    ];

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
        // Register policies
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }

        // Admin gate for quick checks
        Gate::define('admin', function ($user) {
            return $user->isAdmin();
        });
    }
}

