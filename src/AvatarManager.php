<?php

namespace Mapo89\LaravelAvatarManager;

class AvatarManager
{
    public function hash(string $email): string
    {
        return md5(strtolower(trim($email)));
    }

    public function url(string $email): string
    {
        return route('avatar.show', ['hash' => $this->hash($email)]);
    }
}
