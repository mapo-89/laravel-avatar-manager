<?php

namespace Mapo89\LaravelAvatarManager\Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mapo89\LaravelAvatarManager\Tests\Models\User;
use Mapo89\LaravelAvatarManager\Tests\TestCase;

class AvatarControllerTest extends TestCase
{
    private function avatarUrl(): string
    {
        return '/avatar/'.md5('user@example.com');
    }

    public function test_show_returns_avatar_file_if_exists_in_avatars_folder(): void
    {
        $file = UploadedFile::fake()->image('avatar.jpg');
        Storage::disk('public')->put('avatars/'.md5('user@example.com').'.jpg', file_get_contents($file->getRealPath()));

        $response = $this->get($this->avatarUrl());

        $response->assertOk()->assertHeader('content-type', 'image/jpeg');
        $this->assertSame(file_get_contents($file->getRealPath()), $response->streamedContent());
    }

    public function test_managed_photo_takes_precedence_over_stale_api_upload(): void
    {
        User::create(['email' => 'user@example.com', 'profile_photo_path' => 'profile-photos/current.jpg']);
        Storage::disk('public')->put('profile-photos/current.jpg', 'current-photo');
        Storage::disk('public')->put('avatars/'.md5('user@example.com').'.jpg', 'stale-api-photo');

        $this->assertSame('current-photo', $this->get($this->avatarUrl())->assertOk()->streamedContent());
    }

    public function test_show_returns_bundled_default_without_publishing_assets(): void
    {
        config(['avatar-manager.default_avatar' => 'missing-default.png']);

        $response = $this->get($this->avatarUrl())->assertOk()->assertHeader('content-type', 'image/png');

        $this->assertSame(realpath(__DIR__.'/../../resources/assets/default.png'), $response->getFile()->getPathname());
    }

    public function test_configured_default_is_used(): void
    {
        $path = public_path('avatar-manager-test-default.png');
        file_put_contents($path, file_get_contents(__DIR__.'/../../resources/assets/default.png'));
        config(['avatar-manager.default_avatar' => 'avatar-manager-test-default.png']);

        try {
            $this->assertSame($path, $this->get($this->avatarUrl())->assertOk()->getFile()->getPathname());
        } finally {
            unlink($path);
        }
    }

    public function test_uppercase_hash_is_supported(): void
    {
        Storage::disk('public')->put('avatars/'.md5('user@example.com').'.png', 'avatar');

        $this->assertSame('avatar', $this->get('/avatar/'.strtoupper(md5('user@example.com')))->assertOk()->streamedContent());
    }

    public function test_invalid_hashes_do_not_reach_avatar_lookup(): void
    {
        $this->get('/avatar/invalid-hash')->assertNotFound();
        $this->get('/avatar/'.str_repeat('z', 32))->assertNotFound();
        $this->get('/avatar/../secret')->assertNotFound();
    }

    public function test_configured_storage_disks_are_respected(): void
    {
        Storage::fake('avatars');
        Storage::fake('profiles');
        config(['avatar-manager.disk' => 'avatars', 'avatar-manager.profile_photo_disk' => 'profiles']);
        Storage::disk('avatars')->put('avatars/'.md5('user@example.com').'.png', 'api-image');
        $this->assertSame('api-image', $this->get($this->avatarUrl())->assertOk()->streamedContent());

        User::create(['email' => 'user@example.com', 'profile_photo_path' => 'profile.png']);
        Storage::disk('profiles')->put('profile.png', 'profile-image');
        $this->assertSame('profile-image', $this->get($this->avatarUrl())->assertOk()->streamedContent());
    }
}
