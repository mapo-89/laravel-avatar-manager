<?php

namespace Mapo89\LaravelAvatarManager\Tests\Unit;

use Mapo89\LaravelAvatarManager\Contracts\UserProviderInterface;
use Mapo89\LaravelAvatarManager\Tests\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Mapo89\LaravelAvatarManager\Tests\Services\TestUserProvider;
use Mapo89\LaravelAvatarManager\Tests\TestCase;

class AvatarRouteTest extends TestCase
{
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

        // Dummy files for avatar
        Storage::fake('public');
        Storage::disk('public')->put('avatars/demo.png', 'dummy-avatar-content');

        // Default-Avatar anlegen
        File::ensureDirectoryExists(public_path('vendor/avatar-manager'));
        File::put(public_path('vendor/avatar-manager/default.png'), 'fallback-content');
    }

    public function test_avatar_route_returns_user_avatar()
    {
        // Dummy user
        $user = User::create([
            'email' => 'demo@example.com',
            'profile_photo_path' => 'avatars/demo.png',
        ]);

        $hash = md5(strtolower(trim($user->email)));

        $response = $this->get("/avatar/{$hash}");

        $response->assertStatus(200);
        $actual = file_get_contents($response->getFile()->getPathname());
        $this->assertEquals('dummy-avatar-content', $actual);
    }

    public function test_avatar_route_returns_default_if_user_not_found()
    {
        // No User exists with this email
        $response = $this->get('/avatar/fakehash');

        $response->assertStatus(200); // Fallback-
        $actual = file_get_contents($response->getFile()->getPathname());
        $this->assertEquals('fallback-content', $actual);
    }
}
