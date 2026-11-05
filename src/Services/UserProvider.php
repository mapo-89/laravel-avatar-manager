<?php

namespace Mapo89\LaravelAvatarManager\Services;

use Mapo89\LaravelAvatarManager\Contracts\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    public function findByEmailHash(string $hash)
    {
        if (! preg_match('/\A[a-f0-9]{32}\z/i', $hash)) {
            return null;
        }

        $model = config('avatar-manager.user_model') ?? config('auth.providers.users.model');

        // A cursor bounds memory use and respects the configured model's scopes.
        return $model::query()->cursor()->first(function ($user) use ($hash) {
            return md5(strtolower(trim($user->email))) === strtolower($hash);
        });
    }
}
