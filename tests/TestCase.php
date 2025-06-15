<?php

namespace Mapo89\LaravelAvatarManager\Tests;

use Mapo89\LaravelAvatarManager\AvatarManagerServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    /**
     * Load package alias
     * @param  \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            AvatarManagerServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // SQLite In-Memory-DB
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Avatar-Konfiguration
        $app['config']->set('avatar-manager.default_avatar', 'vendor/avatar-manager/default.png');
    }
}
