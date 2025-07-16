<?php 

namespace Mapo89\LaravelAvatarManager\Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Mapo89\LaravelAvatarManager\AvatarManagerServiceProvider;
use Orchestra\Testbench\TestCase;

class AvatarApiUploadTest extends TestCase
{
     protected function getPackageProviders($app)
    {
        return [
            AvatarManagerServiceProvider::class,
        ];
    }
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        Config::set('avatar-manager.api_keys', ['test-api-key']);
    }
    public function test_avatar_upload_with_valid_api_key()
    {
         $response = $this->withHeaders([
            'X-API-KEY' => 'test-api-key',
        ])->postJson('/api/avatars/upload', [
            'email' => 'user@example.com',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
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
