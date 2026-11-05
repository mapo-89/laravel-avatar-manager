<?php

namespace Mapo89\LaravelAvatarManager\Tests\Unit;

use Illuminate\Support\Facades\Storage;
use Mapo89\LaravelAvatarManager\Services\AvatarService;
use Mapo89\LaravelAvatarManager\Tests\TestCase;

class AvatarServiceTest extends TestCase
{
    public function test_find_avatar_by_hash_returns_correct_file(): void
    {
        $path = 'avatars/'.md5('demo@example.com').'.webp';
        Storage::disk('public')->put($path, 'dummy');
        $service = new AvatarService;

        $this->assertSame($path, $service->findAvatarByHash(md5('demo@example.com')));
        $this->assertFileExists($service->getAvatarFullPath($path));
    }

    public function test_invalid_and_unknown_hashes_return_null(): void
    {
        $service = new AvatarService;

        $this->assertNull($service->findAvatarByHash('../secret'));
        $this->assertNull($service->findAvatarByHash(md5('unknown@example.com')));
    }
}
