<?php

namespace Mapo89\LaravelAvatarManager\Tests\Models;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class MediaUser extends User implements HasMedia
{
    use InteractsWithMedia;
}
