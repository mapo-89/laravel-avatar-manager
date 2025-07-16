<?php 

namespace Mapo89\LaravelAvatarManager\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Mapo89\LaravelAvatarManager\Contracts\UserProviderInterface;

class UploadController extends Controller
{
    protected $userProvider;
    public function __construct(UserProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
    }
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'avatar' => 'required|image|max:20480',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $hash = md5(strtolower(trim($request->input('email'))));
        $path = "avatars/{$hash}.jpg";

        $user = $this->userProvider->findByEmailHash($hash);
        if ($user && $user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
             return response()->json([
                'message' => 'User already has a profile photo'
            ], 409);
        }

        Storage::disk('public')->put($path, file_get_contents($request->file('avatar')));

        return response()->json(['message' => 'Avatar uploaded successfully']);
    }
}
