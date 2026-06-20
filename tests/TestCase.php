<?php

namespace mmerlijn\LaravelSalt\Tests;

use mmerlijn\LaravelSalt\LaravelSaltServiceProvider;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    use WithWorkbench;
    protected function getPackageProviders($app): array
    {
        return [LaravelSaltServiceProvider::class];
    }

    protected function defineDatabaseMigrations(): void
    {
        // Dit commando zorgt ervoor dat de standaard Laravel migraties
        // (zoals users, sessions, jobs, etc.) worden ingeladen.
        $this->loadLaravelMigrations();

        // Als je package eigen migraties heeft, laad je die hier ook meteen:
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate:fresh', [
            '--path' => realpath(__DIR__ . '/../database/migrations'),
            '--realpath' => true,
        ]);
        $this->artisan('migrate', [
            '--path' => realpath(__DIR__ . '/../workbench/database/migrations/testMigrations'),
            '--realpath' => true,
        ]);
    }
}
