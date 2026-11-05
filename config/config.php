<?php

return [
    // Published overrides are optional; the bundled image is the final fallback.
    'default_avatar' => 'vendor/avatar-manager/default.png',

    'api_keys' => [env('AVATAR_API_KEY')],

    // API uploads remain separate from managed CorePanel/Jetstream profile images.
    'disk' => 'public',
    'user_model' => null, // Defaults to auth.providers.users.model.
    'profile_photo_disk' => null, // Defaults to jetstream.profile_photo_disk or public.
    'media_collection' => 'avatars',
];
