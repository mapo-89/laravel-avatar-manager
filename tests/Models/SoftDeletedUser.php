<?php

namespace Mapo89\LaravelAvatarManager\Tests\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class SoftDeletedUser extends User
{
    use SoftDeletes;
}
