<?php

namespace Mapo89\LaravelAvatarManager\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Mapo89\LaravelAvatarManager\Contracts\ProfilePhotoProviderInterface;
use Mapo89\LaravelAvatarManager\Contracts\UserProviderInterface;
use Mapo89\LaravelAvatarManager\Services\AvatarService;

class AvatarController
{
    public function __construct(
        protected UserProviderInterface $userProvider,
        protected AvatarService $avatarService,
        protected ProfilePhotoProviderInterface $profilePhotos,
    ) {}

    public function show(string $hash)
    {
        $hash = strtolower($hash);
        $user = $this->userProvider->findByEmailHash($hash);
        $photo = $user ? $this->profilePhotos->findForUser($user) : null;

        // A managed profile image takes precedence over an earlier API upload.
        if ($photo !== null) {
            return Storage::disk($photo['disk'])->response($photo['path'], null, [
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        $path = $this->avatarService->findAvatarByHash($hash);

        if ($path !== null) {
            return Storage::disk(config('avatar-manager.disk', 'public'))->response($path, null, [
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        $default = public_path(config('avatar-manager.default_avatar', 'vendor/avatar-manager/default.png'));

        // Fresh installations work before publishing the optional asset override.
        if (! is_file($default)) {
            $default = dirname(__DIR__, 3).'/resources/assets/default.png';
        }

        return response()->file($default, ['X-Content-Type-Options' => 'nosniff']);
    }
}
