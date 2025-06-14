<?php

use Illuminate\Support\Facades\Route;
use Mapo89\LaravelAvatarManager\Http\Controllers\AvatarController;


Route::prefix('avatar')->group(function () {
    Route::get('{hash}', [AvatarController::class, 'show'])->name('avatar.show');
});
