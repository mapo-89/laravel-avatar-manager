<?php

namespace Mapo89\LaravelAvatarManager;

use Illuminate\Support\ServiceProvider;
use Mapo89\LaravelAvatarManager\Contracts\UserProviderInterface;
use Mapo89\LaravelAvatarManager\Http\Middleware\ApiKeyAuth;
use Mapo89\LaravelAvatarManager\Services\UserProvider;

class AvatarManagerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'avatar-manager');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'avatar-manager');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');

        $this->app['router']->aliasMiddleware('avatar-manager.api_key', ApiKeyAuth::class);

        if ($this->app->runningInConsole()) {
             // Publishing config.
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('avatar-manager.php'),
            ], 'avatar-manager-config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/avatar-manager'),
            ], 'views');*/

            // Publishing assets.
            $this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/avatar-manager'),
            ], 'avatar-manager-assets');

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/avatar-manager'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        //Service provider binds the interface
        $this->app->bind(
            UserProviderInterface::class,
            UserProvider::class
        );
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'avatar-manager');

        // Register the main class to use with the facade
        $this->app->singleton('avatar-manager', function () {
            return new LaravelAvatarManager;
        });
    }
}
