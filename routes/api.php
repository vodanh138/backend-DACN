<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
Route::get('/not-authorized', [UserController::class, 'notLoggedIn'])->name('Not-Loggedin');
Route::post('/login', [UserController::class, 'loginProcessing'])->name('login');
Route::post('/register', [UserController::class, 'registerProcessing']);
Route::post('/post', [UserController::class, 'uppost']);//Create Post
Route::middleware('auth:sanctum')->group(function () {
    //Post
    Route::get('/post', [UserController::class, 'getpost']);//View Post
    

    //Profile
    Route::get('/profile', [UserController::class, 'viewProfile']);//View profile
    Route::put('/profile', [UserController::class, 'editName']);//Edit name
    Route::post('/upload/coverphoto', [UserController::class, 'uploadCoverphoto']);//Edit Coverphoto
    Route::post('/upload/avatar', [UserController::class, 'uploadAvatar']);//Edit Avatar

    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
});