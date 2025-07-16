<?php

return [
    /**
     * default avatar image path
     * This image will be used when no avatar is set for a user.
     * You can change this to any image you want, just make sure the path is correct.
     */
    'default_avatar' => 'vendor/avatar-manager/default.png',

    /**
     * API keys for authentication
     * You can add multiple API keys here to allow access to the avatar manager API.
     * Make sure to keep these keys secret.
     */
    'api_keys' => [
        // 'your_api_key_here',
         env('AVATAR_API_KEY'),
    ],
];