<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SignupController;

Route::get('', [PostController::class, 'index']);
Route::get('posts/{post}', [PostController::class, 'show'])
    ->name('posts.show')
    ->whereNumber('post');

Route::get('signup', [SignupController::class, 'index']);
Route::post('signup', [SignupController::class, 'store']);

