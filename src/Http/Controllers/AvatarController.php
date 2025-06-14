<?php 

namespace Mapo89\LaravelAvatarManager\Http\Controllers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class AvatarController
{
    public function show($hash)
    {
        $user = User::all()->first(function ($user) use ($hash) {
            return md5(strtolower(trim($user->email))) === $hash;
        });

        if ($user && $user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            return Response::file(Storage::disk('public')->path($user->profile_photo_path));
        }

        $defaultPath = public_path(config('avatar-manager.default_avatar', 'vendor/avatar-manager/default.png'));
        return Response::file($defaultPath);
    }
}
