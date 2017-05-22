<?php

namespace Sjdaws\LoyaltyCorpTest\Providers;

use Illuminate\Support\ServiceProvider;

class TestServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // Register required package components
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'loyaltycorp-test');
        $this->publishes([
            __DIR__ . '/../../public/loyaltycorp-test' => public_path('loyaltycorp-test'),
            __DIR__ . '/../../resources/assets/sass/font-awesome/fonts' => public_path('fonts'),
        ], 'public');
    }
}
