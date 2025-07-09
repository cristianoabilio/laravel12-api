<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BlogCategoryController;
use App\Http\Controllers\API\BlogPostController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\LikeController;
use App\Http\Controllers\API\StudentsApiController;
use App\Http\Controllers\API\TestApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// First API Route for testing

Route::get('/test', [TestApiController::class, 'test'])->name('test-api');

Route::apiResource('/students', StudentsApiController::class);

Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/comments/{post}', [CommentController::class, 'show'])->name('show');

Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    // Blog Categories
    Route::apiResource('/categories', BlogCategoryController::class)->middleware(['role:admin']);

    // Blog Posts
    Route::apiResource('/posts', BlogPostController::class)->middleware(['role:admin,author']);
    Route::post('blog-post-image/{post}', [BlogPostController::class, 'blogPostImage'])->name('blog-post-image')
        ->middleware(['role:admin,author']);

    Route::post('/post/react', [LikeController::class, 'react'])->name('react');

    Route::apiResource('/comments', CommentController::class);

    Route::get('/comments', [CommentController::class, 'index'])->name('index')->middleware(['role:admin']);

    Route::post('/comments/change-status/{id}', [CommentController::class, 'changeStatus'])->name('changeStatus')->middleware(['role:admin']);

    Route::get('/comments/{comment}', [CommentController::class, 'destroy'])->name('destroy')->middleware(['role:admin']);
});

Route::get('/posts', [BlogPostController::class, 'index']);
Route::get('/post/reactions/{post}', [LikeController::class, 'reactions'])->name('reactions');
