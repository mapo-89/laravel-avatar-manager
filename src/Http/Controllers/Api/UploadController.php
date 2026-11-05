<?php

namespace Mapo89\LaravelAvatarManager\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Mapo89\LaravelAvatarManager\Contracts\ProfilePhotoProviderInterface;
use Mapo89\LaravelAvatarManager\Contracts\UserProviderInterface;
use Mapo89\LaravelAvatarManager\Services\AvatarService;

class UploadController extends Controller
{
    public function __construct(
        protected UserProviderInterface $userProvider,
        protected ProfilePhotoProviderInterface $profilePhotos,
        protected AvatarService $avatars,
    ) {}

    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $email = strtolower(trim($request->input('email')));
        $user = $this->userProvider->findByEmailHash(md5($email));

        if ($user && $this->profilePhotos->findForUser($user) !== null) {
            return response()->json(['message' => 'User already has a profile photo'], 409);
        }

        $this->avatars->store($email, $request->file('avatar'));

        return response()->json(['message' => 'Avatar uploaded successfully']);
    }
}
