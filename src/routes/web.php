<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostListController;

Route::get('', [PostListController::class, 'index']);
