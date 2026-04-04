<?php

namespace App\Providers;

use App\Models\Department;
use App\Observers\System\NestedObserver;
use Illuminate\Support\ServiceProvider;

class ObserveServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }


    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Department::observe(NestedObserver::class);
    }
}
