<?php

namespace Mapo89\LaravelAvatarManager\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Mapo89\LaravelAvatarManager\AvatarManagerServiceProvider;
use Mapo89\LaravelAvatarManager\Tests\Models\User;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;

class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [AvatarManagerServiceProvider::class, MediaLibraryServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]);
        $app['config']->set('auth.providers.users.model', User::class);
        $app['config']->set('avatar-manager.api_keys', ['test-api-key']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('profile_photo_path')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Storage::fake('public');
    }
}
