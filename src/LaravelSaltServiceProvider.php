<?php

namespace mmerlijn\LaravelSalt;

use Illuminate\Support\ServiceProvider;
use mmerlijn\LaravelSalt\Helpers\Distance;
use mmerlijn\LaravelSalt\Helpers\TimeArray;
use mmerlijn\LaravelSalt\Helpers\Toast;
use mmerlijn\LaravelSalt\Helpers\ToastInterface;

class LaravelSaltServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind('distance', function ($app) {
            return new Distance();
        });

        $this->app->bind('timeArray', function ($app) {
            return new TimeArray();
        });
        $this->app->singleton(ToastInterface::class, function ($app) {
            return new Toast();
        });
        $this->mergeConfigFrom(
            __DIR__.'/../config/laravel_salt.php',
            'laravel_salt'
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/laravel-salt.php');

        $this->publishes([
            __DIR__ . '/../config/laravel_salt.php' => config_path('laravel_salt.php'),
        ], 'config');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'laravel-salt');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/laravel-salt'),
        ], 'views');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'migrations');
    }
}

