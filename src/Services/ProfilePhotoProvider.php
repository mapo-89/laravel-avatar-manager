<?php

namespace Mapo89\LaravelAvatarManager\Services;

use Illuminate\Support\Facades\Storage;
use Mapo89\LaravelAvatarManager\Contracts\ProfilePhotoProviderInterface;

class ProfilePhotoProvider implements ProfilePhotoProviderInterface
{
    public function findForUser(object $user): ?array
    {
        // CorePanel stores profile images in Spatie Media Library's avatars collection.
        if (method_exists($user, 'getFirstMedia')) {
            $media = $user->getFirstMedia(config('avatar-manager.media_collection', 'avatars'));

            if ($media !== null) {
                $photo = [
                    'disk' => (string) $media->disk,
                    'path' => $media->getPathRelativeToRoot(),
                ];

                if (Storage::disk($photo['disk'])->exists($photo['path'])) {
                    return $photo;
                }
            }
        }

        // Keep existing Jetstream applications working without a media dependency.
        $path = $user->profile_photo_path ?? null;
        $disk = config('avatar-manager.profile_photo_disk')
            ?? config('jetstream.profile_photo_disk', 'public');

        if (is_string($path) && $path !== '' && Storage::disk($disk)->exists($path)) {
            return ['disk' => $disk, 'path' => $path];
        }

        return null;
    }
}
