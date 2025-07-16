<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Mapo89\LaravelAvatarManager\AvatarManagerServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Mapo89\LaravelAvatarManager\Contracts\UserProviderInterface;
use Mapo89\LaravelAvatarManager\Tests\Services\TestUserProvider;
use Orchestra\Testbench\TestCase as TestbenchTestCase;

class AvatarControllerTest extends TestbenchTestCase
{
    use RefreshDatabase;

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

        // Dummy files for avatar
        Storage::fake('public');
        Storage::disk('public')->put('avatars/demo.png', 'dummy-avatar-content');

        // Default-Avatar anlegen
        File::ensureDirectoryExists(public_path('vendor/avatar-manager'));
        File::put(public_path('vendor/avatar-manager/default.png'), 'fallback-content');
    }

    public function test_show_returns_avatar_file_if_exists_in_avatars_folder()
    {
        // Generiere Fake-Bild
        $file = UploadedFile::fake()->image('testavatar.jpg');

        // Kopiere Bild in „avatars/“-Ordner
        Storage::disk('public')->put('avatars/testhash.jpg', file_get_contents($file->getRealPath()));

        $response = $this->get('/avatar/testhash');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'image/jpeg');
    }

    public function test_show_returns_user_profile_photo_if_no_avatar_found()
    {
        $fakeUser = (object) [
            'profile_photo_path' => 'profile_photos/userphoto.jpg',
        ];

         // Generiere Fake-Bild
        $file = UploadedFile::fake()->image('userphoto.jpg');
        Storage::disk('public')->put('profile_photos/userphoto.jpg', file_get_contents($file->getRealPath()));

        $this->app->instance(UserProviderInterface::class, new class($fakeUser) implements UserProviderInterface {
            private $user;
            public function __construct($user) { $this->user = $user; }
            public function findByEmailHash($hash) { return $this->user; }
        });

        $response = $this->get('/avatar/testhash');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'image/jpeg');
    }

    public function test_show_returns_default_avatar_if_none_found()
    {
        $response = $this->get('/avatar/nonexistenthash');

        $response->assertStatus(200); // Fallback-
        $actual = file_get_contents($response->getFile()->getPathname());
        $this->assertEquals('fallback-content', $actual);
    }
}
