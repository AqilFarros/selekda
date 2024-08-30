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

Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [UserController::class, 'logout']);
    Route::post('/updateProfile', [UserController::class, 'updateProfile']);
});

Route::get('/users', [UserController::class, 'getUsers'])->middleware(['auth:sanctum', 'admin']);

Route::middleware(['auth:sanctum', 'admin'])->get('/get-users', [UserController::class]);
Route::middleware(['auth:sanctum', 'admin'])->resource('/banner', BannerController::class)->only(['store', 'update', 'delete']);
Route::resource('/banner', BannerController::class)->only(['index', 'show']);
Route::middleware(['auth:sanctum', 'admin'])->resource('/about', AboutController::class)->only(['store', 'update', 'delete']);
Route::resource('/about', AboutController::class)->only(['index', 'show']);
Route::middleware(['auth:sanctum', 'admin'])->resource('/achivement', Achivement::class)->only(['store', 'update', 'delete']);
Route::resource('/achivement', Achivement::class)->only(['index', 'show']);
Route::middleware(['auth:sanctum', 'admin'])->resource('/blog', BlogController::class)->only(['store', 'update', 'delete']);
Route::resource('/blog', BlogController::class)->only(['index', 'show']);
Route::middleware(['auth:sanctum', 'admin'])->resource('/image-gallery', ImageGallery::class)->only(['store', 'update', 'delete']);
Route::resource('/image-gallery', ImageGallery::class)->only(['index', 'show']);
Route::middleware(['auth:sanctum', 'admin'])->resource('/news', NewsController::class)->only(['store', 'update', 'delete']);
Route::resource('/news', NewsController::class)->only(['index', 'show']);
Route::middleware(['auth:sanctum', 'admin'])->resource('/service', ServiceController::class)->only(['store', 'update', 'delete']);
Route::resource('/service', ServiceController::class)->only(['index', 'show']);
