<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use MongoDB\Laravel\MongoDBServiceProvider as MongoServiceProvider;

// 👇 Add this line
use Livewire\Livewire;
use App\Http\Livewire\HotSellers;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Force-register the MongoDB provider (harmless if auto-discovered)
        $this->app->register(MongoServiceProvider::class);
    }

    public function boot(): void
    {
   
    }
}
