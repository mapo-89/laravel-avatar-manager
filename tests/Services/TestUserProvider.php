<?php 

namespace Mapo89\LaravelAvatarManager\Tests\Services;

use Mapo89\LaravelAvatarManager\Contracts\UserProviderInterface;
use Mapo89\LaravelAvatarManager\Tests\Models\User;

class TestUserProvider implements UserProviderInterface
{
    public function findByEmailHash(string $hash)
    {
        return User::all()->first(function($user) use ($hash) {
            return md5(strtolower(trim($user->email))) === $hash;
        });
    }
}
