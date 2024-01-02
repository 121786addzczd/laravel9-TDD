<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\Mypage\UserLoginController;
use App\Http\Controllers\Mypage\PostManageController;

Route::get('', [PostController::class, 'index']);
Route::get('posts/{post}', [PostController::class, 'show'])
    ->name('posts.show')
    ->whereNumber('post');

Route::get('signup', [SignupController::class, 'index']);
Route::post('signup', [SignupController::class, 'store']);

Route::get('mypage/login', [UserLoginController::class, 'index'])->name('login');
Route::post('mypage/login', [UserLoginController::class, 'login']);

Route::middleware('auth')->group(function () {
    Route::post('mypage/logout', [UserLoginController::class, 'logout'])->name('logout');
    Route::get('mypage/posts', [PostManageController::class, 'index'])->name('mypage.posts');
});
