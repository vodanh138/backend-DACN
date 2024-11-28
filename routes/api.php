<?php

use App\Http\Controllers\Likecontroller;
use App\Http\Controllers\Postcontroller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
Route::get('/not-authorized', [UserController::class, 'notLoggedIn'])->name('Not-Loggedin');
Route::post('/login', [UserController::class, 'loginProcessing'])->name('login');
Route::post('/register', [UserController::class, 'registerProcessing']);

Route::middleware('auth:sanctum')->group(function () {
    //Post
    Route::get('/post', [Postcontroller::class, 'getPost']);//View Post
    Route::post('/post', [PostController::class, 'upPost']);//Create Post

    //Like
    Route::post('/post/{post_id}/like', [Likecontroller::class, 'postLike']);//Like
    Route::delete('/post/{post_id}/like', [LikeController::class, 'postUnlike']);//Unlike

    Route::post('/comment/{comment_id}/like', [Likecontroller::class, 'commentLike']);//Like
    Route::delete('/comment/{comment_id}/like', [LikeController::class, 'commentUnlike']);//Unlike

    //Profile
    Route::get('/profile', [UserController::class, 'viewProfile']);//View profile
    Route::put('/profile', [UserController::class, 'editName']);//Edit name
    Route::post('/upload/coverphoto', [UserController::class, 'uploadCoverphoto']);//Edit Coverphoto
    Route::post('/upload/avatar', [UserController::class, 'uploadAvatar']);//Edit Avatar

    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
});