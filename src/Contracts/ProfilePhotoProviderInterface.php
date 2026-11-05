<?php

namespace Mapo89\LaravelAvatarManager\Contracts;

interface ProfilePhotoProviderInterface
{
    /** @return array{disk: string, path: string}|null */
    public function findForUser(object $user): ?array;
}
