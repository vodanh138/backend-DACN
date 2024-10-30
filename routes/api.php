<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
Route::get('/not-authorized', [UserController::class, 'notLoggedIn'])->name('Not-Loggedin');
Route::post('/login', [UserController::class, 'loginProcessing'])->name('login');
Route::post('/register', [UserController::class, 'registerProcessing']);
Route::get('/post', [UserController::class, 'getpost']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [UserController::class, 'viewProfile']);
    
    Route::put('/profile', [UserController::class, 'editName']);
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
    Route::post('/upload/coverphoto', [UserController::class, 'uploadCoverphoto']);
    Route::post('/upload/avatar', [UserController::class, 'uploadAvatar']);
});