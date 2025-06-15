<?php

namespace Mapo89\LaravelAvatarManager\Tests\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $guarded = [];
    protected $table = 'users';
}
