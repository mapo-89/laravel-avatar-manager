<?php 

namespace Mapo89\LaravelAvatarManager\Contracts;

interface UserProviderInterface
{
    public function findByEmailHash(string $hash);
}
