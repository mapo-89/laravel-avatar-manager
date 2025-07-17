<?php

namespace Mapo89\LaravelAvatarManager\Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Mapo89\LaravelAvatarManager\AvatarManagerServiceProvider;
use Mapo89\LaravelAvatarManager\Tests\Services\TestUserProvider;
use Orchestra\Testbench\TestCase;
use Mapo89\LaravelAvatarManager\Contracts\UserProviderInterface;

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

        $this->app->bind(
    UserProviderInterface::class,
    TestUserProvider::class
        );

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->string('profile_photo_path')->nullable();
            $table->timestamps();
        });

        Storage::fake('public');
        Config::set('avatar-manager.api_keys', value: ['test-api-key']);
    }

    public function test_avatar_upload_with_valid_api_key()
    {
        $this->app->bind(UserProviderInterface::class, function () {
            return new class implements UserProviderInterface {
                public function findByEmailHash(string $hash): ?object
                {
                    return null; // No existing user
                }
            };
        });

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

    public function test_avatar_upload_fails_if_user_has_existing_profile_photo()
    {
        // Füge eine Testimplementierung für den UserProvider hinzu
        $this->app->bind(UserProviderInterface::class, function () {
            return new class implements UserProviderInterface {
                public function findByEmailHash(string $hash)
                {
                    return (object)[
                        'profile_photo_path' => 'existing/path/to/photo.jpg'
                    ];
                }
            };
        });

        Storage::disk('public')->put('existing/path/to/photo.jpg', 'fakecontent');

        $response = $this->withHeaders([
            'X-API-KEY' => 'test-api-key',
        ])->postJson('/api/avatars/upload', [
            'email' => 'user@example.com',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $response->assertStatus(409);
        $response->assertJsonFragment(['message' => 'User already has a profile photo']);
    }

}
