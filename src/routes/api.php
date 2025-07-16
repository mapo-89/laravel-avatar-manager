<?php

use Illuminate\Support\Facades\Route;
use Mapo89\LaravelAvatarManager\Http\Controllers\Api\UploadController;

Route::middleware(['api', 'avatar-manager.api_key'])->post('/avatars/upload', [ApiUploadController::class, 'upload']);
