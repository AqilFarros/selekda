<?php

use App\Http\Controllers\api\AboutController;
use App\Http\Controllers\api\BannerController;
use App\Http\Controllers\api\BlogController;
use App\Http\Controllers\api\NewsController;
use App\Http\Controllers\api\ServiceController;
use App\Http\Controllers\api\UserController;
use App\Models\Achivement;
use App\Models\ImageGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/users', [UserController::class, 'getUsers'])->middleware(['auth:sanctum', 'admin']);

Route::middleware(['auth:sanctum', 'admin'])->resource('/banner', BannerController::class);
Route::middleware(['auth:sanctum', 'admin'])->resource('/about', AboutController::class);
Route::middleware(['auth:sanctum', 'admin'])->resource('/achivement', Achivement::class);
Route::middleware(['auth:sanctum', 'admin'])->resource('/blog', BlogController::class);
Route::middleware(['auth:sanctum', 'admin'])->resource('/image-gallery', ImageGallery::class);
Route::middleware(['auth:sanctum', 'admin'])->resource('/news', NewsController::class);
Route::middleware(['auth:sanctum', 'admin'])->resource('/service', ServiceController::class);
