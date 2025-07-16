<?php 

namespace Mapo89\LaravelAvatarManager\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'avatar' => 'required|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $hash = md5(strtolower(trim($request->input('email'))));
        $path = "avatars/{$hash}.jpg";

        Storage::disk('public')->put($path, file_get_contents($request->file('avatar')));

        return response()->json(['message' => 'Avatar uploaded successfully']);
    }
}
