<?php

namespace Mapo89\LaravelAvatarManager\Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mapo89\LaravelAvatarManager\Tests\Models\User;
use Mapo89\LaravelAvatarManager\Tests\TestCase;

class AvatarApiUploadTest extends TestCase
{
    public function test_avatar_upload_with_valid_api_key(): void
    {
        $this->withHeader('X-API-KEY', 'test-api-key')->postJson('/api/avatars/upload', [
            'email' => 'USER@example.com',
            'avatar' => UploadedFile::fake()->image('avatar.png'),
        ])->assertOk();

        Storage::disk('public')->assertExists('avatars/'.md5('user@example.com').'.png');
        $this->get('/avatar/'.md5('user@example.com'))->assertOk()->assertHeader('content-type', 'image/png');
    }

    public function test_avatar_upload_fails_without_valid_api_key(): void
    {
        foreach ([null, '', 'invalid-key', '0', '1'] as $key) {
            $headers = $key === null ? [] : ['X-API-KEY' => $key];
            $this->postJson('/api/avatars/upload', [], $headers)->assertUnauthorized();
        }
    }

    public function test_unconfigured_keys_cannot_authenticate(): void
    {
        config(['avatar-manager.api_keys' => [null, '', true, 1]]);

        $this->withHeader('X-API-KEY', '1')->postJson('/api/avatars/upload', [])->assertUnauthorized();
    }

    public function test_avatar_upload_fails_if_user_has_existing_profile_photo(): void
    {
        User::create(['email' => 'user@example.com', 'profile_photo_path' => 'existing.jpg']);
        Storage::disk('public')->put('existing.jpg', 'original');

        $this->withHeader('X-API-KEY', 'test-api-key')->postJson('/api/avatars/upload', [
            'email' => 'user@example.com', 'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ])->assertStatus(409)->assertJsonFragment(['message' => 'User already has a profile photo']);

        Storage::disk('public')->assertMissing('avatars/'.md5('user@example.com').'.jpg');
        $this->assertSame('original', Storage::disk('public')->get('existing.jpg'));
    }

    public function test_invalid_email_and_non_image_are_rejected(): void
    {
        $this->withHeader('X-API-KEY', 'test-api-key')->postJson('/api/avatars/upload', [
            'email' => 'invalid', 'avatar' => UploadedFile::fake()->create('avatar.txt', 1, 'text/plain'),
        ])->assertUnprocessable()->assertJsonValidationErrors(['email', 'avatar']);
    }

    public function test_svg_and_oversized_images_are_rejected(): void
    {
        $this->withHeader('X-API-KEY', 'test-api-key');
        foreach ([
            UploadedFile::fake()->image('avatar.jpg')->size(2049),
            UploadedFile::fake()->createWithContent('avatar.svg', '<svg xmlns="http://www.w3.org/2000/svg"><rect width="10" height="10"/></svg>'),
        ] as $image) {
            $this->postJson('/api/avatars/upload', ['email' => 'user@example.com', 'avatar' => $image])
                ->assertUnprocessable()->assertJsonValidationErrors('avatar');
        }
    }

    public function test_replacing_an_image_removes_stale_extensions(): void
    {
        $hash = md5('user@example.com');
        Storage::disk('public')->put('avatars/'.$hash.'.jpg', 'old');

        $this->withHeader('X-API-KEY', 'test-api-key')->postJson('/api/avatars/upload', [
            'email' => 'user@example.com', 'avatar' => UploadedFile::fake()->image('new.png'),
        ])->assertOk();

        Storage::disk('public')->assertExists('avatars/'.$hash.'.png');
        Storage::disk('public')->assertMissing('avatars/'.$hash.'.jpg');
    }
}
