<?php

namespace Innoflash\SteroidSeeder;

use Illuminate\Support\ServiceProvider;

class SteroidSeederServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('steroid-seeder.php'),
            ], 'steroid-seeder-config');
        }

        $this->loadFactoriesFrom(config('steroid-seeder.factories-paths'));
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'steroid-seeder');

        // Register the main class to use with the facade
        $this->app->singleton('steroid-seeder', function () {
            return new SteroidSeederManager;
        });
    }
}
