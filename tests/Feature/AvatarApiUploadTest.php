<?php 

namespace Mapo89\LaravelAvatarManager\Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Orchestra\Testbench\TestCase;

class AvatarApiUploadTest extends TestCase
{
    public function test_avatar_upload_with_valid_api_key()
    {
        Storage::fake('public');

        $response = $this->postJson('/api/avatars/upload', [
            'email' => 'user@example.com',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ], [
            'X-API-KEY' => config('avatar-manager.api_keys')[0],
        ]);

        $response->assertStatus(200);
        Storage::disk('public')->assertExists('avatars/' . md5('user@example.com') . '.jpg');
    }

    public function test_avatar_upload_fails_with_invalid_api_key()
    {
        $response = $this->postJson('/api/avatars/upload', [
            'email' => 'user@example.com',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ], [
            'X-API-KEY' => 'invalid-key',
        ]);

        $response->assertStatus(401);
    }
}
