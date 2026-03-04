<?php

namespace App\Providers;

use App\Models\Activity;
use App\Models\Module;
use App\Observers\ActivityObserver;
use App\Observers\ModuleObserver;
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
        // Register model observers for automatic skill syncing
        Activity::observe(ActivityObserver::class);
        Module::observe(ModuleObserver::class);
    }
}
