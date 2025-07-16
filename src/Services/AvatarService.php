<?php 

namespace Mapo89\LaravelAvatarManager\Services;

use Illuminate\Support\Facades\Storage;

class AvatarService
{
    protected $disk;
    protected $extensions = ['jpg', 'jpeg', 'png', 'gif'];

    public function __construct()
    {
        $this->disk = Storage::disk('public');
    }

    /**
     * Find the avatar path by hash in avatars folder or return null.
     */
    public function findAvatarByHash(string $hash): ?string
    {
        $basePath = 'avatars/' . $hash;

        foreach ($this->extensions as $ext) {
            $fullPath = $basePath . '.' . $ext;
            if ($this->disk->exists($fullPath)) {
                return $fullPath;
            }
        }

        return null;
    }

    /**
     * Get full file path for the avatar in storage.
     */
    public function getAvatarFullPath(string $path): string
    {
        return $this->disk->path($path);
    }
}
