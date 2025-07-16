<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Storage;
use Mapo89\LaravelAvatarManager\Services\AvatarService;
use Orchestra\Testbench\TestCase;

class AvatarServiceTest extends TestCase
{
    protected AvatarService $avatarService;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->avatarService = new AvatarService();
    }

    public function test_findAvatarByHash_returns_null_if_no_files_exist()
    {
        $result = $this->avatarService->findAvatarByHash('nonexistenthash');
        $this->assertNull($result);
    }

    public function test_findAvatarByHash_returns_correct_file_if_exists()
    {
        Storage::disk('public')->put('avatars/testhash.jpg', 'dummy content');

        $result = $this->avatarService->findAvatarByHash('testhash');
        $this->assertEquals('avatars/testhash.jpg', $result);
    }

    public function test_getAvatarFullPath_returns_correct_path()
    {
        Storage::disk('public')->put('avatars/testhash.jpg', 'dummy content');
        $path = $this->avatarService->getAvatarFullPath('avatars/testhash.jpg');

        $this->assertStringContainsString('avatars/testhash.jpg', $path);
        $this->assertFileExists($path);
    }
}
