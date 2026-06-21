<?php

namespace mmerlijn\LaravelSalt;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use mmerlijn\LaravelSalt\Helpers\Distance;
use mmerlijn\LaravelSalt\Helpers\TimeArray;
use mmerlijn\LaravelSalt\Helpers\Toast;
use mmerlijn\LaravelSalt\Helpers\ToastInterface;
use mmerlijn\LaravelSalt\Jobs\FlowRunnerJob;
use mmerlijn\LaravelSalt\Jobs\PruneLocks;
use mmerlijn\LaravelSalt\Models\AppError;
use mmerlijn\LaravelSalt\Models\Flow;
use mmerlijn\LaravelSalt\Models\FlowExchange;
use mmerlijn\LaravelSalt\Models\FlowLog;
use mmerlijn\LaravelSalt\Models\FlowResponse;
use mmerlijn\LaravelSalt\Models\Requester;

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
            __DIR__ . '/../config/laravel_salt.php',
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
        Relation::morphMap([
            'flow-log' => FlowLog::class,
            'flow' => Flow::class,
            'app-error' => AppError::class,
            'requester' => Requester::class,
        ]);
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

        // We plannen dit alleen in als Laravel via de console/cron draait
        if ($this->app->runningInConsole()) {

            $this->app->booted(function () {
                $schedule = $this->app->make(Schedule::class);

                // Uitvoeren van FlowTasks
                $schedule->job(new FlowRunnerJob)->everyMinute();
                // Opschonen van de locks
                $schedule->job(new PruneLocks, 'low')->everyMinute();
            });

        }

    }
}

