<?php

namespace Mapo89\LaravelAvatarManager;

use Illuminate\Support\ServiceProvider;
use Mapo89\LaravelAvatarManager\Contracts\ProfilePhotoProviderInterface;
use Mapo89\LaravelAvatarManager\Contracts\UserProviderInterface;
use Mapo89\LaravelAvatarManager\Http\Middleware\ApiKeyAuth;
use Mapo89\LaravelAvatarManager\Services\ProfilePhotoProvider;
use Mapo89\LaravelAvatarManager\Services\UserProvider;

class AvatarManagerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app['router']->aliasMiddleware('avatar-manager.api_key', ApiKeyAuth::class);
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/routes/api.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('avatar-manager.php'),
            ], 'avatar-manager-config');

            $this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/avatar-manager'),
            ], 'avatar-manager-assets');
        }
    }

    public function register(): void
    {
        $this->app->bind(UserProviderInterface::class, UserProvider::class);
        $this->app->bind(ProfilePhotoProviderInterface::class, ProfilePhotoProvider::class);
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'avatar-manager');
        $this->app->singleton('avatar-manager', fn () => new AvatarManager);
    }
}
