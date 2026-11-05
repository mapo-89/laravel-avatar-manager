<?php

use Illuminate\Support\Facades\Route;
use Mapo89\LaravelAvatarManager\Http\Controllers\AvatarController;

Route::get('avatar/{hash}', [AvatarController::class, 'show'])
    ->where('hash', '[a-fA-F0-9]{32}')
    ->name('avatar.show');
