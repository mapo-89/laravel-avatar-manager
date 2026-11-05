<?php

namespace Mapo89\LaravelAvatarManager\Services;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class AvatarService
{
    protected FilesystemAdapter $disk;

    protected array $extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    public function __construct()
    {
        $this->disk = Storage::disk(config('avatar-manager.disk', 'public'));
    }

    public function findAvatarByHash(string $hash): ?string
    {
        if (! preg_match('/\A[a-f0-9]{32}\z/i', $hash)) {
            return null;
        }

        foreach ($this->extensions as $ext) {
            $path = 'avatars/'.strtolower($hash).'.'.$ext;

            if ($this->disk->exists($path)) {
                return $path;
            }
        }

        return null;
    }

    public function store(string $email, UploadedFile $file): string
    {
        $hash = md5(strtolower(trim($email)));
        $extension = match ($file->getMimeType()) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            default => throw new \InvalidArgumentException('Unsupported avatar image type.'),
        };
        $path = 'avatars/'.$hash.'.'.$extension;

        if ($this->disk->putFileAs('avatars', $file, $hash.'.'.$extension) === false) {
            throw new RuntimeException('Unable to store avatar.');
        }

        // An older extension must not shadow the newly uploaded image.
        foreach ($this->extensions as $ext) {
            $previous = 'avatars/'.$hash.'.'.$ext;

            if ($previous !== $path) {
                $this->disk->delete($previous);
            }
        }

        return $path;
    }

    public function getAvatarFullPath(string $path): string
    {
        return $this->disk->path($path);
    }
}
