<?php

namespace Mapo89\LaravelAvatarManager\Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mapo89\LaravelAvatarManager\Tests\Models\MediaUser;
use Mapo89\LaravelAvatarManager\Tests\TestCase;

class MediaLibraryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['avatar-manager.user_model' => MediaUser::class]);
        $migration = require __DIR__.'/../../vendor/spatie/laravel-medialibrary/database/migrations/create_media_table.php.stub';
        $migration->up();
        config(['filesystems.disks.media' => ['driver' => 'local', 'root' => storage_path('app/media')]]);
        Storage::fake('media');
    }

    public function test_core_panel_media_is_served_from_its_own_disk(): void
    {
        $user = MediaUser::create(['email' => 'user@example.com']);
        $file = UploadedFile::fake()->image('profile.png');
        $contents = file_get_contents($file->getRealPath());
        $user->addMedia($file)->toMediaCollection('avatars', 'media');
        Storage::disk('public')->put('avatars/'.md5($user->email).'.jpg', 'old-api-upload');

        $response = $this->get('/avatar/'.md5($user->email))->assertOk()->assertHeader('content-type', 'image/png');
        $this->assertSame($contents, $response->streamedContent());
    }

    public function test_api_does_not_override_core_panel_media(): void
    {
        $user = MediaUser::create(['email' => 'user@example.com']);
        $user->addMedia(UploadedFile::fake()->image('profile.png'))->toMediaCollection('avatars', 'media');

        $this->withHeader('X-API-KEY', 'test-api-key')->postJson('/api/avatars/upload', [
            'email' => $user->email, 'avatar' => UploadedFile::fake()->image('replacement.jpg'),
        ])->assertStatus(409);

        Storage::disk('public')->assertMissing('avatars/'.md5($user->email).'.jpg');
    }

    public function test_configured_media_collection_is_used(): void
    {
        config(['avatar-manager.media_collection' => 'profile']);
        $user = MediaUser::create(['email' => 'user@example.com']);
        $file = UploadedFile::fake()->image('profile.jpg');
        $contents = file_get_contents($file->getRealPath());
        $user->addMedia($file)->toMediaCollection('profile', 'media');

        $this->assertSame($contents, $this->get('/avatar/'.md5($user->email))->assertOk()->streamedContent());
    }
}
