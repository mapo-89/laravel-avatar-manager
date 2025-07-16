<?php 

namespace Mapo89\LaravelAvatarManager\Http\Controllers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Mapo89\LaravelAvatarManager\Contracts\UserProviderInterface;
use Mapo89\LaravelAvatarManager\Services\AvatarService;

class AvatarController
{
    protected $userProvider;
    protected $avatarService;

    public function __construct(UserProviderInterface $userProvider, AvatarService $avatarService)
    {
        $this->userProvider = $userProvider;
        $this->avatarService = $avatarService;
    }
    public function show($hash)
    {
        $avatarPath = $this->avatarService->findAvatarByHash($hash);
        
        if ($avatarPath) {
            return response()->file($this->avatarService->getAvatarFullPath($avatarPath));
        }

        $user = $this->userProvider->findByEmailHash($hash);
        if ($user && $user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            return Response::file(Storage::disk('public')->path($user->profile_photo_path));
        }

        $defaultPath = public_path(config('avatar-manager.default_avatar', 'vendor/avatar-manager/default.png'));
        return Response::file($defaultPath);
    }
}
