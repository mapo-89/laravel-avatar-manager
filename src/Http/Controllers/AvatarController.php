<?php 

namespace Mapo89\LaravelAvatarManager\Http\Controllers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Mapo89\LaravelAvatarManager\Contracts\UserProviderInterface;

class AvatarController
{
    protected $userProvider;

    public function __construct(UserProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
    }
    public function show($hash)
    {
        $user = $this->userProvider->findByEmailHash($hash);

        if ($user && $user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            return Response::file(Storage::disk('public')->path($user->profile_photo_path));
        }

        $defaultPath = public_path(config('avatar-manager.default_avatar', 'vendor/avatar-manager/default.png'));
        return Response::file($defaultPath);
    }
}
