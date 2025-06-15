<?php


namespace Mapo89\LaravelAvatarManager\Tests\Unit;

use Mapo89\LaravelAvatarManager\AvatarManagerServiceProvider;
use Mapo89\LaravelAvatarManager\Tests\TestCase;

class ServiceProviderTest extends TestCase
{
    public function test_service_provider_is_loaded()
    {
        $this->assertTrue(
            $this->app->providerIsLoaded(AvatarManagerServiceProvider::class),
            'AvatarManagerServiceProvider is not loaded'
        );
    }
}
