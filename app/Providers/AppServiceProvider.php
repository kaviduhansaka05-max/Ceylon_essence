<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// ✅ use the package’s provider (NOT App\Providers\…)
use MongoDB\Laravel\MongoDBServiceProvider as MongoServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Force-register the MongoDB provider (harmless if auto-discovered)
        $this->app->register(MongoServiceProvider::class);
    }

    public function boot(): void
    {
        //
    }
}
