<?php 

namespace Mapo89\LaravelAvatarManager\Services;

use App\Models\User;
use Mapo89\LaravelAvatarManager\Contracts\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    public function findByEmailHash(string $hash)
    {
        return User::all()->first(function($user) use ($hash) {
            return md5(strtolower(trim($user->email))) === $hash;
        });
    }
}
