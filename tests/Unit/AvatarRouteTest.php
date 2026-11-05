<?php

namespace Mapo89\LaravelAvatarManager\Tests\Unit;

use Illuminate\Support\Facades\Storage;
use Mapo89\LaravelAvatarManager\Services\UserProvider;
use Mapo89\LaravelAvatarManager\Tests\Models\SoftDeletedUser;
use Mapo89\LaravelAvatarManager\Tests\Models\User;
use Mapo89\LaravelAvatarManager\Tests\TestCase;

class AvatarRouteTest extends TestCase
{
    public function test_avatar_route_returns_user_avatar_with_normalized_email(): void
    {
        User::create(['email' => '  DEMO@example.com  ', 'profile_photo_path' => 'photo.png']);
        Storage::disk('public')->put('photo.png', 'profile-content');

        $this->assertSame('profile-content', $this->get('/avatar/'.md5('demo@example.com'))->assertOk()->streamedContent());
    }

    public function test_configured_model_scopes_are_respected(): void
    {
        config(['avatar-manager.user_model' => SoftDeletedUser::class]);
        $user = SoftDeletedUser::create(['email' => 'demo@example.com']);
        $user->delete();

        $this->assertNull((new UserProvider)->findByEmailHash(md5('demo@example.com')));
    }
}
