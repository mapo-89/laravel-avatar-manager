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
        $disk = Storage::disk('public');
        $basePath = 'avatars/' . $hash;

        // Supported formats, in order of priority
        $extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        // 1. Dynamically find the existing file
        foreach ($extensions as $ext) {
            $fullPath = $basePath . '.' . $ext;
            if ($disk->exists($fullPath)) {
                return Response::file($disk->path($fullPath));
            }
        }

         // 2. Fallback to user profile photo
        $user = $this->userProvider->findByEmailHash($hash);
        if ($user && $user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            return Response::file(Storage::disk('public')->path($user->profile_photo_path));
        }

        // 3. Default avatar
        $defaultPath = public_path(config('avatar-manager.default_avatar', 'vendor/avatar-manager/default.png'));
        return Response::file($defaultPath);
    }
}
