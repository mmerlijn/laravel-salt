<?php

namespace mmerlijn\LaravelSalt\Tests;

use mmerlijn\LaravelSalt\LaravelSaltServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [LaravelSaltServiceProvider::class];
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

        $this->artisan('migrate', [
            '--path'     => realpath(__DIR__ . '/../database/migrations'),
            '--realpath' => true,
        ]);
        $this->artisan('migrate', [
            '--path'     => realpath(__DIR__ . '/../database/testMigrations'),
            '--realpath' => true,
        ]);
    }
}
