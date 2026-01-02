<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\HashtagController;
use Illuminate\Support\Facades\Route;
use App\Models\User;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public blog routes
Route::get('/blogs', [BlogController::class, 'index']);
Route::get('/blogs/{blog}', [BlogController::class, 'show']);

// Public hashtag routes
Route::get('/hashtags/popular', [HashtagController::class, 'popular']);
Route::get('/hashtags', [HashtagController::class, 'index']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Blog routes
    Route::post('/blogs', [BlogController::class, 'store']);
    Route::put('/blogs/{blog}', [BlogController::class, 'update']);
    Route::delete('/blogs/{blog}', [BlogController::class, 'destroy']);
    Route::post('/blogs/{blog}/like', [BlogController::class, 'like']);
    Route::post('/blogs/{blog}/unlike', [BlogController::class, 'unlike']);

    // Bookmark routes
    Route::get('/bookmarks', [BookmarkController::class, 'index']);
    Route::post('/blogs/{blog}/bookmark', [BookmarkController::class, 'store']);
    Route::delete('/blogs/{blog}/bookmark', [BookmarkController::class, 'destroy']);

    Route::post('/users/{user}/follow', function (User $user) {
        auth()->user()->following()->syncWithoutDetaching($user->id);
    });

    Route::delete('/users/{user}/follow', function (User $user) {
        auth()->user()->following()->detach($user->id);
    });

});
